<?php
$pageTitle = 'المتجر';
require_once 'config/database.php';
require_once 'includes/functions.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($category) {
    $query .= " AND c.slug = :category";
    $params[':category'] = $category;
}

if ($search) {
    $query .= " AND (p.name LIKE :search OR p.description LIKE :search2)";
    $params[':search'] = "%$search%";
    $params[':search2'] = "%$search%";
}

switch ($sort) {
    case 'price_asc': $query .= " ORDER BY p.price ASC"; break;
    case 'price_desc': $query .= " ORDER BY p.price DESC"; break;
    case 'name': $query .= " ORDER BY p.name ASC"; break;
    default: $query .= " ORDER BY p.created_at DESC"; break;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

require_once 'includes/header.php';
?>
    <section class="page-header">
        <div class="container">
            <h1>المتجر</h1>
            <div class="breadcrumb">
                <a href="index.php">الرئيسية</a> / <span><?php echo $category ? 'التصنيف: ' . htmlspecialchars($category) : 'المتجر'; ?></span>
            </div>
        </div>
    </section>

    <section class="shop section">
        <div class="container">
            <div class="shop-layout">
                <aside class="shop-sidebar">
                    <div class="sidebar-widget">
                        <h3><i class="fas fa-search"></i> بحث</h3>
                        <form method="GET" class="search-form">
                            <input type="text" name="search" placeholder="ابحث عن منتج..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                    <div class="sidebar-widget">
                        <h3><i class="fas fa-list"></i> التصنيفات</h3>
                        <ul class="category-list">
                            <li><a href="products.php" class="<?php echo !$category ? 'active' : ''; ?>">الكل</a></li>
                            <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="products.php?category=<?php echo $cat['slug']; ?>" class="<?php echo $category == $cat['slug'] ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </aside>

                <div class="shop-content">
                    <div class="shop-toolbar">
                        <p><?php echo count($products); ?> منتج</p>
                        <div class="sort-options">
                            <label>ترتيب:</label>
                            <select onchange="window.location = this.value;">
                                <option value="?sort=newest<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>الأحدث</option>
                                <option value="?sort=price_asc<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>السعر: من الأقل</option>
                                <option value="?sort=price_desc<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>السعر: من الأعلى</option>
                                <option value="?sort=name<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'name' ? 'selected' : ''; ?>>الاسم</option>
                            </select>
                        </div>
                    </div>

                    <?php if (empty($products)): ?>
                        <div class="empty-state">
                            <i class="fas fa-gem"></i>
                            <h3>لا توجد منتجات</h3>
                            <p>لم نعثر على منتجات تطابق بحثك. جربي كلمات بحث أخرى.</p>
                            <a href="products.php" class="btn btn-primary">عرض الكل</a>
                        </div>
                    <?php else: ?>
                        <div class="products-grid">
                            <?php foreach ($products as $product): ?>
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
                                            <button type="submit" class="action-btn"><i class="fas fa-shopping-bag"></i></button>
                                        </form>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
