<?php
// db_connect.php
// Railway MySQL (Cloud) connection

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   Railway MySQL Config
========================= */
$DB_HOST = 'gondola.proxy.rlwy.net';
$DB_USER = 'root';
$DB_PASS = 'UQnIBtmqRCthKfELFHlTBCOBBffwTKDy';
$DB_NAME = 'olos';
$DB_PORT = 16199;

/* =========================
   Create mysqli connection
========================= */
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);

/* =========================
   Connection check
========================= */
if ($mysqli->connect_errno) {
    error_log("MySQL connect error: " . $mysqli->connect_error);
    echo "Service temporarily unavailable. (Database connection error)";
    exit;
}

/* =========================
   Charset
========================= */
$mysqli->set_charset('utf8mb4');
