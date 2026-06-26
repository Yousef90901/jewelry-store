<?php if (!isAdmin()) redirect('../login.php'); ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>لوحة التحكم</title>
    <link rel="stylesheet" href="assets/admin-style.css">
</head>
<body>
<div class="admin-sidebar">
    <div class="admin-logo">
        <i class="fas fa-gem"></i>
        <span>Jewelry <span class="gold">Store</span></span>
        <span style="font-size:.8rem;color:rgba(255,255,255,.5)">لوحة التحكم</span>
    </div>
    <ul>
        <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i class="fas fa-home"></i> الرئيسية</a></li>
        <li><a href="products.php" class="<?php echo strpos(basename($_SERVER['PHP_SELF']), 'product') !== false ? 'active' : ''; ?>"><i class="fas fa-gem"></i> المنتجات</a></li>
        <li><a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> الطلبات</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> المستخدمون</a></li>
        <li><a href="../index.php"><i class="fas fa-external-link-alt"></i> الموقع</a></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
    </ul>
</div>
<div class="admin-main">
    <div class="admin-topbar">
        <span>مرحباً، <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
        <span><?php echo date('Y-m-d'); ?></span>
    </div>
