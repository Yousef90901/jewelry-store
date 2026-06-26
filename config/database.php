<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

define('SITE_URL', 'https://jewelry-store-production.up.railway.app');
define('SITE_NAME', 'Jewelry Store | متجر المجوهرات');

// Railway provides config via env vars; use SQLite by default
$dbPath = getenv('RAILWAY_VOLUME_PATH')
    ? rtrim(getenv('RAILWAY_VOLUME_PATH'), '/') . '/database/jewelry.db'
    : __DIR__ . '/../database/jewelry.db';

$dbDir = dirname($dbPath);
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("PRAGMA journal_mode=WAL");
    $pdo->exec("PRAGMA foreign_keys=ON");
} catch(PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}

function isAdmin() {
    return isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}
