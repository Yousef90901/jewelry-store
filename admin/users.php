<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$pageTitle = 'المستخدمون';
require_once 'includes/admin-header.php';
?>
<div class="admin-content">
    <h1>المستخدمون</h1>
    <div class="admin-card">
        <table class="admin-table">
            <thead><tr><th>#</th><th>الاسم</th><th>البريد</th><th>الهاتف</th><th>مسؤول</th><th>التسجيل</th></tr></thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['phone'] ?? '-'); ?></td>
                    <td><?php echo $u['is_admin'] ? '<i class="fas fa-check-circle" style="color:#2e7d32"></i>' : '-'; ?></td>
                    <td><?php echo date('Y-m-d', strtotime($u['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once 'includes/admin-footer.php'; ?>
