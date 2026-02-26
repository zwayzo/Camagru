<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "./send.php";
$pdo = require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to comment an image.");
}

$user_id = $_SESSION['user_id'];
$image_id = $_POST['image_id'];
$comment  = $_POST['comment'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($image_id <= 0) {
    die("Invalid image.");
}

$stmt = $pdo->prepare("INSERT INTO comments (user_id, image_id, comment, created_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$user_id, $image_id, $comment]);


// var_dump($user['username']);
// exit();


if ($user['enable'] == 1){
    sendEmail($image_id, $pdo, "comment", $comment);
}

header("Location: gallerie.php");

?>
