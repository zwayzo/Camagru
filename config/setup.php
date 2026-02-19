<?php
// config/setup.php
// General setup for the application

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Optional: load global functions or classes
// require_once __DIR__ . '/../src/functions.php';
