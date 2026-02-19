<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require './email_verify.php';
$pdo = require '../config/database.php'; // PDO instance

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password_no_hash = $_POST['password'];

    // Password validations
    if (!preg_match('/[@#$%!]/', $password_no_hash)) {
        $_SESSION['register_error'] = "Password should contain at least one special character";
        $_SESSION['active_form'] = 'register';
        header("Location: ../public/index.php");
        exit();
    }

    if (!preg_match('/[A-Z]/', $password_no_hash)) {
        $_SESSION['register_error'] = "Password should contain at least one Uppercase letter";
        $_SESSION['active_form'] = 'register';
        header("Location: ../public/index.php");
        exit();
    }

    if (strlen($password_no_hash) < 8) {
        $_SESSION['register_error'] = "Password should contain at least 8 characters";
        $_SESSION['active_form'] = 'register';
        header("Location: ../public/index.php");
        exit();
    }

    $password = password_hash($password_no_hash, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
        header("Location: ../public/index.php");
        exit();
    }

    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $password]);

    // Fetch newly created user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Set session
    session_unset();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    // Send verification email
    verify_email();
    $_SESSION['VF'] = "You need to verify your email. Check your inbox.";
    header("Location: ../public/index.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user by username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        if ($user['is_verified'] == 0) {
            $_SESSION['VF'] = "You need to verify your email. Check your inbox.";
            header("Location: ../public/index.php");
            exit();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        header("Location: user_page.php");
        exit();
    }

    $_SESSION['login_error'] = 'Incorrect username or password';
    $_SESSION['active_form'] = 'login';
    header("Location: ../public/index.php");
    exit();
}
?>
