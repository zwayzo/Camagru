<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = require "../config/database.php";

if (!isset($_POST["imageData"])) {
    $_SESSION['error'] = 'No image received';
    header('Location: user_page.php');
    exit;
}

$data = $_POST["imageData"];
$data = str_replace("data:image/png;base64,", "", $data);
$data = str_replace(" ", "+", $data);
$imageData = base64_decode($data);

$filename = uniqid() . ".png";
$absDir = __DIR__ . '/../public/assets/img';
$absPath = $absDir . '/' . $filename;
$dbPath = '../public/assets/img/' . $filename; // path used in templates (relative from src/)

// Ensure directory exists and is writable
if (!is_dir($absDir)) {
    if (!mkdir($absDir, 0755, true) && !is_dir($absDir)) {
        $_SESSION['error'] = 'Failed to create image directory.';
        header('Location: user_page.php');
        exit;
    }
}

if (!is_writable($absDir)) {
    // try to set writable (may fail on some mounts)
    @chmod($absDir, 0755);
}

$written = @file_put_contents($absPath, $imageData);
if ($written === false) {
    $_SESSION['error'] = 'Failed to save image. Check folder permissions.';
    header('Location: user_page.php');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO images (user_id, image_path, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([
        $_SESSION["user_id"],
        $dbPath
    ]);
    $_SESSION['success'] = 'Image saved successfully.';
} catch (Exception $e) {
    // rollback file on DB error
    @unlink($absPath);
    $_SESSION['error'] = 'Failed to save image metadata.';
}

header('Location: user_page.php');
exit;


