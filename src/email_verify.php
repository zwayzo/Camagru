<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function verify_email() {
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? null;

    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

    $pdo = require '../config/database.php';

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION["identify_error"] = "The user isn't logged";
        sleep(2);
        header("Location: ../index.php");
        exit();
    }

    $email = $user['email'];

    $sql = "UPDATE users SET mail_token = ?, token_expiry = ? WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$token_hash, $expiry, $email]);

    require __DIR__ . "/mailer.php";
    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Verify your Email";
    $mail->Body = <<<END
Click <a href="http://localhost:8081/src/token_verify.php?token=$token">here</a> to verify your mail.
END;

    try {
        $mail->send();
    } catch(Exception $e) {
        error_log("Mailer error: {$mail->ErrorInfo}");
    }

    header("Location: ../index.php");
    exit();
}
?>
