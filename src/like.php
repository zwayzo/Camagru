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

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND image_id = ?");
$stmt->execute([$user_id, $image_id]);
$likeExists = $stmt->fetch(PDO::FETCH_ASSOC);

if ($likeExists) {
    $del = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND image_id = ?");
    $del->execute([$user_id, $image_id]);
} else {
    $insert = $pdo->prepare("INSERT INTO likes (user_id, image_id) VALUES (?, ?)");
    $insert->execute([$user_id, $image_id]);

    sendEmail($image_id, $pdo, "like", null);
}

header("Location: gallerie.php");
exit();
?>
