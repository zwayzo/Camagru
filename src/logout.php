<?php
session_start();


// var_dump($_SESSION['user_id']);
// var_dump($_SESSION['username']);

// exit();

if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    header("Location: ../public/index.php");
    exit();
}

// else {
// }


// Remove all session variables
$_SESSION = [];
session_destroy();
// Destroy the session


// Optional: delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
// unset($_SESSION['email_verified']);
// unset($_SESSION['register_error']);
// unset($_SESSION['login_error']);
// unset($_SESSION['username']);
// unset($_SESSION['user_id']);

// session_start();
// $_SESSION['succes'] = "You've been logged out";
// var_dump($_SESSION['succes']);
// exit();
// Redirect to login page
header("Location: ../public/index.php");
exit();
