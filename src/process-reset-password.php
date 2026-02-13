<?php
// echo "<pre>";
// print_r($_POST);
// exit();



session_start();
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST["token"])) {
    $_SESSION["reset_error"] = "Invalid request.";
    header("Location: ../public/index.php");
    exit();
}



$token = $_POST["token"];

$token_hash = hash("sha256", $token);
$mysqli = require "../config/config.php";



$password = $_POST["password"];
$password_confirmation = $_POST["password_confirmation"];

if ($password !== $password_confirmation) {
    $_SESSION["reset_error"] = "Passwords do not match.";
    // die("Passwords do not match");
    header("Location: reset-password.php?token=" . urlencode($token));
    exit();
}

// var_dump($password);
// exit();


$sql = "SELECT * FROM users WHERE reset_token = ?";
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

# Password validation
if (!preg_match('/[@#$%!]/', $password)){
    // var_dump($password);
    // die();
    // exit();
    $_SESSION["reset_error"] = "Password must contain a special character.";
    header("Location: reset-password.php?token=" . urlencode($token));
    exit();
}

if (!preg_match('/[A-Z]/', $password)){
    // die("Passwords A");
    // exit();
    $_SESSION["reset_error"] = "Password must contain an uppercase letter.";
    header("Location: reset-password.php?token=" . urlencode($token));
    exit();
}

if (strlen($password) < 8){
    // die("Passwords 8");
    // exit();
    $_SESSION["reset_error"] = "Password must be at least 8 characters.";
    header("Location: reset-password.php?token=" . urlencode($token));
    exit();
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);


$sql = "UPDATE users
        SET password = ?,
            reset_token = NULL,
            token_expiry = NULL
        WHERE id = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("si", $password_hash, $user["id"]);
$stmt->execute();

$_SESSION["reset_success"] = "Password successfully reset.";
header("Location: ../public/index.php");
echo "passwor updated succefly";

exit();


?>