<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

$view = isset($_GET['view']) ? (int)$_GET['view'] : 0;

if ($view > 0) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
    $stmt->execute([':id' => $view]);
    $order = $stmt->fetch();
    if (!$order) { echo '<div>الطلب غير موجود</div>'; exit; }

    $items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :id");
    $items->execute([':id' => $view]);
    $orderItems = $items->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newStatus = $_POST['status'] ?? '';
        $paymentStatus = $_POST['payment_status'] ?? '';
        if ($newStatus) {
            $pdo->prepare("UPDATE orders SET status = :s WHERE id = :id")->execute([':s' => $newStatus, ':id' => $view]);
            $order['status'] = $newStatus;
        }
        if ($paymentStatus) {
            $pdo->prepare("UPDATE orders SET payment_status = :s WHERE id = :id")->execute([':s' => $paymentStatus, ':id' => $view]);
            $order['payment_status'] = $paymentStatus;
        }
    }

    $pageTitle = 'تفاصيل الطلب';
    require_once 'includes/admin-header.php';
    ?>
    <div class="admin-content">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h1>الطلب #<?php echo htmlspecialchars($order['order_number']); ?></h1>
            <a href="orders.php" class="btn btn-primary"><i class="fas fa-arrow-right"></i> العودة</a>
        </div>
        <div class="admin-grid-2">
            <div class="admin-card">
                <h2>معلومات الطلب</h2>
                <table class="admin-table">
                    <tr><td>رقم الطلب</td><td><?php echo htmlspecialchars($order['order_number']); ?></td></tr>
                    <tr><td>التاريخ</td><td><?php echo $order['created_at']; ?></td></tr>
                    <tr><td>الإجمالي</td><td><?php echo formatPrice($order['total']); ?></td></tr>
                    <tr><td>طريقة الدفع</td><td><?php echo $order['payment_method']; ?></td></tr>
                    <tr><td>رقم الهاتف</td><td><?php echo htmlspecialchars($order['phone'] ?? '-'); ?></td></tr>
                </table>
            </div>
            <div class="admin-card">
                <h2>تحديث الحالة</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>حالة الطلب</label>
                        <select name="status">
                            <option value="pending" <?php echo $order['status']=='pending'?'selected':''; ?>>قيد الانتظار</option>
                            <option value="processing" <?php echo $order['status']=='processing'?'selected':''; ?>>قيد التجهيز</option>
                            <option value="shipped" <?php echo $order['status']=='shipped'?'selected':''; ?>>تم الشحن</option>
                            <option value="delivered" <?php echo $order['status']=='delivered'?'selected':''; ?>>تم التوصيل</option>
                            <option value="cancelled" <?php echo $order['status']=='cancelled'?'selected':''; ?>>ملغي</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>حالة الدفع</label>
                        <select name="payment_status">
                            <option value="pending" <?php echo $order['payment_status']=='pending'?'selected':''; ?>>قيد الانتظار</option>
                            <option value="paid" <?php echo $order['payment_status']=='paid'?'selected':''; ?>>مدفوع</option>
                            <option value="failed" <?php echo $order['payment_status']=='failed'?'selected':''; ?>>فشل</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> تحديث</button>
                </form>
            </div>
        </div>
        <div class="admin-card" style="margin-top:20px">
            <h2>عنوان الشحن</h2>
            <p><?php echo nl2br(htmlspecialchars($order['shipping_address'] ?? '-')); ?></p>
            <?php if ($order['notes']): ?>
                <h3 style="margin-top:15px">ملاحظات</h3>
                <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
            <?php endif; ?>
        </div>
        <div class="admin-card" style="margin-top:20px">
            <h2>المنتجات</h2>
            <table class="admin-table">
                <thead><tr><th>المنتج</th><th>الكمية</th><th>السعر</th><th>الإجمالي</th></tr></thead>
                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                    <tr><td><?php echo htmlspecialchars($item['product_name']); ?></td><td><?php echo $item['quantity']; ?></td><td><?php echo formatPrice($item['price']); ?></td><td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    require_once 'includes/admin-footer.php';
    exit;
}

$status = $_GET['status'] ?? '';
$query = "SELECT * FROM orders";
if ($status) {
    $query .= " WHERE status = :status";
}
$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
if ($status) $stmt->execute([':status' => $status]);
else $stmt->execute();
$orders = $stmt->fetchAll();

$pageTitle = 'الطلبات';
require_once 'includes/admin-header.php';
?>
<div class="admin-content">
    <h1>الطلبات</h1>
    <div style="margin-bottom:15px;display:flex;gap:10px;flex-wrap:wrap">
        <a href="orders.php" class="btn <?php echo !$status ? 'btn-primary' : ''; ?>">الكل</a>
        <a href="orders.php?status=pending" class="btn <?php echo $status=='pending'?'btn-primary':''; ?>">قيد الانتظار</a>
        <a href="orders.php?status=processing" class="btn <?php echo $status=='processing'?'btn-primary':''; ?>">قيد التجهيز</a>
        <a href="orders.php?status=shipped" class="btn <?php echo $status=='shipped'?'btn-primary':''; ?>">تم الشحن</a>
        <a href="orders.php?status=delivered" class="btn <?php echo $status=='delivered'?'btn-primary':''; ?>">تم التوصيل</a>
        <a href="orders.php?status=cancelled" class="btn <?php echo $status=='cancelled'?'btn-primary':''; ?>">ملغي</a>
    </div>
    <div class="admin-card">
        <table class="admin-table">
            <thead><tr><th>#</th><th>رقم الطلب</th><th>المبلغ</th><th>الحالة</th><th>الدفع</th><th>التاريخ</th><th>الإجراءات</th></tr></thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?php echo $o['id']; ?></td>
                    <td><?php echo htmlspecialchars($o['order_number']); ?></td>
                    <td><?php echo formatPrice($o['total']); ?></td>
                    <td><span class="status status-<?php echo $o['status']; ?>"><?php echo $o['status']; ?></span></td>
                    <td><span class="status status-<?php echo $o['payment_status'] == 'paid' ? 'delivered' : ($o['payment_status'] == 'failed' ? 'cancelled' : 'pending'); ?>"><?php echo $o['payment_status']; ?></span></td>
                    <td><?php echo date('Y-m-d', strtotime($o['created_at'])); ?></td>
                    <td><a href="orders.php?view=<?php echo $o['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'includes/admin-footer.php'; ?>
