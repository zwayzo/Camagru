<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET["token"])) {
    $_SESSION["reset_error"] = "Invalid request.";
    header("Location: ../index.php");
    exit();
}

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

$pdo = require '../config/database.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE mail_token = ?");
$stmt->execute([$token_hash]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user){
    $_SESSION["reset_error"] = "Invalid token.";
    header("Location: ../index.php");
    exit();
}

if (strtotime($user["token_expiry"]) <= time()){
    $_SESSION["reset_error"] = "Token expired.";
    header("Location: ../index.php");
    exit();
}

$update = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
$update->execute([$user['id']]);

$_SESSION['email_verified'] = "Email has been Verified";
header("Location: ../index.php");
exit();
?>
