<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();
$pageTitle = 'إدارة المنتجات';
require_once 'includes/admin-header.php';
?>
<div class="admin-content">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
        <h1>المنتجات</h1>
        <a href="product-add.php" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة منتج</a>
    </div>
    <div class="admin-card">
        <table class="admin-table">
            <thead><tr><th>#</th><th>الصورة</th><th>الاسم</th><th>التصنيف</th><th>السعر</th><th>المخزون</th><th>مميز</th><th>الإجراءات</th></tr></thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><img src="<?php echo getProductImage($p['image']); ?>" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:4px"></td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo htmlspecialchars($p['category_name'] ?? '-'); ?></td>
                    <td><?php echo formatPrice($p['sale_price'] ?: $p['price']); ?></td>
                    <td><?php echo $p['stock']; ?></td>
                    <td><?php echo $p['featured'] ? '<i class="fas fa-star" style="color:#D4AF37"></i>' : '-'; ?></td>
                    <td>
                        <a href="product-edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="product-delete.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('حذف المنتج؟')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'includes/admin-footer.php'; ?>
