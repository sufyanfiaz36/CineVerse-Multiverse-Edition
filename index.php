<?php
// CineVerse: Multiverse Edition - Home Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineVerse: Multiverse Edition — MCU Timeline</title>
    <!-- CSS File Link -->
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
            <a href="dashboard.php" style="color: #ffffff; text-decoration: none; margin-left: 1.5rem;">My Watchlist</a>
            <a href="login.php" style="color: #ffffff; text-decoration: none; margin-left: 1.5rem;">Login</a>
            <a href="register.php" style="background: #e50914; padding: 0.5rem 1rem; border-radius: 4px; color: white; text-decoration: none; margin-left: 1.5rem;">Sign Up</a>
        </div>
    </navbar>

    <!-- Main Container -->
    <div class="container">
        <h1>Welcome to the Marvel Multiverse 🌌</h1>
        <p style="color: #9ca3af; margin-top: 0.5rem;">Explore the complete MCU timeline, movies, and TV series in one secure hub.</p>

        <!-- Movie Grid Display -->
        <div class="movie-grid">
            <div class="movie-card">
                <img src="https://image.tmdb.org/t/p/w500/78lPtwv72eTNqFW9COBYI0dWDJa.jpg" alt="Iron Man">
                <div class="movie-info">
                    <h3>Iron Man (2008)</h3>
                    <p>Phase 1 • Movie</p>
                </div>
            </div>

            <div class="movie-card">
                <img src="https://image.tmdb.org/t/p/w500/RYMX2wcKCBAr24UyPD7xwmjaTn.jpg" alt="The Avengers">
                <div class="movie-info">
                    <h3>The Avengers (2012)</h3>
                    <p>Phase 1 • Movie</p>
                </div>
            </div>

            <div class="movie-card">
                <img src="https://image.tmdb.org/t/p/w500/kEl2t3OhXc3Zb9FBh1AuYzRTgZp.jpg" alt="Loki">
                <div class="movie-info">
                    <h3>Loki - Season 1 (2021)</h3>
                    <p>Disney+ • TV Series</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>