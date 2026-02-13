<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// var_dump($_POST);
// exit();

require './email_verify.php';
require_once '../config/config.php';

if (isset($_POST['register'])){
    // echo "test";
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password_no_hash = $_POST['password'];
    $index = 0;

    if (preg_match('/[@#$%!]/', $password_no_hash) == false){
        $_SESSION['register_error'] = "Password should contain at least one special character";
        $_SESSION['active_form'] = 'register';
        header("Location: ../public/index.php");
        exit();
    }

    if (preg_match('/[A-Z]/', $password_no_hash) == false){
        $_SESSION['register_error'] = "Password should contain at least one Uppercase letter";
        $_SESSION['active_form'] = 'register';
        header("Location: ../public/index.php");
        exit();
    }

    if (strlen($password_no_hash) < 8){
        $_SESSION['register_error'] = "Password should contain at least 8 character";
        $_SESSION['active_form'] = 'register';
        header("Location: ../public/index.php");
        exit();
    }
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    



    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0){
        $_SESSION['register_error'] = 'Email is already registred!';
        $_SESSION['active_form'] = 'register';
    } else {
        $conn->query("INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')");
    }
    $mysqli = require "../config/config.php";

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

 


    session_unset();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    verify_email();
    $_SESSION['VF'] = "You need to verify you mail\ncheck you're inbox";
    header("Location: ../public/index.php");
    exit();


}




if (isset($_POST['login'])){
    // echo "rrrrrrr\n\n";
    // sleep(3);
    $username = $_POST['username'];
    
    
    // echo($username);
    // echo($password);
    // sleep(2);
    $mysqli = require "../config/config.php";
    
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $username;
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])){
            if ($user['is_verified'] == 0){
        
                $_SESSION['VF'] = "You need to verify you mail\ncheck you're inbox";
                header("Location: ../public/index.php");
                exit();
            }
            // $_SESSION['password'] = $user['password'];
            $_SESSION['username'] = $user['username'];
            // var_dump($_SESSION['user_id']);
            // exit();
            header("Location: user_page.php");
            exit();
        }
    }
    $_SESSION['login_error'] = 'Incorrect username or password';
    $_SESSION['active_form'] = 'login';
    
    // var_dump($user['password']);
    
    header("Location: ../public/index.php");
    exit(); 
}





?>