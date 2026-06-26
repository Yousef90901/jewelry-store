<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (isLoggedIn()) redirect('index.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$phone || !$password) {
        $error = 'يرجى ملء جميع الحقول المطلوبة';
    } elseif ($password !== $confirm) {
        $error = 'كلمة المرور غير متطابقة';
    } elseif (strlen($password) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'البريد الإلكتروني مسجل بالفعل';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (:n, :e, :p, :pw)");
            $stmt->execute([':n' => $name, ':e' => $email, ':p' => $phone, ':pw' => $hashed]);
            $success = 'تم إنشاء الحساب بنجاح! يمكنك تسجيل الدخول الآن.';
        }
    }
}

$pageTitle = 'إنشاء حساب';
require_once 'includes/header.php';
?>
    <section class="auth-page section">
        <div class="container">
            <div class="auth-card">
                <div class="auth-header">
                    <i class="fas fa-gem"></i>
                    <h2>إنشاء حساب جديد</h2>
                    <p>انضمي إلينا لتجربة تسوق فريدة</p>
                </div>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label>الاسم الكامل</label>
                        <input type="text" name="name" placeholder="الاسم كما في الهوية" required>
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" placeholder="example@email.com" required>
                    </div>
                    <div class="form-group">
                        <label>رقم الهاتف</label>
                        <input type="tel" name="phone" placeholder="010xxxxxxx" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>كلمة المرور</label>
                            <input type="password" name="password" placeholder="أقل شيء 6 أحرف" required>
                        </div>
                        <div class="form-group">
                            <label>تأكيد كلمة المرور</label>
                            <input type="password" name="confirm_password" placeholder="أعيدي إدخال كلمة المرور" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">إنشاء الحساب</button>
                </form>
                <div class="auth-footer">
                    <p>لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
                </div>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
