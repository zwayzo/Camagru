<?php
session_start();

// var_dump("d");
// exit();



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mysqli = require "../config/config.php";

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to like an image.");
}

$user_id = $_SESSION['user_id'];


$stmt = $mysqli->prepare("SELECT * FROM users 
                        WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
// $stmt->store_result();
$stmt->close();


// var_dump($user[enable]);
// exit();

$new_enable = ($user['enable'] == 1) ? 0 : 1;

// update in DB
$stmt = $mysqli->prepare("UPDATE users SET enable = ? WHERE id = ?");
$stmt->bind_param("ii", $new_enable, $user_id);
$stmt->execute();
$stmt->close();

// optional: redirect back
header("Location: user_page.php");
exit();





?>