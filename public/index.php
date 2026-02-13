<?php
session_start();
require_once "../src/errors.php"; // adjust path as needed

// var_dump($_SESSION['register_error']);
// exit();
$successMessage = $_SESSION['succes'] ?? $_SESSION['email_verified'] ??  $_SESSION['VF'] ?? '';
$failMessage = $_SESSION['register_error'] ?? $_SESSION['login_error'] ?? '';
unset($_SESSION['success']);
unset($_SESSION['email_verified']);
unset($_SESSION['register_error']);
unset($_SESSION['login_error']);
unset($_SESSION['VF']);

// var_dump($successMessage);
// exit();





// var_dump($successMessage);
// exit();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="./assets/js/script.js"></script> 

</head>

<body>
    <div class="container">
        <?php if($successMessage): ?>
            <p id="flash-message" class="success-message"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
            <form action="../src/login_register.php" method="post">
                <h2>Login</h2>
                <?= showError($errors['login']); ?>

                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>

                <button type="submit" name="login">Login</button>

                <p>Don't have an account? <a href="#" onclick="showForm(`register-form`)">Register</a></p>
                <p>Forget Password? <a href="#" onclick="showMail(); return false;">Send link via email</a></p>
            </form>

            <!-- FORGOT PASSWORD FORM (separate form, initially hidden) -->
            <form id="forgot-form" action="../src/forgot_password.php" method="post" style="<?= $forgotFormStyle ?>">
                <?= showError($errors['reset_error']); ?>
                <input type="text" id="user-username" name="username" placeholder="Enter your username" required>
                <button type="submit">Send</button>
            </form>

            <!-- <p id="success-message" style="color: green; display: none;">Email sent successfully!</p> -->
        </div>

        <!-- REGISTER FORM -->
        <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
            <form action="../src/login_register.php" method="post">
                <h2>Register</h2>
                <?= showError($errors['register']); ?>

                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>

                <button type="submit" name="register">Register</button>

                <p>Already have an account? <a href="#" onclick="showForm(`login-form`)">Login</a></p>
            </form>
        </div>

    </div>

    <script src="assets/js/script.js"></script>
</body>
<?php
// session_unset();

?>
</html>
