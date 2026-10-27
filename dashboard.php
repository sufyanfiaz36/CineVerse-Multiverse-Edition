<?php
session_start();
require_once 'config/db.php';

// Security check: Agar user login nahi hai toh login page par redirect kar do
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$success_msg = "";
$error_msg = "";

// 1. Handle Add to Watchlist (Create Operation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_watchlist'])) {
    $movie_id = $_POST['movie_id'];
    $status = $_POST['status'];

    // Check karo ke movie pehle se watchlist mein toh nahi hai
    $check_stmt = $pdo->prepare("SELECT * FROM user_watchlist WHERE user_id = ? AND movie_id = ?");
    $check_stmt->execute([$user_id, $movie_id]);
    
    if ($check_stmt->rowCount() > 0) {
        $error_msg = "This movie is already in your watchlist!";
    } else {
        $insert_stmt = $pdo->prepare("INSERT INTO user_watchlist (user_id, movie_id, status) VALUES (?, ?, ?)");
        if ($insert_stmt->execute([$user_id, $movie_id, $status])) {
            $success_msg = "Movie successfully added to your watchlist!";
        } else {
            $error_msg = "Failed to add movie. Please try again.";
        }
    }
}

// 2. Handle Remove from Watchlist (Delete Operation)
if (isset($_GET['delete_id'])) {
    $watchlist_id = $_GET['delete_id'];
    $delete_stmt = $pdo->prepare("DELETE FROM user_watchlist WHERE id = ? AND user_id = ?");
    $delete_stmt->execute([$watchlist_id, $user_id]);
    header("Location: dashboard.php");
    exit();
}

// 3. Fetch all available movies for the dropdown selection
$movies_stmt = $pdo->query("SELECT * FROM movies ORDER BY release_year ASC");
$all_movies = $movies_stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Fetch current user's watchlist with movie details (Read Operation)
$watchlist_stmt = $pdo->prepare("
    SELECT w.id as watchlist_id, m.title, m.type, m.release_year, m.director, m.poster_url, w.status 
    FROM user_watchlist w 
    JOIN movies m ON w.movie_id = m.id 
    WHERE w.user_id = ?
");
$watchlist_stmt->execute([$user_id]);
$my_watchlist = $watchlist_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CineVerse: Multiverse Edition</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="display: block; padding: 20px;">

    <!-- Navbar -->
    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(18, 24, 38, 0.9); padding: 15px 30px; border-radius: 10px; border: 1px solid rgba(229, 9, 20, 0.3); margin-bottom: 30px;">
        <h2 style="margin: 0; color: #ffffff;">CINE<span style="color: #e50914;">VERSE</span></h2>
        <div>
            <span style="color: #f5c518; margin-right: 20px; font-weight: bold;">Welcome, <?php echo htmlspecialchars($username); ?>!</span>
            <a href="index.php" style="color: #ffffff; text-decoration: none; margin-right: 15px;">Timeline</a>
            <a href="logout.php" style="background: #e50914; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold;">Logout</a>
        </div>
    </div>

    <!-- Main Container -->
    <div style="max-width: 1200px; margin: 0 auto;">

        <!-- Success / Error Messages -->
        <?php if (!empty($success_msg)): ?>
            <div style="background: rgba(46, 204, 113, 0.2); border: 1px solid #2ecc71; color: #2ecc71; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div style="background: rgba(229, 9, 20, 0.2); border: 1px solid #e50914; color: #ff6b6b; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <!-- Add Movie Form Section -->
        <div class="login-container" style="max-width: 100%; margin-bottom: 40px; box-sizing: border-box;">
            <h3 style="margin-top: 0; color: #ffffff; border-bottom: 2px solid #e50914; padding-bottom: 10px;">Add MCU Title to Your Watchlist</h3>
            <form action="dashboard.php" method="POST" style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-end;">
                <div style="flex: 2; min-width: 250px;">
                    <label style="display: block; margin-bottom: 8px; color: #9ca3af; font-size: 14px;">Select Movie / Series</label>
                    <select name="movie_id" class="form-control" required>
                        <option value="">-- Choose MCU Title --</option>
                        <?php foreach ($all_movies as $movie): ?>
                            <option value="<?php echo $movie['id']; ?>">
                                <?php echo htmlspecialchars($movie['title']); ?> (<?php echo $movie['release_year']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 8px; color: #9ca3af; font-size: 14px;">Tracking Status</label>
                    <select name="status" class="form-control" required>
                        <option value="Want to Watch">Want to Watch</option>
                        <option value="Watching">Watching</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>

                <div style="flex: 1; min-width: 150px;">
                    <button type="submit" name="add_watchlist" class="btn-primary" style="height: 45px;">Add to List</button>
                </div>
            </form>
        </div>

        <!-- Watchlist Grid Section -->
        <h3 style="color: #ffffff; border-bottom: 2px solid #374151; padding-bottom: 10px; margin-bottom: 20px;">My Personal Watchlist</h3>

        <?php if (empty($my_watchlist)): ?>
            <div style="text-align: center; padding: 40px; background: rgba(18, 24, 38, 0.5); border-radius: 10px; color: #9ca3af;">
                <p>Your watchlist is currently empty. Start adding your favorite MCU titles above!</p>
            </div>
        <?php else: ?>
            <div class="movie-grid">
                <?php foreach ($my_watchlist as $item): ?>
                    <div class="movie-card">
                        <!-- Poster Image -->
                        <img src="<?php echo !empty($item['poster_url']) ? htmlspecialchars($item['poster_url']) : 'assets/images/default-poster.jpg'; ?>" alt="Poster">
                        
                        <div class="movie-info">
                            <div class="movie-title"><?php echo htmlspecialchars($item['title']); ?></div>
                            <div class="movie-meta"><?php echo $item['type']; ?> &bull; <?php echo $item['release_year']; ?></div>
                            
                            <!-- Status Badge -->
                            <div style="margin: 10px 0;">
                                <span style="background: 
                                    <?php 
                                        if($item['status'] == 'Completed') echo 'rgba(46, 204, 113, 0.2); color: #2ecc71; border: 1px solid #2ecc71;';
                                        elseif($item['status'] == 'Watching') echo 'rgba(241, 196, 15, 0.2); color: #f1c40f; border: 1px solid #f1c40f;';
                                        else echo 'rgba(52, 152, 219, 0.2); color: #3498db; border: 1px solid #3498db;';
                                    ?> 
                                    padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; display: inline-block;">
                                    <?php echo $item['status']; ?>
                                </span>
                            </div>

                            <!-- Remove Button -->
                            <a href="dashboard.php?delete_id=<?php echo $item['watchlist_id']; ?>" 
                               onclick="return confirm('Are you sure you want to remove this title from your watchlist?');" 
                               style="display: block; text-align: center; background: rgba(229, 9, 20, 0.2); color: #ff6b6b; border: 1px solid #e50914; padding: 6px; border-radius: 5px; text-decoration: none; font-size: 13px; font-weight: bold; margin-top: 10px; transition: 0.3s;">
                                Remove
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>