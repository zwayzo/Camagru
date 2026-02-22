<?php
session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '',
    'reset_error' => $_SESSION['reset_error'] ?? '',
    'email_verified' => $_SESSION['email_verified'] ?? '',
];

$activeForm = $_SESSION['active_form'] ?? 'login';

$forgotFormStyle = !empty($errors['reset_error']) 
    ? 'display: block; margin-top: 10px;' 
    : 'display: none; margin-top: 10px;';

function showError($error) {
    return !empty($error) 
        ? "<p style='color: #721c24; background: #f8d7da; padding: 12px; border-radius: 6px;'>$error</p>" 
        : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}
?>