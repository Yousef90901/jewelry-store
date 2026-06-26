<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

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
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9-]/', '-', $name)));
        $slug = preg_replace('/-+/', '-', $slug) . '-' . time();

        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = $slug . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/products/$image");
        }

        $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, description, price, sale_price, image, material, weight_grams, stock, featured) VALUES (:cat, :n, :s, :d, :p, :sp, :img, :mat, :w, :st, :f)");
        $stmt->execute([
            ':cat' => $category_id ?: null,
            ':n' => $name,
            ':s' => $slug,
            ':d' => $description,
            ':p' => $price,
            ':sp' => $sale_price ?: null,
            ':img' => $image ?: null,
            ':mat' => $material ?: null,
            ':w' => $weight ?: null,
            ':st' => $stock,
            ':f' => $featured
        ]);
        $success = 'تم إضافة المنتج بنجاح';
    }
}

$pageTitle = 'إضافة منتج';
require_once 'includes/admin-header.php';
?>
<div class="admin-content">
    <h1>إضافة منتج جديد</h1>
    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <div class="admin-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>اسم المنتج</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>التصنيف</label>
                    <select name="category_id">
                        <option value="">بدون تصنيف</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>السعر (ج.م)</label>
                    <input type="number" step="0.01" name="price" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>سعر التخفيض (ج.م)</label>
                    <input type="number" step="0.01" name="sale_price">
                </div>
                <div class="form-group">
                    <label>المخزون</label>
                    <input type="number" name="stock" value="10">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>الخامة</label>
                    <input type="text" name="material" placeholder="ذهب عيار 18">
                </div>
                <div class="form-group">
                    <label>الوزن (جرام)</label>
                    <input type="number" step="0.01" name="weight">
                </div>
            </div>
            <div class="form-group">
                <label>الوصف</label>
                <textarea name="description" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>صورة المنتج</label>
                <input type="file" name="image" accept="image/*">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="featured"> منتج مميز</label>
            </div>
            <div class="admin-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
                <a href="products.php" class="btn btn-outline">إلغاء</a>
            </div>
        </form>
    </div>
</div>
<?php require_once 'includes/admin-footer.php'; ?>
