<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = (int)($_POST['product_id'] ?? 0);
    $redirect = $_POST['redirect'] ?? 'cart.php';

    if ($action === 'add' && $product_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);
        $product = $stmt->fetch();
        if ($product) {
            $qty = max(1, (int)($_POST['quantity'] ?? 1));
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $qty;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'sale_price' => $product['sale_price'],
                    'image' => $product['image'],
                    'quantity' => $qty
                ];
            }
        }
    } elseif ($action === 'update') {
        foreach ($_POST['quantities'] ?? [] as $id => $qty) {
            $id = (int)$id;
            $qty = (int)$qty;
            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id]['quantity'] = $qty;
            }
        }
    } elseif ($action === 'remove' && $product_id > 0) {
        unset($_SESSION['cart'][$product_id]);
    } elseif ($action === 'clear') {
        unset($_SESSION['cart']);
    }

    if ($action === 'add' && isset($_POST['ajax'])) {
        echo json_encode(['count' => getCartCount(), 'total' => getCartTotal()]);
        exit;
    }

    header("Location: $redirect");
    exit;
}

$pageTitle = 'سلة التسوق';
require_once 'includes/header.php';
?>
    <section class="page-header">
        <div class="container">
            <h1>سلة التسوق</h1>
            <div class="breadcrumb">
                <a href="index.php">الرئيسية</a> / <span>سلة التسوق</span>
            </div>
        </div>
    </section>

    <section class="cart-page section">
        <div class="container">
            <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>سلتك فارغة</h3>
                    <p>أضيفي بعض القطع الرائعة إلى سلتك!</p>
                    <a href="products.php" class="btn btn-primary">تسوق الآن</a>
                </div>
            <?php else: ?>
                <form method="POST" action="cart.php">
                    <input type="hidden" name="action" value="update">
                    <div class="cart-table">
                        <div class="cart-header">
                            <div class="cart-col product-info-col">المنتج</div>
                            <div class="cart-col product-price-col">السعر</div>
                            <div class="cart-col product-qty-col">الكمية</div>
                            <div class="cart-col product-total-col">المجموع</div>
                            <div class="cart-col product-remove-col"></div>
                        </div>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                        <?php
                            $price = $item['sale_price'] > 0 ? $item['sale_price'] : $item['price'];
                            $subtotal = $price * $item['quantity'];
                        ?>
                        <div class="cart-row">
                            <div class="cart-col product-info-col">
                                <div class="cart-product">
                                    <img src="<?php echo getProductImage($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <div>
                                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="cart-col product-price-col"><?php echo formatPrice($price); ?></div>
                            <div class="cart-col product-qty-col">
                                <div class="qty-input">
                                    <button type="button" class="qty-btn qty-minus">-</button>
                                    <input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1">
                                    <button type="button" class="qty-btn qty-plus">+</button>
                                </div>
                            </div>
                            <div class="cart-col product-total-col"><?php echo formatPrice($subtotal); ?></div>
                            <div class="cart-col product-remove-col">
                                <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="remove-btn" onclick="event.preventDefault(); document.getElementById('remove-<?php echo $item['id']; ?>').submit();">
                                    <i class="fas fa-times"></i>
                                </a>
                                <form id="remove-<?php echo $item['id']; ?>" method="POST" action="cart.php" style="display:none">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-actions">
                        <a href="products.php" class="btn btn-outline"><i class="fas fa-arrow-right"></i> متابعة التسوق</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> تحديث السلة</button>
                        <a href="cart.php?action=clear" class="btn btn-outline btn-danger" onclick="return confirm('هل أنت متأكد من إفراغ السلة؟')"><i class="fas fa-trash"></i> إفراغ السلة</a>
                    </div>
                </form>

                <div class="cart-summary">
                    <div class="summary-card">
                        <h3>ملخص الطلب</h3>
                        <div class="summary-row">
                            <span>إجمالي المنتجات</span>
                            <span><?php echo formatPrice(getCartTotal()); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>الشحن</span>
                            <span><?php echo getCartTotal() >= 500 ? 'مجاني' : formatPrice(50); ?></span>
                        </div>
                        <div class="summary-row total">
                            <span>الإجمالي النهائي</span>
                            <span><?php echo formatPrice(getCartTotal() + (getCartTotal() >= 500 ? 0 : 50)); ?></span>
                        </div>
                        <a href="checkout.php" class="btn btn-primary btn-lg btn-block"><i class="fas fa-credit-card"></i> إتمام الطلب</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
