<?php
require_once "../src/errors.php"; 

ini_set('display_errors', 1);
error_reporting(E_ALL);



if (!isset($_GET["token"])) {
    die("No token provided");
}

if (isset($_SESSION["reset_error"])) {
    echo "<p style='color:red'>" . $_SESSION["reset_error"] . "</p>";
    unset($_SESSION["reset_error"]);
}

$token = $_GET["token"];
$token_hash = hash("sha256", $token);


$pdo = require '../config/database.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
$stmt->execute([$token_hash]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user == null){
    die("Token not found");
}

if (strtotime($user["token_expiry"]) <= time()){
    die("Token has expired");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Reset Password</h1>

    <form method="post" action="process-reset-password.php">
        <?= showError($errors['reset_error'] ?? null); ?>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">New password</label>
        <input type="password" name="password" id="password" required>

        <label for="password_confirmation">Repeat password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>

        <button type="submit">Send</button>
    </form>
</body>
</html>
