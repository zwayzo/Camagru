<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST['username'];

    // generate a secure token
    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

    // DB connection using PDO
    $pdo = require '../config/database.php';

    // 1️⃣ First, get the user's email from username
    $stmt = $pdo->prepare("SELECT email FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Optional: do not reveal if username exists for security
        // We just silently continue
        header("Location: ../public/index.php");
        exit();
    }

    $email = $user['email']; // now we have the email

    // 2️⃣ Update the reset token and expiry in DB
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE username = ?");
    $stmt->execute([$token_hash, $expiry, $username]);

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

// Redirect back to login page
header("Location: ../public/index.php");
exit();
?>
