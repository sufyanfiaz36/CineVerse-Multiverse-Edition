<?php
// dashboard.php - User Watchlist & CRUD Management
session_start();
require_once 'config/db.php';

// Check if user is logged in (Security requirement)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$message = "";

// 1. CREATE Operation: Add movie to personal watchlist
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_movie'])) {
    $movie_id = $_POST['movie_id'];
    $status = $_POST['status'];

    try {
        // Check if already in watchlist
        $check = $pdo->prepare("SELECT id FROM user_watchlist WHERE user_id = ? AND movie_id = ?");
        $check->execute([$user_id, $movie_id]);
        
        if ($check->rowCount() > 0) {
            $message = "This movie is already in your watchlist!";
        } else {
            $insert = $pdo->prepare("INSERT INTO user_watchlist (user_id, movie_id, status) VALUES (?, ?, ?)");
            $insert->execute([$user_id, $movie_id, $status]);
            $message = "Movie added to your watchlist successfully!";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// 2. DELETE Operation: Remove movie from watchlist
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $del_stmt = $pdo->prepare("DELETE FROM user_watchlist WHERE id = ? AND user_id = ?");
        $del_stmt->execute([$delete_id, $user_id]);
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        $message = "Error deleting record: " . $e->getMessage();
    }
}

// Fetch all available movies for the dropdown
$movies_stmt = $pdo->query("SELECT id, title FROM movies");
$all_movies = $movies_stmt->fetchAll();

// 3. READ Operation: Fetch user's watchlist items with movie details
$watchlist_stmt = $pdo->prepare("
    subqueries...
    SELECT uw.id as watchlist_id, m.title, m.release_year, m.poster_url, uw.status 
    FROM user_watchlist uw 
    JOIN movies m ON uw.movie_id = m.id 
    WHERE uw.user_id = ?
");
// Simplified query for fetching watchlist:
$watchlist_stmt = $pdo->prepare("
    SELECT uw.id as watchlist_id, m.title, m.release_year, m.poster_url, uw.status 
    FROM user_watchlist uw 
    JOIN movies m ON uw.movie_id = m.id 
    WHERE uw.user_id = ?
");
$watchlist_stmt->execute([$user_id]);
$watchlist_items = $watchlist_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard — CineVerse: Multiverse Edition</title>
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
            <a href="dashboard.php" style="color: #f5c518; text-decoration: none; margin-left: 1.5rem;">My Watchlist</a>
            <span style="color: #9ca3af; margin-left: 1.5rem;">| Welcome, <?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" style="background: #e50914; padding: 0.4rem 0.8rem; border-radius: 4px; color: white; text-decoration: none; margin-left: 1.5rem;">Logout</a>
        </div>
    </navbar>

    <div class="container">
        <h1>Your Personal Watchlist 🎬</h1>
        <p style="color: #9ca3af; margin-top: 0.5rem;">Manage your favorite MCU movies and series, track your progress, and curate your universe.</p>

        <?php if (!empty($message)): ?>
            <div style="background: rgba(229, 9, 20, 0.2); border: 1px solid #e50914; padding: 0.75rem; border-radius: 5px; margin: 1.5rem 0; text-align: center;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Add Movie Form (CREATE Operation) -->
        <div style="background-color: #161b26; padding: 1.5rem; border-radius: 8px; margin-top: 2rem; border-left: 4px solid #f5c518;">
            <h3>Add Movie to Watchlist</h3>
            <form action="dashboard.php" method="POST" style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
                <div style="flex: 2; min-width: 200px;">
                    <select name="movie_id" required style="width: 100%; padding: 0.75rem; background-color: #0b0f19; color: white; border: 1px solid #374151; border-radius: 5px;">
                        <option value="">-- Select MCU Movie / Series --</option>
                        <?php foreach ($all_movies as $mov): ?>
                            <option value="<?php echo $mov['id']; ?>"><?php echo htmlspecialchars($mov['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <select name="status" style="width: 100%; padding: 0.75rem; background-color: #0b0f19; color: white; border: 1px solid #374151; border-radius: 5px;">
                        <option value="Want to Watch">Want to Watch</option>
                        <option value="Watching">Watching</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div>
                    <button type="submit" name="add_movie" class="btn" style="padding: 0.75rem 1.5rem; background-color: #f5c518; color: black;">Add to List</button>
                </div>
            </form>
        </div>

        <!-- Watchlist Display Grid (READ & DELETE Operations) -->
        <h2 style="margin-top: 3rem;">Your Saved Titles</h2>
        
        <?php if (count($watchlist_items) > 0): ?>
            <div class="movie-grid">
                <?php foreach ($watchlist_items as $item): ?>
                    <div class="movie-card">
                        <img src="<?php echo htmlspecialchars($item['poster_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="movie-info">
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p>Released: <?php echo $item['release_year']; ?></p>
                            <p style="margin-top: 0.5rem; color: #f5c518;">Status: <b><?php echo $item['status']; ?></b></p>
                            
                            <!-- Delete Button with JS Confirmation -->
                            <a href="dashboard.php?delete_id=<?php echo $item['watchlist_id']; ?>" 
                               onclick="return confirm('Are you sure you want to remove this from your watchlist?');" 
                               style="display: inline-block; margin-top: 1rem; color: #e50914; text-decoration: none; font-size: 0.9rem;">
                                🗑️ Remove
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: #9ca3af; margin-top: 1rem;">Your watchlist is currently empty. Add your favorite Marvel movies above!</p>
        <?php endif; ?>

    </div>

</body>
</html>