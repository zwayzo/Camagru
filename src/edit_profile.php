<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = 'You must be logged in to edit your profile.';
    header('Location: ../index.php');
    exit;
}

$username = $_SESSION['username'];

$pdo = require '../config/database.php';

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    // user not found — avoid accessing $user as an array which causes warnings
    $_SESSION['error'] = 'User record not found.';
    header('Location: ../index.php');
    exit;
}

$new_username = trim($_POST['username'] ?? '');
$new_email = trim($_POST['email'] ?? '');
$new_password = trim($_POST['password'] ?? '');

try {
    if ($new_username !== '') {
        $update = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        $update->execute([$new_username, $user['id']]);
        // update session username so subsequent requests use the new one
        $_SESSION['username'] = $new_username;
    }

    if ($new_email !== '') {
        $update = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
        $update->execute([$new_email, $user['id']]);
    }

    if ($new_password !== '') {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$password, $user['id']]);
    }

    $_SESSION['succes'] = "Profile updated";
    header('Location: gallerie.php');
    exit();
} catch (Exception $e) {
    $_SESSION['error'] = 'Failed to update profile.';
    header('Location: gallerie.php');
    exit();
}
