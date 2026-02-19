<?php
session_start();
$pdo = require '../config/database.php';
require_once '../config/setup.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}

if (!isset($_POST['image_id'])) {
    die("Invalid request.");
}

$image_id = (int) $_POST['image_id'];
$user_id = $_SESSION['user_id'];

// First check if image belongs to this user
$stmt = $pdo->prepare("SELECT image_path FROM images WHERE id = ? AND user_id = ?");
$stmt->execute([$image_id, $user_id]);
$image = $stmt->fetch();

if (!$image) {
    die("You cannot delete this image.");
}

// Delete image file from server
if (file_exists($image['image_path'])) {
    unlink($image['image_path']);
}

// Delete from database
$stmt = $pdo->prepare("DELETE FROM images WHERE id = ?");
$stmt->execute([$image_id]);

// Redirect back
header("Location: gallerie.php");
exit();
