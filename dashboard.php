<?php
require_once 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Handle Watched Status Toggle
if (isset($_GET['toggle_watched'])) {
    $mw_id = intval($_GET['toggle_watched']);
    $status = intval($_GET['status']);
    mysqli_query($conn, "UPDATE user_watchlist SET is_watched = '$status' WHERE user_id = '$user_id' AND movie_id = '$mw_id'");
    header("Location: dashboard.php");
    exit();
}

// Handle Review Submission
if (isset($_POST['submit_review'])) {
    $movie_id = intval($_POST['movie_id']);
    $rating = intval($_POST['rating']);
    $review_text = mysqli_real_escape_string($conn, $_POST['review_text']);

    mysqli_query($conn, "INSERT INTO movie_reviews (user_id, movie_id, rating, review_text) VALUES ('$user_id', '$movie_id', '$rating', '$review_text')");
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineVerse - My Watchlist</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: #121826;
            --text-color: #ffffff;
            --meta-color: #9ca3af;
            --nav-bg: #0b0f19;
            --border-color: #1f2937;
        }
        [data-theme="light"] {
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-color: #1f2937;
            --meta-color: #4b5563;
            --nav-bg: #ffffff;
            --border-color: #e5e7eb;
        }
        body { background-color: var(--bg-color); color: var(--text-color); transition: 0.3s; }
        .navbar { background-color: var(--nav-bg) !important; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; }
        .logo { font-size: 22px; font-weight: bold; color: #fff; white-space: nowrap; flex-shrink: 0; }
        [data-theme="light"] .logo { color: #000; }
        .logo span { color: #e50914; }
        .nav-left, .nav-right { display: flex; align-items: center; gap: 20px; }
        .nav-link { color: var(--meta-color); text-decoration: none; font-size: 14px; font-weight: 500; }
        .nav-link:hover, .nav-link.active { color: var(--text-color); }
        .movie-card { background: var(--card-bg) !important; border: 1px solid var(--border-color) !important; }
        .movie-title { color: var(--text-color) !important; }
        .movie-meta { color: var(--meta-color) !important; }
        .watched-badge { background: #10b981; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .unwatched-badge { background: #f59e0b; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .tag-badge { background: #8b5cf6; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; display: inline-block; margin-top: 5px; }
        .review-box { margin-top: 10px; font-size: 12px; background: rgba(100,100,100,0.1); padding: 8px; border-radius: 5px; }
    </style>
</head>
<body data-theme="dark">

    <!-- Navbar -->
    <div class="navbar">
        <div class="nav-left">
            <div class="logo">CINE<span>VERSE</span></div>
            <a href="index.php" class="nav-link">Home</a>
            <a href="dashboard.php" class="nav-link active">My Watchlist</a>
        </div>
        <div class="nav-right">
            <span style="color: var(--meta-color); font-size: 14px;">Welcome, <?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" style="background: #e50914; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px;">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h2 class="section-title">My Watchlist & Progress</h2>
        
        <div class="movie-grid">
            <?php
            $query = "SELECT m.*, w.is_watched, w.custom_tag FROM movies m 
                      JOIN user_watchlist w ON m.id = w.movie_id 
                      WHERE w.user_id = '$user_id'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $movie_id = $row['id'];
                    $title = isset($row['title']) ? $row['title'] : 'Unknown';
                    $year = isset($row['release_year']) ? $row['release_year'] : '2023';
                    $poster = isset($row['poster']) ? $row['poster'] : '';
                    $type = isset($row['type']) ? $row['type'] : 'Movie';
                    $phase = isset($row['phase']) ? $row['phase'] : 'MCU Phase';
                    $is_watched = $row['is_watched'];
                    $custom_tag = isset($row['custom_tag']) ? $row['custom_tag'] : 'Must Watch';
            ?>
                <!-- Movie Card -->
                <div class="movie-card">
                    <div class="movie-poster">
                        <img src="<?php echo htmlspecialchars($poster); ?>" alt="<?php echo htmlspecialchars($title); ?>" onerror="this.onerror=null; this.src='https://via.placeholder.com/300x450/1f2937/ffffff?text=<?php echo urlencode($title); ?>';">
                    </div>
                    <div class="movie-info">
                        <div class="movie-title"><?php echo htmlspecialchars($title); ?></div>
                        <div class="movie-meta"><?php echo htmlspecialchars($type); ?> &bull; <?php echo htmlspecialchars($year); ?></div>
                        <div>
                            <span class="tag-badge"><?php echo htmlspecialchars($custom_tag); ?></span>
                        </div>
                        <div style="margin: 8px 0;">
                            <?php if($is_watched == 1): ?>
                                <span class="watched-badge">Watched ✔️</span>
                                <a href="dashboard.php?toggle_watched=<?php echo $movie_id; ?>&status=0" style="font-size: 11px; color: var(--meta-color); margin-left: 5px;">Unwatch</a>
                            <?php else: ?>
                                <span class="unwatched-badge">Watchlist ⏳</span>
                                <a href="dashboard.php?toggle_watched=<?php echo $movie_id; ?>&status=1" style="font-size: 11px; color: #3498db; margin-left: 5px;">Mark Watched</a>
                            <?php endif; ?>
                        </div>

                        <!-- Review Form -->
                        <form action="dashboard.php" method="POST" class="review-box">
                            <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
                            <select name="rating" style="background: var(--card-bg); color: var(--text-color); border: 1px solid var(--border-color); padding: 3px; border-radius: 3px; font-size: 11px;">
                                <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                <option value="3">⭐⭐⭐ (3/5)</option>
                                <option value="2">⭐⭐ (2/5)</option>
                                <option value="1">⭐ (1/5)</option>
                            </select>
                            <input type="text" name="review_text" placeholder="Add review..." required style="width: 100%; margin-top: 5px; background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-color); padding: 5px; border-radius: 3px; font-size: 11px;">
                            <button type="submit" name="submit_review" style="margin-top: 5px; background: #e50914; color: white; border: none; padding: 3px 8px; border-radius: 3px; cursor: pointer; font-size: 11px;">Post Review</button>
                        </form>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<p style='color: var(--meta-color);'>Your watchlist is empty!</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>