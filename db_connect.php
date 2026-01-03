<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Database configuration
|--------------------------------------------------------------------------
| Priority:
| 1. Environment variables (Render / Railway)
| 2. Local fallback (XAMPP)
*/

// Railway / Render 环境变量
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'food_ordering';
$DB_PORT = getenv('DB_PORT') ?: 3306;

// Create connection
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, (int)$DB_PORT);

// Check connection
if ($mysqli->connect_errno) {
    error_log("MySQL Error: " . $mysqli->connect_error);
    die("Database connection failed.");
}

$mysqli->set_charset("utf8mb4");
