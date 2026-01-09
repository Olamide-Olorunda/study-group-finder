<?php
session_start();
require_once 'config/db.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT sg.id, sg.name, c.course_code 
    FROM study_groups sg
    JOIN courses c ON sg.course_id = c.id
    JOIN group_members gm ON sg.id = gm.group_id
    WHERE gm.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$userGroups = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Study Group Finder</title>
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
        <section class="hero">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p>Connect with fellow students, join study groups, and excel in your courses together.</p>
        </section>

        <div class="dashboard">
            <section class="my-groups">
                <h2><i class="fas fa-users"></i> Your Study Groups</h2>
                <?php if(count($userGroups) > 0): ?>
                    <div class="group-list">
                        <?php foreach($userGroups as $group): ?>
                            <div class="group-card member">
                                <h3><?php echo htmlspecialchars($group['name']); ?></h3>
                                <p class="course"><i class="fas fa-book"></i> <?php echo htmlspecialchars($group['course_code']); ?></p>
                                <div class="group-actions">
                                    <a href="groups.php?group_id=<?php echo $group['id']; ?>" class="btn view-btn">
                                        <i class="fas fa-info-circle"></i> View Group
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-groups">
                        <p>You haven't joined any study groups yet.</p>
                        <a href="groups.php" class="btn">Browse Available Groups</a>
                    </div>
                <?php endif; ?>
            </section>
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