<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$username = $_SESSION['username'];

// Connect via PDO
$pdo = require '../config/database.php';

// Get user by username
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);
$user = $stmt->fetch();

// New values from form
$new_username = trim($_POST['username'] ?? '');
$new_email = trim($_POST['email'] ?? '');
$new_password = trim($_POST['password'] ?? '');

// Update username
if ($new_username !== '') {
    $update = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    $update->execute([$new_username, $user['id']]);
}

// Update email
if ($new_email !== '') {
    $update = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
    $update->execute([$new_email, $user['id']]);
}

// Update password
if ($new_password !== '') {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->execute([$password, $user['id']]);
}

// Redirect back with success message
header("Location: user_page.php");
$_SESSION['succes'] = "Profile updated";
exit();
