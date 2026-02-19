<?php
$mysqli = require "../config/database.php";

$result = $mysqli->query("SELECT 1");
if ($result) {
    echo "Connected to database successfully!";
} else {
    echo "Error: " . $mysqli->error;
}
