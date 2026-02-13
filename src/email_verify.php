<?php
// session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

function verify_email(){





    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? null;
    // $email = $_SESSION['email'] ?? null;
    $token = bin2hex(random_bytes(16));


    $token_hash = hash("sha256", $token);
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

    // var_dump($user_id);
    // exit();



    $mysqli = require "../config/config.php";


    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user){
        $_SESSION["identify_error"] = "The user isn't logged";
        sleep(2);
        header("Location: ../public/index.php");
    }

    $email = $user['email'];

    // var_dump($email);
    // exit();


    $sql = "UPDATE users SET mail_token = ?, token_expiry = ? WHERE email = ?"; 
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sss", $token_hash, $expiry, $email);
    $stmt->execute();

    //send mail

    require __DIR__ ."/mailer.php";
    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Verify your Email";
    $mail->Body = <<<END
    Click <a href="http://localhost/camagru/src/token_verify.php?token=$token">here</a> to verify your mail.
    END;

    try {
        $mail->send();
    } catch(Exception $e) {
        // log error but don't reveal sensitive info to user
        error_log("Mailer error: {$mail->ErrorInfo}");
    } 

    header("Location: ../public/index.php");

}



?>