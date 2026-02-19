<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
$pdo = require"../config/database.php";


if (!isset($_POST["imageData"])) {
    die("No image received");
}

$data = $_POST["imageData"];

// Remove base64 header
// exit();
$data = str_replace("data:image/png;base64,", "", $data);
$data = str_replace(" ", "+", $data);

$imageData = base64_decode($data);

// Create unique filename
$filename = uniqid() . ".png";
$filepath = "../public/assets/img/" . $filename;

// Save file
file_put_contents($filepath, $imageData);

// Insert into database
$stmt = $pdo->prepare("INSERT INTO images (user_id, image_path, created_at)
                       VALUES (?, ?, NOW())");

$stmt->execute([
    $_SESSION["user_id"],
    $filepath
]);

echo "Image saved successfully!";

header("Location: user_page.php")
?>

