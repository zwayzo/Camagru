<?php
session_start();
$pdo = require '../config/database.php';
// require_once '../config/setup.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}

if (!isset($_POST['image_id'])) {
    die("Invalid request.");
}

$image_id = (int) $_POST['image_id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT image_path FROM images WHERE id = ? AND user_id = ?");
$stmt->execute([$image_id, $user_id]);
$image = $stmt->fetch();

if (!$image) {
    die("You cannot delete this image.");
}
if (file_exists($image['image_path'])) {
    unlink($image['image_path']);
}

$stmt = $pdo->prepare("DELETE FROM images WHERE id = ?");
$stmt->execute([$image_id]);

header("Location: gallerie.php");
exit();
