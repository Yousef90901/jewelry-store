<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect('cart.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'cod';

    if (!$name || !$phone || !$address || !$email) {
        $error = 'يرجى ملء جميع الحقول المطلوبة';
    } else {
        try {
            $pdo->beginTransaction();

            $order_number = 'ORD-' . strtoupper(uniqid());
            $total = getCartTotal() + (getCartTotal() >= 500 ? 0 : 50);

            $stmt = $pdo->prepare("INSERT INTO orders (order_number, total, payment_method, shipping_address, phone, notes) VALUES (:on, :total, :pm, :addr, :phone, :notes)");
            $stmt->execute([
                ':on' => $order_number,
                ':total' => $total,
                ':pm' => $payment_method,
                ':addr' => $address,
                ':phone' => $phone,
                ':notes' => $notes
            ]);
            $order_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (:oid, :pid, :pn, :qty, :price)");
            foreach ($_SESSION['cart'] as $item) {
                $price = $item['sale_price'] > 0 ? $item['sale_price'] : $item['price'];
                $stmt->execute([
                    ':oid' => $order_id,
                    ':pid' => $item['id'],
                    ':pn' => $item['name'],
                    ':qty' => $item['quantity'],
                    ':price' => $price
                ]);
            }

            $pdo->commit();
            $_SESSION['order_success'] = $order_number;
            unset($_SESSION['cart']);
            redirect('checkout.php?success=1');
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'حدث خطأ أثناء معالجة طلبك. يرجى المحاولة مرة أخرى.';
        }
    }
}

$success_order = $_SESSION['order_success'] ?? null;
unset($_SESSION['order_success']);

$pageTitle = 'إتمام الطلب';
require_once 'includes/header.php';
?>
    <section class="page-header">
        <div class="container">
            <h1>إتمام الطلب</h1>
            <div class="breadcrumb">
                <a href="index.php">الرئيسية</a> / <a href="cart.php">سلة التسوق</a> / <span>إتمام الطلب</span>
            </div>
        </div>
    </section>

    <section class="checkout-page section">
        <div class="container">
            <?php if (isset($_GET['success']) && $success_order): ?>
                <div class="order-success">
                    <i class="fas fa-check-circle"></i>
                    <h2>تم الطلب بنجاح!</h2>
                    <p>رقم الطلب: <strong><?php echo htmlspecialchars($success_order); ?></strong></p>
                    <p>سيتم التواصل معك قريباً لتأكيد الطلب.</p>
                    <a href="products.php" class="btn btn-primary">متابعة التسوق</a>
                </div>
            <?php else: ?>
                <div class="checkout-layout">
                    <div class="checkout-form">
                        <h2>معلومات الشحن</h2>
                        <?php if ($error): ?>
                            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="form-group">
                                <label>الاسم الكامل <span class="required">*</span></label>
                                <input type="text" name="name" placeholder="مثال: سارة أحمد" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>البريد الإلكتروني <span class="required">*</span></label>
                                    <input type="email" name="email" placeholder="example@email.com" required>
                                </div>
                                <div class="form-group">
                                    <label>رقم الهاتف <span class="required">*</span></label>
                                    <input type="tel" name="phone" placeholder="010xxxxxxx" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>عنوان الشحن <span class="required">*</span></label>
                                <textarea name="address" rows="3" placeholder="المدينة، الحي، الشارع، رقم المبنى" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>طريقة الدفع</label>
                                <select name="payment_method">
                                    <option value="cod">الدفع عند الاستلام</option>
                                    <option value="card">بطاقة ائتمان</option>
                                    <option value="bank">تحويل بنكي</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>ملاحظات إضافية (اختياري)</label>
                                <textarea name="notes" rows="2" placeholder="أي ملاحظات للطلب..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-check"></i> تأكيد الطلب
                            </button>
                        </form>
                    </div>
                    <div class="checkout-summary">
                        <h2>ملخص الطلب</h2>
                        <?php $shipping = getCartTotal() >= 500 ? 0 : 50; ?>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <?php $price = $item['sale_price'] > 0 ? $item['sale_price'] : $item['price']; ?>
                            <div class="checkout-item">
                                <img src="<?php echo getProductImage($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div>
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <span>الكمية: <?php echo $item['quantity']; ?></span>
                                </div>
                                <span class="checkout-item-price"><?php echo formatPrice($price * $item['quantity']); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="checkout-totals">
                            <div class="summary-row"><span>المجموع الفرعي</span><span><?php echo formatPrice(getCartTotal()); ?></span></div>
                            <div class="summary-row"><span>الشحن</span><span><?php echo $shipping == 0 ? 'مجاني' : formatPrice($shipping); ?></span></div>
                            <div class="summary-row total"><span>الإجمالي</span><span><?php echo formatPrice(getCartTotal() + $shipping); ?></span></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
