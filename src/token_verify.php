<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET["token"])) {
    $_SESSION["reset_error"] = "Invalid request.";
    header("Location: ../public/index.php");
    exit();
}

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

// PDO connection
$pdo = require '../config/database.php';

// 1️⃣ Fetch the user by token
$stmt = $pdo->prepare("SELECT * FROM users WHERE mail_token = ?");
$stmt->execute([$token_hash]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user){
    $_SESSION["reset_error"] = "Invalid token.";
    header("Location: ../public/index.php");
    exit();
}

// 2️⃣ Check token expiry
if (strtotime($user["token_expiry"]) <= time()){
    $_SESSION["reset_error"] = "Token expired.";
    header("Location: ../public/index.php");
    exit();
}

// 3️⃣ Mark email as verified
$update = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
$update->execute([$user['id']]);

$_SESSION['email_verified'] = "Email has been Verified";
header("Location: ../public/index.php");
exit();
?>
