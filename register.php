<?php
// register.php - User Registration with Secure Password Hashing
require_once 'config/db.php'; // Database connection file (hum isko agle step mein banayenge)

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $message = "Email is already registered!";
            } else {
                // Secure Password Hashing (Assignment Requirement)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert User into Database using Prepared Statements
                $insert_stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                if ($insert_stmt->execute([$username, $email, $hashed_password])) {
                    $message = "Registration successful! You can now <a href='login.php' style='color: #f5c518;'>Login</a>.";
                } else {
                    $message = "Something went wrong. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — CineVerse: Multiverse Edition</title>
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
            <a href="login.php" style="color: #ffffff; text-decoration: none; margin-left: 1.5rem;">Login</a>
        </div>
    </navbar>

    <!-- Registration Form Box -->
    <div class="form-box">
        <h2>Create Account</h2>

        <?php if (!empty($message)): ?>
            <div style="background: rgba(229, 9, 20, 0.2); border: 1px solid #e50914; padding: 0.75rem; border-radius: 5px; margin-bottom: 1rem; font-size: 0.9rem; text-align: center;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        <p style="text-align: center; margin-top: 1rem; color: #9ca3af; font-size: 0.9rem;">
            Already have an account? <a href="login.php" style="color: #e50914; text-decoration: none;">Login here</a>
        </p>
    </div>

</body>
</html>