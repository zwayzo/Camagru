<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$mysqli = require "../config/config.php";
require "./send.php";


if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to like an image.");
}

$user_id = $_SESSION['user_id'];
$image_id = $_POST['image_id'] ?? 0;

$stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();


if ($image_id <= 0) {
    die("Invalid image.");
}

// Check if user already liked this image
$stmt = $mysqli->prepare("SELECT id FROM likes WHERE user_id = ? AND image_id = ?");
$stmt->bind_param("ii", $user_id, $image_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // User already liked → remove like (toggle off)
    $stmt->close();
    $del = $mysqli->prepare("DELETE FROM likes WHERE user_id = ? AND image_id = ?");
    $del->bind_param("ii", $user_id, $image_id);
    $del->execute();
    $del->close();




} else {
    // Add like
    $stmt->close();
    $insert = $mysqli->prepare("INSERT INTO likes (user_id, image_id) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $image_id);
    $insert->execute();
    $insert->close();
    // var_dump($user['email']);
    // exit();
    sendEmail($image_id, $mysqli, "like", NULL);

}

// Redirect back to gallery (or use AJAX to update count dynamically)
header("Location: user_page.php");
exit();






?>