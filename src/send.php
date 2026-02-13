<?php
// session_start();


function sendEmail($image_id, $mysqli, $action, $text) {
    $sql = "SELECT images.*, users.email, users.enable
            FROM images
            JOIN users ON images.user_id = users.id
            WHERE images.id = ?
            ";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // var_dump($user['enable']);
    if ($user['enable'] == 0){
        header("Location: user_page.php");
        exit();
    }

    $current = $_SESSION['user_id'];


    $email = $user['email'];
   

    require __DIR__ ."/mailer.php";
    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Update on your image";

    if ($action == 'comment'){
        $mail->Body = <<<END
        Hi,<br>
        You have a new $action.<br>
        $current said: "$text".<br><br><br><br>
        Thanks,The Camagru Team<br><br>
        END;
    }
    else {
        $mail->Body = <<<END
        Hi,<br>
        You have a new $action from $current.<br><br><br>
        Thanks,The Camagru Team
        END;
    }


    

    try {
        $mail->send();
    } catch(Exception $e) {
        // log error but don't reveal sensitive info to user
        error_log("Mailer error: {$mail->ErrorInfo}");
    } 


    header("Location: user_page.php");
    exit();
}




?>