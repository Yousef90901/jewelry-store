<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$revenue = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status != 'failed'")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();
$lowStock = $pdo->query("SELECT * FROM products WHERE stock <= 5 LIMIT 5")->fetchAll();

$pageTitle = 'لوحة التحكم';
require_once 'includes/admin-header.php';
?>
<div class="admin-content">
    <h1>لوحة التحكم</h1>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32"><i class="fas fa-gem"></i></div>
            <div class="stat-info"><h3><?php echo $productCount; ?></h3><p>المنتجات</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e3f2fd;color:#1565c0"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-info"><h3><?php echo $orderCount; ?></h3><p>الطلبات</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff3e0;color:#e65100"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-info"><h3><?php echo formatPrice($revenue); ?></h3><p>الإيرادات</p></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fce4ec;color:#c62828"><i class="fas fa-clock"></i></div>
            <div class="stat-info"><h3><?php echo $pendingOrders; ?></h3><p>قيد الانتظار</p></div>
        </div>
    </div>

    <div class="admin-grid-2">
        <div class="admin-card">
            <h2>آخر الطلبات</h2>
            <table class="admin-table">
                <thead><tr><th>#</th><th>رقم الطلب</th><th>المبلغ</th><th>الحالة</th><th>التاريخ</th></tr></thead>
                <tbody>
                    <?php foreach ($recentOrders as $o): ?>
                    <tr>
                        <td><?php echo $o['id']; ?></td>
                        <td><a href="orders.php?view=<?php echo $o['id']; ?>"><?php echo htmlspecialchars($o['order_number']); ?></a></td>
                        <td><?php echo formatPrice($o['total']); ?></td>
                        <td><span class="status status-<?php echo $o['status']; ?>"><?php echo $o['status']; ?></span></td>
                        <td><?php echo date('Y-m-d', strtotime($o['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="admin-card">
            <h2>منتجات منخفضة المخزون</h2>
            <table class="admin-table">
                <thead><tr><th>المنتج</th><th>المخزون</th></tr></thead>
                <tbody>
                    <?php foreach ($lowStock as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><span class="status status-danger"><?php echo $p['stock']; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once 'includes/admin-footer.php'; ?>
