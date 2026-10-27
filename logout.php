<?php
session_start();

// Session variables ko unset aur destroy karna
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - CineVerse</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- 2 seconds ke baad automatically login page par redirect karne ke liye -->
    <meta http-equiv="refresh" content="2;url=login.php">
</head>
<body>

    <div class="login-container" style="text-align: center; max-width: 400px; padding: 50px 30px;">
        <!-- Animated Logo or Icon -->
        <div style="font-size: 40px; margin-bottom: 20px; animation: floatLogo 2s ease-in-out infinite;">
            🛡️
        </div>
        
        <h2 style="margin-bottom: 10px; color: #ffffff;">LOGGING OUT</h2>
        <p style="color: #9ca3af; font-size: 14px; margin-bottom: 25px;">
            Closing your session from the Multiverse timeline... Please wait.
        </p>

        <!-- Loading Spinner Animation -->
        <div style="display: flex; justify-content: center; align-items: center;">
            <div style="width: 35px; height: 35px; border: 3px solid rgba(229, 9, 20, 0.2); border-top: 3px solid #e50914; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        </div>
    </div>

    <!-- Spinner Keyframe Style -->
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
            
        }
    </style>

</body>
</html>