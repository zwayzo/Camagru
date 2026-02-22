<?php
function sendEmail($image_id, $pdo, $action, $text) {
    $sql = "SELECT images.*, users.email, users.enable
            FROM images
            JOIN users ON images.user_id = users.id
            WHERE images.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$image_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: gallerie.php");
        exit();
    }

    if ($user['enable'] == 0){
        header("Location: gallerie.php");
        exit();
    }

    $current = $_SESSION['user_id'];
    $email = $user['email'];
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$current]);
    $test = $stmt->fetchColumn();

    

    

    // Send email
    require __DIR__ . "/mailer.php";
    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Update on your image";

    if ($action == 'comment'){
        $mail->Body = <<<END
        Hi,<br>
        You have a new $action.<br>
        $current said: "$text".<br><br><br><br>
        Thanks, The Camagru Team<br><br>
        END;
    } else {
        $mail->Body = <<<END
        Hi,<br>
        You have a new $action from $test.<br><br><br>
        Thanks, The Camagru Team
        END;
    }

    try {
        $mail->send();
    } catch(Exception $e) {
        error_log("Mailer error: {$mail->ErrorInfo}");
    }

    header("Location: gallerie.php");
    exit();
}
?>
