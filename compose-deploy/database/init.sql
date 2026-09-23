-- Use the database created by the MySQL container.
USE nkslab;

-- Create the application users table.
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the classroom demo user.
-- We store a SHA-256 hash instead of the plain-text password.
INSERT INTO users (username, password_hash)
VALUES ('admin', SHA2('admin@123', 256))
ON DUPLICATE KEY UPDATE username = VALUES(username);
