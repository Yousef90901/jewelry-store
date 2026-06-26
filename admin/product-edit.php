<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();
if (!$product) { echo '<div style="padding:20px;text-align:center"><h2>المنتج غير موجود</h2><a href="products.php">العودة</a></div>'; exit; }

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $sale_price = (float)($_POST['sale_price'] ?? 0);
    $material = trim($_POST['material'] ?? '');
    $weight = (float)($_POST['weight'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $featured = isset($_POST['featured']) ? 1 : 0;

    if (!$name || !$price) {
        $error = 'الاسم والسعر مطلوبان';
    } else {
        $image = $product['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = $product['slug'] . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/products/$image");
        }

        $stmt = $pdo->prepare("UPDATE products SET category_id=:cat, name=:n, description=:d, price=:p, sale_price=:sp, image=:img, material=:mat, weight_grams=:w, stock=:st, featured=:f WHERE id=:id");
        $stmt->execute([':cat' => $category_id ?: null, ':n' => $name, ':d' => $description, ':p' => $price, ':sp' => $sale_price ?: null, ':img' => $image, ':mat' => $material ?: null, ':w' => $weight ?: null, ':st' => $stock, ':f' => $featured, ':id' => $id]);
        $success = 'تم تحديث المنتج بنجاح';
        $product['name'] = $name;
    }
}

$pageTitle = 'تعديل المنتج';
require_once 'includes/admin-header.php';
?>
<div class="admin-content">
    <h1>تعديل: <?php echo htmlspecialchars($product['name']); ?></h1>
    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <div class="admin-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>اسم المنتج</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>التصنيف</label>
                    <select name="category_id">
                        <option value="">بدون تصنيف</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $c['id'] == $product['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>السعر (ج.م)</label>
                    <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>سعر التخفيض (ج.م)</label>
                    <input type="number" step="0.01" name="sale_price" value="<?php echo $product['sale_price']; ?>">
                </div>
                <div class="form-group">
                    <label>المخزون</label>
                    <input type="number" name="stock" value="<?php echo $product['stock']; ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>الخامة</label>
                    <input type="text" name="material" value="<?php echo htmlspecialchars($product['material'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>الوزن (جرام)</label>
                    <input type="number" step="0.01" name="weight" value="<?php echo $product['weight_grams']; ?>">
                </div>
            </div>
            <div class="form-group">
                <label>الوصف</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label>صورة المنتج</label>
                <input type="file" name="image" accept="image/*">
                <?php if ($product['image']): ?>
                    <img src="<?php echo getProductImage($product['image']); ?>" class="image-preview">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="featured" <?php echo $product['featured'] ? 'checked' : ''; ?>> منتج مميز</label>
            </div>
            <div class="admin-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التغييرات</button>
                <a href="products.php" class="btn btn-outline">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php require_once 'includes/admin-footer.php'; ?>
