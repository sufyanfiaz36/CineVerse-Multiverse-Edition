-- Database Creation
CREATE DATABASE IF NOT EXISTS cineverse_db;
USE cineverse_db;

-- 1. Users Table (Authentication & Security)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Hashed passwords
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Phases Table (MCU Phases & Categories)
CREATE TABLE IF NOT EXISTS phases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phase_name VARCHAR(100) NOT NULL
);

-- Insert Sample MCU Phases
INSERT INTO phases (phase_name) VALUES 
('Phase 1: The Avengers Assemble'),
('Phase 2: Age of Ultron & Guardians'),
('Phase 3: Infinity Saga & Endgame'),
('Phase 4: Multiverse Saga Begins'),
('Phase 5: Multiverse Deep Dive'),
('Disney+ TV Series');

-- 3. Movies Table (MCU Core Data - CRUD)
CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    type ENUM('Movie', 'TV Series') DEFAULT 'Movie',
    phase_id INT,
    release_year INT,
    director VARCHAR(100),
    poster_url VARCHAR(255),
    description TEXT,
    FOREIGN KEY (phase_id) REFERENCES phases(id) ON DELETE SET NULL
);

-- Insert Sample MCU Movies & Series (Including Loki as requested!)
INSERT INTO movies (title, type, phase_id, release_year, director, poster_url, description) VALUES 
('Iron Man', 'Movie', 1, 2008, 'Jon Favreau', 'https://image.tmdb.org/t/p/w500/78lPtwv72eTNqFW9COBYI0dWDJa.jpg', 'After being held captive in an Afghan cave, billionaire engineer Tony Stark creates a unique armored suit to fight evil.'),
('The Avengers', 'Movie', 1, 2012, 'Joss Whedon', 'https://image.tmdb.org/t/p/w500/RYMX2wcKCBAr24UyPD7xwmjaTn.jpg', 'Earth''s mightiest heroes must come together and learn to fight as a team to stop Loki and his alien army.'),
('Loki - Season 1', 'TV Series', 6, 2021, 'Kate Herron', 'https://image.tmdb.org/t/p/w500/kEl2t3OhXc3Zb9FBh1AuYzRTgZp.jpg', 'The God of Mischief steps out of his brother''s shadow in a new series that takes place after Avengers: Endgame.');

-- 4. User Watchlist Table (User Personal CRUD Interaction)
CREATE TABLE IF NOT EXISTS user_watchlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    movie_id INT,
    status ENUM('Want to Watch', 'Watching', 'Completed') DEFAULT 'Want to Watch',
    user_rating INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);