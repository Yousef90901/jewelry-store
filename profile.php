<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
if (!isLoggedIn()) redirect('login.php');

$user_id = $_SESSION['user_id'];
$user = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$user->execute([':id' => $user_id]);
$user = $user->fetch();

$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = :id ORDER BY created_at DESC LIMIT 10");
$orders->execute([':id' => $user_id]);
$userOrders = $orders->fetchAll();

$pageTitle = 'ملفي الشخصي';
require_once 'includes/header.php';
?>
    <section class="page-header">
        <div class="container">
            <h1>ملفي الشخصي</h1>
            <div class="breadcrumb"><a href="index.php">الرئيسية</a> / <span>ملفي</span></div>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <div class="checkout-layout">
                <div class="checkout-form">
                    <h2>البيانات الشخصية</h2>
                    <p><strong>الاسم:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>البريد:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>الهاتف:</strong> <?php echo htmlspecialchars($user['phone'] ?? '-'); ?></p>
                </div>
                <div class="checkout-summary">
                    <h2>آخر الطلبات</h2>
                    <?php if (empty($userOrders)): ?>
                        <p style="color:var(--text-light)">لا توجد طلبات حتى الآن</p>
                    <?php else: ?>
                        <?php foreach ($userOrders as $o): ?>
                        <div class="checkout-item">
                            <div>
                                <h4>#<?php echo htmlspecialchars($o['order_number']); ?></h4>
                                <span><?php echo $o['created_at']; ?></span>
                            </div>
                            <span><?php echo formatPrice($o['total']); ?></span>
                            <span class="status status-<?php echo $o['status']; ?>"><?php echo $o['status']; ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php require_once 'includes/footer.php'; ?>
