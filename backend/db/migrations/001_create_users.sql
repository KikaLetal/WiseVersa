CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    profile_picture VARCHAR(255) DEFAULT 'default.png',
    username VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user' NOT NULL
);