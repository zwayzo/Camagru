<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Step 1<br>";

$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'camagru_db';

echo "Step 2<br>";

try {
    echo "Step 3 - before PDO<br>";

    $pdo = new PDO("mysql:host=$host;port=3306;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Step 4 - connected<br>";

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8 COLLATE utf8_general_ci");

    echo "Step 5 - DB created<br>";

    $pdo->exec("USE `$dbname`");

    echo "Step 6 - ussing DB<br>";
    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_verified TINYINT(1) DEFAULT 0,
        mail_token VARCHAR(64) DEFAULT NULL,
        reset_token VARCHAR(64) DEFAULT NULL,
        token_expiry DATETIME DEFAULT NULL,
        enable TINYINT(1) DEFAULT 1,
        created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // Images table
    $pdo->exec("CREATE TABLE IF NOT EXISTS images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // Likes table
    $pdo->exec("CREATE TABLE IF NOT EXISTS likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        image_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_like (user_id, image_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // Comments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        image_id INT NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    // STOP HERE FOR NOW
    // die("DEBUG STOP");
    echo "finish<br>";

} catch (PDOException $e) {
    die("❌ Setup failed: " . $e->getMessage());
}