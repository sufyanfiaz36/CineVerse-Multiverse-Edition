<?php
require_once 'config/db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_query = "SELECT * FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "This email is already registered!";
    } else {
        $insert_query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if (mysqli_query($conn, $insert_query)) {
            header("Location: login.php?success=1");
            exit();
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineVerse - Register</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <div class="navbar">
        <div class="logo">CINE<span>VERSE</span></div>
        <div>
            <a href="index.php" style="color: #ffffff; text-decoration: none; margin-right: 15px;">Home</a>
            <a href="login.php" style="color: #ffffff; text-decoration: none;">Login</a>
        </div>
    </div>

    <div class="auth-container">
        <h2>Create Account</h2>
        <?php if(!empty($message)) { echo "<div class='error-msg'>$message</div>"; } ?>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Choose a username">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Create a secure password">
            </div>
            <button type="submit" class="btn-submit">Register</button>
        </form>
        <div class="auth-links">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

</body>
</html>