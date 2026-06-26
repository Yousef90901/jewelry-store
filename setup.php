<?php
echo "============================================\n";
echo "  Jewelry Store - تجهيز الموقع\n";
echo "============================================\n\n";

$dbPath = __DIR__ . '/database/jewelry.db';
$schemaPath = __DIR__ . '/database/schema.sqlite.sql';

echo "الخطوة 1: إنشاء قاعدة البيانات (SQLite)...\n";

$dbDir = dirname($dbPath);
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

try {
    $isNew = !file_exists($dbPath) || filesize($dbPath) === 0;
    if ($isNew && file_exists($dbPath)) {
        unlink($dbPath);
    }

    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA journal_mode=WAL");
    $pdo->exec("PRAGMA foreign_keys=ON");

    $sql = file_get_contents($schemaPath);
    $statements = explode(';', $sql);
    $count = 0;
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if (!empty($stmt)) {
            $pdo->exec($stmt);
            $count++;
        }
    }

    echo "  ✓ تم إنشاء $count جدول وأمر\n";
    echo "  ✓ قاعدة البيانات جاهزة!\n\n";
    echo "بيانات الدخول للمدير:\n";
    echo "  البريد: admin@jewelry.com\n";
    echo "  كلمة المرور: password\n\n";
} catch (Exception $e) {
    echo "✗ خطأ: " . $e->getMessage() . "\n";
    exit(1);
}

echo "لتشغيل الموقع محلياً:\n";
echo "  php -S localhost:8000 -t \"" . __DIR__ . "\"\n";
echo "  ثم افتح المتصفح على: http://localhost:8000\n\n";
