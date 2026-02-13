<?php
require_once __DIR__ . '/../config/config.php';

if ($conn) {
    echo "Database connected successfully!";
} else {
    echo "Database connection failed!";
}
?>
