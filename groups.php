<?php
session_start();
require_once 'config/db.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if(isset($_GET['action']) && isset($_GET['group_id'])) {
    $groupId = $_GET['group_id'];
    
    if($_GET['action'] == 'join') {
        try {
            $stmt = $pdo->prepare("INSERT INTO group_members (user_id, group_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $groupId]);
            $_SESSION['success'] = "Successfully joined the group!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "You're already a member of this group";
        }
    } elseif($_GET['action'] == 'leave') {
        $stmt = $pdo->prepare("DELETE FROM group_members WHERE user_id = ? AND group_id = ?");
        $stmt->execute([$_SESSION['user_id'], $groupId]);
        $_SESSION['success'] = "You've left the group";
    }
    
    header('Location: groups.php');
    exit;
}

$groups = $pdo->query("
    SELECT sg.id, sg.name, sg.description, c.course_code, c.course_name,
    EXISTS (
        SELECT 1 FROM group_members 
        WHERE group_id = sg.id AND user_id = {$_SESSION['user_id']}
    ) as is_member
    FROM study_groups sg
    JOIN courses c ON sg.course_id = c.id
    ORDER BY c.course_code, sg.name
")->fetchAll();

$groupDetails = null;
$members = [];
if(isset($_GET['group_id'])) {
    $groupId = $_GET['group_id'];
    
    $stmt = $pdo->prepare("
        SELECT sg.*, c.course_code, c.course_name 
        FROM study_groups sg
        JOIN courses c ON sg.course_id = c.id
        WHERE sg.id = ?
    ");
    $stmt->execute([$groupId]);
    $groupDetails = $stmt->fetch();
    
    $stmt = $pdo->prepare("
        SELECT u.id, u.name 
        FROM group_members gm
        JOIN users u ON gm.user_id = u.id
        WHERE gm.group_id = ?
    ");
    $stmt->execute([$groupId]);
    $members = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($groupDetails) ? htmlspecialchars($groupDetails['name']) : 'Study Groups'; ?> - Study Group Finder</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <i class="fas fa-users"></i>
                <span>Study Group Finder</span>
            </div>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="groups.php"><i class="fas fa-book-open"></i> Study Groups</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>
    </header>
    <main>
        <div class="groups-container">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="success-message">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if($groupDetails): ?>
                <div class="group-details">
                    <h2><i class="fas fa-users"></i> <?php echo htmlspecialchars($groupDetails['name']); ?></h2>
                    <p class="course-info"><i class="fas fa-book"></i> <strong>Course:</strong> <?php echo htmlspecialchars($groupDetails['course_code'] . ' - ' . $groupDetails['course_name']); ?></p>
                    <p class="description"><?php echo htmlspecialchars($groupDetails['description']); ?></p>
                    
                    <div class="group-actions">
                        <?php 
                        $isMember = $pdo->query("
                            SELECT 1 FROM group_members 
                            WHERE group_id = $groupDetails[id] AND user_id = {$_SESSION['user_id']}
                        ")->fetch();
                        
                        if($isMember): ?>
                            <a href="groups.php?action=leave&group_id=<?php echo $groupDetails['id']; ?>" class="btn leave-btn">
                                <i class="fas fa-user-minus"></i> Leave Group
                            </a>
                        <?php else: ?>
                            <a href="groups.php?action=join&group_id=<?php echo $groupDetails['id']; ?>" class="btn join-btn">
                                <i class="fas fa-user-plus"></i> Join Group
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <h3><i class="fas fa-users"></i> Members</h3>
                    <?php if(count($members) > 0): ?>
                        <ul class="member-list">
                            <?php foreach($members as $member): ?>
                                <li><i class="fas fa-user"></i> <?php echo htmlspecialchars($member['name']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No members yet.</p>
                    <?php endif; ?>
                    
                    <a href="groups.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to all groups</a>
                </div>
            <?php else: ?>
                <h1><i class="fas fa-users"></i> Study Groups</h1>
                
                <div class="group-list">
                    <?php foreach($groups as $group): ?>
                        <div class="group-card <?php echo $group['is_member'] ? 'member' : ''; ?>">
                            <h3><?php echo htmlspecialchars($group['name']); ?></h3>
                            <p class="course"><i class="fas fa-book"></i> <?php echo htmlspecialchars($group['course_code'] . ' - ' . $group['course_name']); ?></p>
                            <p class="description"><?php echo htmlspecialchars($group['description']); ?></p>
                            
                            <div class="group-actions">
                                <?php if($group['is_member']): ?>
                                    <a href="groups.php?action=leave&group_id=<?php echo $group['id']; ?>" class="btn leave-btn">
                                        <i class="fas fa-user-minus"></i> Leave
                                    </a>
                                <?php else: ?>
                                    <a href="groups.php?action=join&group_id=<?php echo $group['id']; ?>" class="btn join-btn">
                                        <i class="fas fa-user-plus"></i> Join
                                    </a>
                                <?php endif; ?>
                                <a href="groups.php?group_id=<?php echo $group['id']; ?>" class="btn view-btn">
                                    <i class="fas fa-info-circle"></i> Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Study Group Finder</p>
        </div>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>