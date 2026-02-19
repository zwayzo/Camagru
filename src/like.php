<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$pdo = require '../config/database.php';
require "./send.php";

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to like an image.");
}

$user_id = $_SESSION['user_id'];
$image_id = $_POST['image_id'] ?? 0;

if ($image_id <= 0) {
    die("Invalid image.");
}

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user already liked this image
$stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND image_id = ?");
$stmt->execute([$user_id, $image_id]);
$likeExists = $stmt->fetch(PDO::FETCH_ASSOC);

if ($likeExists) {
    // User already liked → remove like (toggle off)
    $del = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND image_id = ?");
    $del->execute([$user_id, $image_id]);
} else {
    // Add like
    $insert = $pdo->prepare("INSERT INTO likes (user_id, image_id) VALUES (?, ?)");
    $insert->execute([$user_id, $image_id]);

    // Send email notification
    sendEmail($image_id, $pdo, "like", null);
}

// Redirect back to gallery
header("Location: gallerie.php");
exit();
?>
