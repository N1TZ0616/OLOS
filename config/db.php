<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| Database configuration (Cloud first)
|--------------------------------------------------------------------------
*/

// 只从环境变量读（Render / Railway）
$DB_HOST = getenv('DB_HOST');
$DB_USER = getenv('DB_USER');
$DB_PASS = getenv('DB_PASS');
$DB_NAME = getenv('DB_NAME');
$DB_PORT = getenv('DB_PORT');

// 如果没读到，直接报错（防止偷偷用 localhost）
if (!$DB_HOST || !$DB_USER || !$DB_NAME || !$DB_PORT) {
    die("Database environment variables not set");
}

// 创建连接
$mysqli = new mysqli(
    $DB_HOST,
    $DB_USER,
    $DB_PASS,
    $DB_NAME,
    (int)$DB_PORT
);

// 检查连接
if ($mysqli->connect_errno) {
    die("MySQL connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');
