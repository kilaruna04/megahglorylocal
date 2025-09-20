<?php
// ================== KONFIG WAKTU ==================
date_default_timezone_set('Asia/Jakarta'); // Ganti sesuai kebutuhan

// ================== KONFIG DATABASE ==================
$host = "localhost";
$user = "megw6638_megah1"; // atau "root" kalau pakai default XAMPP
$pass = "X@tK@U3TkSj4uE@"; // kosong "" kalau pakai root
$db   = "megw6638_servicedesk";

// ================== KONFIG ERROR LOG ==================
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.log'); // log bawaan PHP

// ================== HELPER LOGGER ==================
function logMessage($message, $level = "INFO") {
    $logFile = __DIR__ . "/app_log.log";
    $time = date("Y-m-d H:i:s"); // waktu sesuai timezone di atas
    $entry = "[$time] [$level] $message" . PHP_EOL;
    file_put_contents($logFile, $entry, FILE_APPEND);
}

// ================== KONEKSI DATABASE ==================
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    logMessage("Database error: " . $conn->connect_error, "ERROR");
    die("❌ Database error, silakan cek file log!");
}

// ================== KONFIG BASE URL ==================
$hostName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
define('BASE_URL', 'http://' . $hostName . '/megahglory');
?>
