<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require "./send.php";
$mysqli = require "../config/config.php";
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to comment an image.");
}

$user_id = $_SESSION['user_id'];
$image_id = $_POST['image_id'] ;
$comment  = $_POST['comment'];       

$stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($image_id <= 0) {
    die("Invalid image.");
}



$stmt = $mysqli->prepare("INSERT INTO comments (user_id, image_id, comment, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $user_id, $image_id, $comment);
$stmt->execute();
$result = $stmt->get_result();

// $image = $result->fetch_assoc();
$stmt->close();
if ($user['enable'] == 1){
    sendEmail($image_id, $mysqli, "comment", $comment);
}






?>