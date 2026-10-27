/* --- Movie Cards Hover & Grid Animations --- */
.movie-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 25px;
    padding: 20px 0;
}

.movie-card {
    background: #121826;
    border: 1px solid #1f2937;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    animation: fadeInCard 0.6s ease-out forwards;
    box-shadow: 0 4px 10px rgba(0,0,0,0.4);
}

/* Card Hover Lift & Red Glow Effect */
.movie-card:hover {
    transform: translateY(-8px) scale(1.02);
    border-color: #e50914;
    box-shadow: 0 12px 30px rgba(229, 9, 20, 0.3);
}

.movie-card img {
    width: 100%;
    height: 320px;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.movie-card:hover img {
    transform: scale(1.05);
}

.movie-info {
    padding: 15px;
}

.movie-title {
    font-size: 16px;
    font-weight: bold;
    color: #ffffff;
    margin-bottom: 5px;
}

.movie-meta {
    font-size: 13px;
    color: #9ca3af;
}

/* Keyframe for Movie Cards */
@keyframes fadeInCard {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}