<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Group Finder</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <i class="fas fa-users"></i>
                <span>Study Group Finder</span>
            </div>
            <div class="nav-links">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="groups.php"><i class="fas fa-book-open"></i> Study Groups</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="index.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>