<?php
$pageTitle = 'الرئيسية';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

$featured = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 LIMIT 8")->fetchAll();
$newProducts = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 4")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
    <section class="hero">
        <div class="hero-slider">
            <div class="hero-slide active">
                <div class="hero-content">
                    <span class="hero-subtitle">مجموعة 2026</span>
                    <h1>تألقي بأناقة <span class="gold">المجوهرات</span> الفاخرة</h1>
                    <p>اكتشفي أحدث تشكيلاتنا من المجوهرات المصممة بعناية لأجلك</p>
                    <a href="products.php" class="btn btn-primary">تسوق الآن</a>
                </div>
            </div>
        </div>
    </section>

    <section class="categories section">
        <div class="container">
            <div class="section-header">
                <h2>تصفح <span class="gold">الأقسام</span></h2>
                <p>اكتشفي مجموعتنا المتنوعة من المجوهرات الفاخرة</p>
            </div>
            <div class="categories-grid">
                <?php foreach ($categories as $cat): ?>
                <a href="products.php?category=<?php echo $cat['slug']; ?>" class="category-card">
                    <div class="category-icon">
                        <?php
                        $icons = ['necklaces' => 'fa-necklace', 'bracelets' => 'fa-ring', 'rings' => 'fa-gem', 'earrings' => 'fa-earrings', 'watches' => 'fa-clock'];
                        $icon = isset($icons[$cat['slug']]) ? $icons[$cat['slug']] : 'fa-gem';
                        ?>
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="featured section">
        <div class="container">
            <div class="section-header">
                <h2>المنتجات <span class="gold">المميزة</span></h2>
                <p>أجمل قطع المجوهرات التي نالت إعجاب عميلاتنا</p>
            </div>
            <div class="products-grid">
                <?php foreach ($featured as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo getProductImage($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php if ($product['sale_price']): ?>
                            <span class="product-badge sale">خصم</span>
                        <?php endif; ?>
                        <div class="product-actions">
                            <a href="product-detail.php?slug=<?php echo $product['slug']; ?>" class="action-btn"><i class="fas fa-eye"></i></a>
                            <form method="POST" action="cart.php" style="display:inline">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="redirect" value="index.php">
                                <button type="submit" class="action-btn"><i class="fas fa-shopping-bag"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="product-info">
                        <?php if (isset($product['category_name'])): ?>
                            <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                        <?php endif; ?>
                        <h3><a href="product-detail.php?slug=<?php echo $product['slug']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                        <div class="product-price">
                            <?php if ($product['sale_price']): ?>
                                <span class="old-price"><?php echo formatPrice($product['price']); ?></span>
                                <span class="current-price"><?php echo formatPrice($product['sale_price']); ?></span>
                            <?php else: ?>
                                <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="banner section">
        <div class="container">
            <div class="banner-content">
                <div class="banner-text">
                    <span class="banner-subtitle">تخفيضات الصيف</span>
                    <h2>خصم يصل إلى <span class="gold">40%</span></h2>
                    <p>على جميع مجموعات الذهب والفضة لفترة محدودة</p>
                    <a href="products.php" class="btn btn-primary">تسوق الآن</a>
                </div>
                <div class="banner-image">
                    <i class="fas fa-gem"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="new-arrivals section">
        <div class="container">
            <div class="section-header">
                <h2>وصل <span class="gold">حديثاً</span></h2>
                <p>أحدث قطع المجوهرات المضافة إلى متجرنا</p>
            </div>
            <div class="products-grid">
                <?php foreach ($newProducts as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo getProductImage($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <span class="product-badge new">جديد</span>
                        <div class="product-actions">
                            <a href="product-detail.php?slug=<?php echo $product['slug']; ?>" class="action-btn"><i class="fas fa-eye"></i></a>
                            <form method="POST" action="cart.php" style="display:inline">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="action-btn"><i class="fas fa-shopping-bag"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3><a href="product-detail.php?slug=<?php echo $product['slug']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                        <div class="product-price">
                            <?php if ($product['sale_price']): ?>
                                <span class="old-price"><?php echo formatPrice($product['price']); ?></span>
                                <span class="current-price"><?php echo formatPrice($product['sale_price']); ?></span>
                            <?php else: ?>
                                <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="features-bar">
        <div class="container">
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-truck"></i>
                    <h4>توصيل مجاني</h4>
                    <p>لجميع الطلبات فوق 500 ج.م</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-undo"></i>
                    <h4>إرجاع مجاني</h4>
                    <p>خلال 30 يوم من الاستلام</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <h4>منتجات أصلية</h4>
                    <p>ضمان أصالة 100%</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <h4>دعم فوري</h4>
                    <p>خدمة عملاء على مدار الساعة</p>
                </div>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
