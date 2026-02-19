<?php
// database.php

$host = '127.0.0.1';
$db   = 'camagru_db';
$user = 'root';
$pass = 'root123';
$port = 3307; // your Docker-mapped port
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    return $pdo;
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
