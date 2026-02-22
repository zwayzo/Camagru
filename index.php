<?php
// session_start();
require_once "src/errors.php"; 

$successMessage = $_SESSION['succes'] ?? $_SESSION['email_verified'] ??  $_SESSION['VF'] ?? '';
$failMessage = $_SESSION['register_error'] ?? $_SESSION['login_error'] ?? '';
unset($_SESSION['success']);
unset($_SESSION['email_verified']);
unset($_SESSION['register_error']);
unset($_SESSION['login_error']);
unset($_SESSION['VF']);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
    <link rel="stylesheet" href="public/assets/css/style.css">
    <link rel="stylesheet" href="public/assets/css/user.css">

</head>

<body>
    <div class="container">
        <?php if($successMessage): ?>
            <p id="flash-message" class="success-message"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>

        <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
            <form action="src/login_register.php" method="post">
                <h2>Login</h2>
                <?= showError($errors['login']); ?>

                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>

                <button type="submit" name="login">Login</button>

                <p>Don't have an account? <a href="#" onclick="showForm(`register-form`)">Register</a></p>
                <p>Forget Password? <a href="#" onclick="showMail(); return false;">Send link via email</a></p>
                <a href="src/gallerie.php" class="text-link" >View Gallerie</a>
            </form>

            <form id="forgot-form" action="src/forgot_password.php" method="post" style="<?= $forgotFormStyle ?>">
                <?= showError($errors['reset_error']); ?>
                <input type="text" id="user-username" name="username" placeholder="Enter your username" required>
                <button type="submit">Send</button>
            </form>

        </div>

        <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
            <form action="src/login_register.php" method="post">
                <h2>Register</h2>
                <?= showError($errors['register']); ?>

                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>

                <button type="submit" name="register">Register</button>

                <p>Already have an account? <a href="#" onclick="showForm(`login-form`)">Login</a></p>
                <a href="src/gallerie.php" class="text-link" >View Gallerie</a>
            </form>
        </div>

    </div>

    <script src="public/assets/js/script.js"></script>
</body>
<?php

?>
</html>


<!-- php -S 127.0.0.1:8000 -->
<!-- docker run --name camagru-db \
  -e MYSQL_ROOT_PASSWORD=root123 \
  -e MYSQL_DATABASE=camagru_db \
  -p 3307:3306 \
  -d mariadb:12 -->

<!-- php config/setup.php -->

<!-- docker run --name camagru-pma \
    -d \
    --link camagru-db:db \
    -p 8080:80 \
    -e PMA_HOST=camagru-db \
    phpmyadmin/phpmyadmin -->