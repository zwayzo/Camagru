<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST["token"])) {
    $_SESSION["reset_error"] = "Invalid request.";
    header("Location: ../index.php");
    exit();
}

$token = $_POST["token"];
$token_hash = hash("sha256", $token);
$pdo = require '../config/database.php'; 

$password = $_POST["password"];
$password_confirmation = $_POST["password_confirmation"];

if ($password !== $password_confirmation) {
    $_SESSION["reset_error"] = "Passwords do not match.";
    header("Location: reset-password.php?token=" . urlencode($token));
    exit();
}


$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
$stmt->execute([$token_hash]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION["reset_error"] = "Invalid token.";
    header("Location: ../index.php");
    exit();
}

if (strtotime($user["token_expiry"]) <= time()) {
    $_SESSION["reset_error"] = "Token expired.";
    header("Location: ../index.php");
    exit();
}


if (!preg_match('/[@#$%!]/', $password)) {
    $_SESSION["reset_error"] = "Password must contain a special character.";
    header("Location: reset-password.php?token=" . urlencode($token));
    exit();
}

if (!preg_match('/[A-Z]/', $password)) {
    $_SESSION["reset_error"] = "Password must contain an uppercase letter.";
    header("Location: reset-password.php?token=" . urlencode($token));
    exit();
}

if (strlen($password) < 8) {
    $_SESSION["reset_error"] = "Password must be at least 8 characters.";
    header("Location: reset-password.php?token=" . urlencode($token));
    exit();
}


$password_hash = password_hash($password, PASSWORD_DEFAULT);


$stmt = $pdo->prepare("
    UPDATE users
    SET password = ?, reset_token = NULL, token_expiry = NULL
    WHERE id = ?
");
$stmt->execute([$password_hash, $user["id"]]);

$_SESSION["reset_success"] = "Password successfully reset.";
header("Location: ../index.php");
exit();
