<?php
session_start();
require_once 'config/db.php';

if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        header('Location: index.php?registered=1');
        exit;
    } catch (PDOException $e) {
        $error = "Email already exists";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Study Group Finder</title>
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
                <a href="index.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
            </div>
        </nav>
    </header>
    <main>
        <div class="register-container">
            <h2>Register</h2>
            <?php if(!empty($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                <button type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="index.php">Login here</a></p>
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