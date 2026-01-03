<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Database configuration (Cloud first, safe & compatible)
|--------------------------------------------------------------------------
| - Cloud (Render / Railway): use environment variables
| - Local (XAMPP): optional fallback
*/

// ===== 1️⃣ Read environment variables =====
$DB_HOST = getenv('DB_HOST');
$DB_USER = getenv('DB_USER');
$DB_PASS = getenv('DB_PASS');
$DB_NAME = getenv('DB_NAME');
$DB_PORT = getenv('DB_PORT');

// ===== 2️⃣ Local fallback (ONLY if env not set) =====
if (!$DB_HOST || !$DB_USER || !$DB_NAME || !$DB_PORT) {
    // ⚠️ 本地开发才会用到（云端不会走这里）
    $DB_HOST = '127.0.0.1';
    $DB_USER = 'root';
    $DB_PASS = '';
    $DB_NAME = 'food_ordering';
    $DB_PORT = 3307; // 你本地 MySQL 端口
}

// ===== 3️⃣ Create mysqli connection =====
$mysqli = new mysqli(
    $DB_HOST,
    $DB_USER,
    $DB_PASS,
    $DB_NAME,
    (int)$DB_PORT
);

// ===== 4️⃣ Connection check =====
if ($mysqli->connect_errno) {
    error_log("MySQL connection failed: " . $mysqli->connect_error);
    die("Database connection failed.");
}

// ===== 5️⃣ Charset =====
$mysqli->set_charset('utf8mb4');

// ===== 6️⃣ ⭐ Compatibility layer (VERY IMPORTANT) =====
// 让旧代码里的 $conn 继续可用
$conn = $mysqli;
