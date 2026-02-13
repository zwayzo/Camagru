<?php
session_start();


// ini_set('display_errors', 1);
// error_reporting(E_ALL);

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET["token"])) {
    $_SESSION["reset_error"] = "Invalid request.";
    header("Location: ../public/index.php");
    exit();
}

$token = $_GET["token"];

// var_dump($token);
// exit();

$token_hash = hash("sha256", $token);
$mysqli = require "../config/config.php";

$sql = "SELECT * FROM users WHERE mail_token = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user){
    $_SESSION["reset_error"] = "Invalid token.";
    header("Location: ../public/index.php");
    exit();
}

if (strtotime($user["token_expiry"]) <= time()){
    $_SESSION["reset_error"] = "Token expired.";
    header("Location: ../public/index.php");
    exit();
}

$update = $mysqli->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
$update->bind_param("i", $user['id']);
$update->execute();





$_SESSION['email_verified'] = "Email has been Verified";
header("Location: ../public/index.php");


exit();




?>