<?php
// login.php - User Login with Secure Password Verification & Sessions
session_start();
require_once 'config/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "Please fill in all fields!";
    } else {
        try {
            // Fetch user from database using Prepared Statement
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Password is correct, start session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to Home / Dashboard
                header("Location: index.php");
                exit();
            } else {
                $message = "Invalid email or password!";
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CineVerse: Multiverse Edition</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Navigation Bar -->
    <navbar style="background-color: #161b26; border-bottom: 2px solid #e50914; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div class="logo" style="font-size: 1.5rem; font-weight: bold; color: #ffffff;">
            🔴 CineVerse: <span style="color: #f5c518;">Multiverse Edition</span>
        </div>
        <div class="nav-links">
            <a href="index.php" style="color: #ffffff; text-decoration: none; margin-left: 1.5rem;">Timeline</a>
            <a href="register.php" style="color: #ffffff; text-decoration: none; margin-left: 1.5rem;">Sign Up</a>
        </div>
    </navbar>

    <!-- Login Form Box -->
    <div class="form-box">
        <h2>User Login</h2>

        <?php if (!empty($message)): ?>
            <div style="background: rgba(229, 9, 20, 0.2); border: 1px solid #e50914; padding: 0.75rem; border-radius: 5px; margin-bottom: 1rem; font-size: 0.9rem; text-align: center;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p style="text-align: center; margin-top: 1rem; color: #9ca3af; font-size: 0.9rem;">
            Don't have an account? <a href="register.php" style="color: #e50914; text-decoration: none;">Register here</a>
        </p>
    </div>

</body>
</html>