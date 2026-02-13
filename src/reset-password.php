<?php

require_once "../src/errors.php"; // adjust path as needed


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

$mysqli = require "../config/config.php";

$sql = "SELECT * FROM users WHERE reset_token = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();



if ($user == null){
    die("token not found");
}

if (strtotime($user["token_expiry"]) <= time()){
    die("token has expired");
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
        <?= showError($errors['reset_error']); ?>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">New password</label>
        <input type="password" name="password" id="password" required>

        <label for="password_confirmation">Repeat password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>

        <button type="submit">Send</button>
    </form>
</body>
</html>
