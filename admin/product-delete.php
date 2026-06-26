<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $p = $stmt->fetch();
    if ($p && $p['image'] && file_exists("../uploads/products/" . $p['image'])) {
        unlink("../uploads/products/" . $p['image']);
    }
    $pdo->prepare("DELETE FROM products WHERE id = :id")->execute([':id' => $id]);
}
redirect('products.php');
