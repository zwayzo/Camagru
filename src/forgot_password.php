<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST['username'];

    // generate a secure token
    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

    // DB connection
    $mysqli = require "../config/config.php";

    // 1️⃣ First, get the user's email from username
    $sql = "SELECT email FROM users WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // if (!$user) {
    //     // Optional: do not reveal if username exists for security
    //     echo "If this username exists, a reset link has been sent.";
    //     exit();
    // }

    $email = $user['email']; // now we have the email

    // 2️⃣ Update the reset token and expiry in DB
    $sql = "UPDATE users SET reset_token = ?, token_expiry = ? WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sss", $token_hash, $expiry, $username);
    $stmt->execute();

    // 3️⃣ Send email
    require __DIR__ . "/mailer.php"; // PHPMailer instance
    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END
Click <a href="http://localhost/camagru/src/reset-password.php?token=$token">here</a> to reset your password.
END;

    try {
        $mail->send();
    } catch(Exception $e) {
        // log error but don't reveal sensitive info to user
        error_log("Mailer error: {$mail->ErrorInfo}");
    }

    echo "If this username exists, a reset link has been sent.";
}
header("Location: ../public/index.php");

?>
