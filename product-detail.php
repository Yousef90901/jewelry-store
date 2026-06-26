<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = :slug");
$stmt->execute([':slug' => $slug]);
$product = $stmt->fetch();

if (!$product) {
    header("HTTP/1.0 404 Not Found");
    $pageTitle = 'المنتج غير موجود';
    require_once 'includes/header.php';
    echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><h3>المنتج غير موجود</h3><a href="products.php" class="btn btn-primary">عودة للمتجر</a></div>';
    require_once 'includes/footer.php';
    exit;
}

$pageTitle = $product['name'];
$related = $pdo->prepare("SELECT * FROM products WHERE category_id = :cat AND id != :id LIMIT 4");
$related->execute([':cat' => $product['category_id'], ':id' => $product['id']]);
$relatedProducts = $related->fetchAll();

require_once 'includes/header.php';
?>
    <section class="page-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">الرئيسية</a> / <a href="products.php">المتجر</a> / <span><?php echo htmlspecialchars($product['name']); ?></span>
            </div>
        </div>
    </section>

    <section class="product-detail section">
        <div class="container">
            <div class="product-detail-layout">
                <div class="product-gallery">
                    <div class="product-main-image">
                        <img src="<?php echo getProductImage($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php if ($product['sale_price']): ?>
                            <span class="product-badge sale">خصم</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="product-info-detail">
                    <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="product-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span>(12 تقييم)</span>
                    </div>
                    <div class="product-price-detail">
                        <?php if ($product['sale_price']): ?>
                            <span class="old-price"><?php echo formatPrice($product['price']); ?></span>
                            <span class="current-price"><?php echo formatPrice($product['sale_price']); ?></span>
                            <span class="save-badge">وفر <?php echo formatPrice($product['price'] - $product['sale_price']); ?></span>
                        <?php else: ?>
                            <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-meta">
                        <?php if ($product['material']): ?>
                            <div class="meta-item"><span>الخامة:</span> <?php echo htmlspecialchars($product['material']); ?></div>
                        <?php endif; ?>
                        <?php if ($product['weight_grams']): ?>
                            <div class="meta-item"><span>الوزن:</span> <?php echo $product['weight_grams']; ?> جرام</div>
                        <?php endif; ?>
                        <div class="meta-item">
                            <span>الحالة:</span>
                            <?php if ($product['stock'] > 0): ?>
                                <span class="in-stock"><i class="fas fa-check-circle"></i> متوفر</span>
                            <?php else: ?>
                                <span class="out-of-stock"><i class="fas fa-times-circle"></i> غير متوفر</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product-description">
                        <h3>الوصف</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                    <form method="POST" action="cart.php" class="add-to-cart-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="quantity-selector">
                            <label>الكمية:</label>
                            <div class="qty-input">
                                <button type="button" class="qty-btn qty-minus">-</button>
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                                <button type="button" class="qty-btn qty-plus">+</button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-shopping-bag"></i> أضف إلى السلة
                        </button>
                    </form>
                    <div class="product-share">
                        <span>مشاركة:</span>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                        <a href="#"><i class="fab fa-pinterest-p"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($relatedProducts)): ?>
    <section class="related-products section">
        <div class="container">
            <div class="section-header">
                <h2>منتجات <span class="gold">مشابهة</span></h2>
            </div>
            <div class="products-grid">
                <?php foreach ($relatedProducts as $rp): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo getProductImage($rp['image']); ?>" alt="<?php echo htmlspecialchars($rp['name']); ?>">
                        <div class="product-actions">
                            <a href="product-detail.php?slug=<?php echo $rp['slug']; ?>" class="action-btn"><i class="fas fa-eye"></i></a>
                            <form method="POST" action="cart.php" style="display:inline">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $rp['id']; ?>">
                                <button type="submit" class="action-btn"><i class="fas fa-shopping-bag"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3><a href="product-detail.php?slug=<?php echo $rp['slug']; ?>"><?php echo htmlspecialchars($rp['name']); ?></a></h3>
                        <div class="product-price">
                            <?php if ($rp['sale_price']): ?>
                                <span class="old-price"><?php echo formatPrice($rp['price']); ?></span>
                                <span class="current-price"><?php echo formatPrice($rp['sale_price']); ?></span>
                            <?php else: ?>
                                <span class="current-price"><?php echo formatPrice($rp['price']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
