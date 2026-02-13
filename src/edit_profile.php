<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ALL);


$username = $_SESSION['username'];

// var_dump($username);
// exit();


$mysqli = require "../config/config.php";

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
// $id = $user['id'];

// var_dump($id);
// exit();

$new_username = trim($_POST['username'] ?? '');
$new_email = trim($_POST['email'] ?? '');
$new_password = trim($_POST['password'] ?? '');

// var_dump($new_email);
// var_dump($new_username);
// var_dump($new_password);
// exit();
if ($new_username !== '') {
    $update = $mysqli->prepare("UPDATE users SET username = ? WHERE id = ?");
    $update->bind_param("si", $new_username, $user['id']);
    $update->execute();

} 

if ($new_email !== '') {
    $update = $mysqli->prepare("UPDATE users SET email = ? WHERE id = ?");
    $update->bind_param("si", $new_email, $user['id']);
    $update->execute();

} 



if ($new_password !== '') {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $update = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->bind_param("si", $password, $user['id']);
    $update->execute();

} 


header("Location: user_page.php");
$_SESSION['succes'] = "Profile updated";
exit();





// var_dump($user);
// exit();






?>