<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = $_POST['username'];

    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

    $pdo = require '../config/database.php';

    $stmt = $pdo->prepare("SELECT email FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {

        header("Location: ../index.php");
        exit();
    }

    $email = $user['email']; 

    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE username = ?");
    $stmt->execute([$token_hash, $expiry, $username]);

    require __DIR__ . "/mailer.php"; 
    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END
Click <a href="http://localhost:8081/src/reset-password.php?token=$token">here</a> to reset your password.
END;
    
    try {
        $mail->send();
    } catch(Exception $e) {
        error_log("Mailer error: {$mail->ErrorInfo}");
    }

    // echo "If this username exists, a reset link has been sent.";
}

header("Location: ../index.php");
exit();
?>
