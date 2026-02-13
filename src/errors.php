<?php
session_start();

// Collect errors from session
$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '',
    'reset_error' => $_SESSION['reset_error'] ?? '',
    'email_verified' => $_SESSION['email_verified'] ?? '',
];

// Decide which form should be active
$activeForm = $_SESSION['active_form'] ?? 'login';

// Clear session messages after reading
// session_unset();

// ---------------
// NEW: decide forgot form visibility based on error
$forgotFormStyle = !empty($errors['reset_error']) 
    ? 'display: block; margin-top: 10px;' 
    : 'display: none; margin-top: 10px;';

// Functions
function showError($error) {
    return !empty($error) 
        ? "<p style='color: #721c24; background: #f8d7da; padding: 12px; border-radius: 6px;'>$error</p>" 
        : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}
?>