<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CineVerse: Multiverse Edition</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="login-container">
        <div style="text-align: center;">
            <!-- Aap yahan apna Marvel logo image ya icon laga sakte hain -->
            <h2 style="margin-bottom: 5px;">CINE<span>VERSE</span></h2>
            <p style="color: #9ca3af; font-size: 13px; margin-top: 0;">Multiverse Edition</p>
        </div>

        <?php if(isset($error)): ?>
            <div style="background: rgba(229, 9, 20, 0.2); border: 1px solid #e50914; padding: 10px; border-radius: 5px; color: #ff6b6b; font-size: 14px; margin-bottom: 15px; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-primary" style="margin-top: 10px;">Login to Timeline</button>
        </form>

        <div class="auth-links">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>

</body>
</html>