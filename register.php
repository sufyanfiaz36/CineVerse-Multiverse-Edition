<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CineVerse: Multiverse Edition</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="login-container" style="max-width: 450px;">
        <div style="text-align: center;">
            <h2 style="margin-bottom: 5px;">JOIN THE <span>MULTIVERSE</span></h2>
            <p style="color: #9ca3af; font-size: 13px; margin-top: 0;">Create your MCU tracking account</p>
        </div>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Choose a username" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Create a secure password" required>
            </div>

            <button type="submit" class="btn-primary" style="margin-top: 10px;">Register Account</button>
        </form>

        <div class="auth-links">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

</body>
</html>