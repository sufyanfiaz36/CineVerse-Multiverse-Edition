<?php
require_once 'config/db.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $message = "Incorrect password! Please try again.";
        }
    } else {
        $message = "This email is not registered!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineVerse - Login</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* Fallback inline styles to guarantee the card renders correctly */
        .auth-container {
            max-width: 400px;
            margin: 80px auto;
            background: #121826;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #1f2937;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; color: #9ca3af; margin-bottom: 5px; font-size: 14px; }
        .form-group input { width: 100%; padding: 10px; background: #0b0f19; border: 1px solid #1f2937; border-radius: 6px; color: #fff; box-sizing: border-box; }
        .btn-submit { width: 100%; background: #e50914; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #b20710; }
        .auth-links { text-align: center; margin-top: 15px; font-size: 13px; color: #9ca3af; }
        .auth-links a { color: #e50914; text-decoration: none; }
        .error-msg { color: #e50914; text-align: center; font-size: 13px; margin-bottom: 10px; }
        .success-msg { color: #2ecc71; text-align: center; font-size: 13px; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">CINE<span>VERSE</span></div>
        <div>
            <a href="index.php" style="color: #ffffff; text-decoration: none; margin-right: 15px;">Home</a>
            <a href="register.php" style="background: #e50914; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">Register</a>
        </div>
    </div>

    <div class="auth-container">
        <h2 style="color: #fff; margin-bottom: 20px; text-align: center; border-bottom: 2px solid #e50914; padding-bottom: 10px;">Welcome Back</h2>
        <?php 
        if(isset($_GET['success'])) { echo "<div class='success-msg'>Account created successfully! Please login.</div>"; }
        if(!empty($message)) { echo "<div class='error-msg'>$message</div>"; } 
        ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit" class="btn-submit">Login</button>
        </form>
        <div class="auth-links">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>

</body>
</html>