<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pdo = require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to like an image.");
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$new_enable = ($user['enable'] == 1) ? 0 : 1;

$stmt = $pdo->prepare("UPDATE users SET enable = ? WHERE id = ?");
$stmt->execute([$new_enable, $user_id]);
header("Location: gallerie.php");
exit();
?>
