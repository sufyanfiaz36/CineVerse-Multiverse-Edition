<?php
require_once 'config/db.php';
session_start();

// Handle Add to Watchlist request with Custom Tag
if (isset($_GET['add_watchlist']) && isset($_SESSION['user_id'])) {
    $movie_id = intval($_GET['add_watchlist']);
    $user_id = $_SESSION['user_id'];
    $tag = isset($_GET['tag']) ? mysqli_real_escape_string($conn, $_GET['tag']) : 'Must Watch';

    $check = mysqli_query($conn, "SELECT * FROM user_watchlist WHERE user_id = '$user_id' AND movie_id = '$movie_id'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO user_watchlist (user_id, movie_id, is_watched, custom_tag) VALUES ('$user_id', '$movie_id', 0, '$tag')");
    }
    header("Location: index.php");
    exit();
}

// Handle Filtering & Search
$search_query = "";
$filter_phase = "";
$filter_type = "";
$where_clauses = [];

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = mysqli_real_escape_string($conn, trim($_GET['search']));
    $where_clauses[] = "(title LIKE '%$search_query%' OR phase LIKE '%$search_query%')";
}

if (isset($_GET['phase']) && !empty($_GET['phase'])) {
    $filter_phase = mysqli_real_escape_string($conn, $_GET['phase']);
    $where_clauses[] = "phase = '$filter_phase'";
}

if (isset($_GET['type']) && !empty($_GET['type'])) {
    $filter_type = mysqli_real_escape_string($conn, $_GET['type']);
    $where_clauses[] = "type = '$filter_type'";
}

$query = "SELECT * FROM movies";
if (count($where_clauses) > 0) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineVerse: Multiverse Edition</title>
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
        .search-container { text-align: center; margin: 20px 0; }
        .search-box, .filter-select { padding: 10px 18px; background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 25px; color: var(--text-color); font-size: 14px; outline: none; }
        .search-box { width: 100%; max-width: 400px; }
        .filters-bar { display: flex; justify-content: center; gap: 15px; margin-bottom: 30px; flex-wrap: wrap; }
        .theme-toggle-btn { background: none; border: 1px solid var(--border-color); color: var(--text-color); padding: 6px 12px; border-radius: 20px; cursor: pointer; font-size: 13px; }
        .nav-left, .nav-right { display: flex; align-items: center; gap: 20px; }
        .nav-link { color: var(--meta-color); text-decoration: none; font-size: 14px; font-weight: 500; }
        .nav-link:hover, .nav-link.active { color: var(--text-color); }
        .movie-card { background: var(--card-bg) !important; border: 1px solid var(--border-color) !important; cursor: pointer; transition: transform 0.2s; }
        .movie-card:hover { transform: translateY(-5px); }
        .movie-title { color: var(--text-color) !important; }
        .movie-meta { color: var(--meta-color) !important; }

        /* Modal Popup Styles */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); justify-content: center; align-items: center; }
        .modal-content { background: var(--card-bg); border: 1px solid var(--border-color); padding: 30px; border-radius: 12px; width: 90%; max-width: 500px; position: relative; color: var(--text-color); }
        .close-btn { position: absolute; top: 15px; right: 20px; font-size: 22px; cursor: pointer; color: var(--meta-color); }
        .close-btn:hover { color: #e50914; }
    </style>
</head>
<body data-theme="dark">

    <!-- Navbar -->
    <div class="navbar">
        <div class="nav-left">
            <div class="logo">CINE<span>VERSE</span></div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="index.php" class="nav-link active">Home</a>
                <a href="dashboard.php" class="nav-link">My Watchlist</a>
            <?php endif; ?>
        </div>
        <div class="nav-right">
            <button class="theme-toggle-btn" onclick="toggleTheme()">🌙 Theme</button>
            <?php if(isset($_SESSION['user_id'])): ?>
                <span style="color: var(--meta-color); font-size: 14px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" style="background: #e50914; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px;">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Login</a>
                <a href="register.php" style="background: #e50914; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h2 class="section-title">MCU Timeline & Watchlist Portal</h2>
        
        <div class="search-container">
            <form action="index.php" method="GET" class="filters-bar">
                <input type="text" name="search" class="search-box" placeholder="Search movies..." value="<?php echo htmlspecialchars($search_query); ?>">
                
                <select name="phase" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Phases</option>
                    <?php 
                    $p_res = mysqli_query($conn, "SELECT DISTINCT phase FROM movies");
                    while($p = mysqli_fetch_assoc($p_res)) {
                        $sel = ($filter_phase == $p['phase']) ? 'selected' : '';
                        echo "<option value='{$p['phase']}' $sel>{$p['phase']}</option>";
                    }
                    ?>
                </select>

                <select name="type" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="Movie" <?php if($filter_type=='Movie') echo 'selected'; ?>>Movie</option>
                    <option value="TV Series" <?php if($filter_type=='TV Series') echo 'selected'; ?>>TV Series</option>
                </select>
            </form>
        </div>

        <div class="movie-grid">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $movie_id = $row['id'];
                    $title = isset($row['title']) ? $row['title'] : 'Unknown';
                    $year = isset($row['release_year']) ? $row['release_year'] : '2023';
                    $poster = isset($row['poster']) ? $row['poster'] : '';
                    $type = isset($row['type']) ? $row['type'] : 'Movie';
                    $phase = isset($row['phase']) ? $row['phase'] : 'MCU Phase';
            ?>
                <!-- Movie Card -->
                <div class="movie-card" onclick="openModal('<?php echo addslashes($title); ?>', '<?php echo $year; ?>', '<?php echo addslashes($type); ?>', '<?php echo addslashes($phase); ?>', '<?php echo $poster; ?>', '<?php echo $movie_id; ?>')">
                    <div class="movie-poster">
                        <img src="<?php echo htmlspecialchars($poster); ?>" alt="<?php echo htmlspecialchars($title); ?>" onerror="this.onerror=null; this.src='https://via.placeholder.com/300x450/1f2937/ffffff?text=<?php echo urlencode($title); ?>';">
                    </div>
                    <div class="movie-info">
                        <div class="movie-title"><?php echo htmlspecialchars($title); ?></div>
                        <div class="movie-meta"><?php echo htmlspecialchars($type); ?> &bull; <?php echo htmlspecialchars($year); ?></div>
                        <span class="badge"><?php echo htmlspecialchars($phase); ?></span>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<p style='color: var(--meta-color); text-align: center; grid-column: 1 / -1;'>No movies found!</p>";
            }
            ?>
        </div>
    </div>

    <!-- Movie Details Modal Popup -->
    <div id="movieModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <div style="display: flex; gap: 20px; align-items: center;">
                <img id="modalPoster" src="" alt="" style="width: 120px; height: 180px; object-fit: cover; border-radius: 8px;">
                <div>
                    <h3 id="modalTitle" style="margin: 0 0 10px 0; color: var(--text-color);"></h3>
                    <p id="modalMeta" style="margin: 5px 0; font-size: 13px; color: var(--meta-color);"></p>
                    <p id="modalPhase" style="margin: 5px 0; font-size: 13px; color: #3498db;"></p>
                </div>
            </div>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <div style="margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                    <form id="watchlistForm" action="index.php" method="GET">
                        <input type="hidden" name="add_watchlist" id="modalMovieId">
                        <label style="font-size: 13px; display: block; margin-bottom: 5px;">Select Category Tag:</label>
                        <select name="tag" style="width: 100%; padding: 8px; background: var(--card-bg); color: var(--text-color); border: 1px solid var(--border-color); border-radius: 5px; margin-bottom: 10px;">
                            <option value="Must Watch">🔥 Must Watch</option>
                            <option value="Masterpiece">⭐ Masterpiece</option>
                            <option value="Favorite">❤️ Favorite</option>
                            <option value="Plan to Watch">⏳ Plan to Watch</option>
                        </select>
                        <button type="submit" style="width: 100%; background: #e50914; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; font-weight: bold;">+ Add to Watchlist with Tag</button>
                    </form>
                </div>
            <?php else: ?>
                <p style="margin-top: 20px; text-align: center; font-size: 13px; color: var(--meta-color);"><a href="login.php" style="color: #3498db;">Login</a> to add this movie to your watchlist.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function openModal(title, year, type, phase, poster, id) {
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalMeta').innerText = type + ' • ' + year;
            document.getElementById('modalPhase').innerText = phase;
            document.getElementById('modalPoster').src = poster;
            document.getElementById('modalMovieId').value = id;
            document.getElementById('movieModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('movieModal').style.display = 'none';
        }

        window.onclick = function(event) {
            let modal = document.getElementById('movieModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        function toggleTheme() {
            const body = document.body;
            if (body.getAttribute('data-theme') === 'dark') {
                body.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            }
        }
        if (localStorage.getItem('theme') === 'light') {
            document.body.setAttribute('data-theme', 'light');
        }
    </script>
</body>
</html>