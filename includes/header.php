<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="top-bar">
        <div class="container">
            <span><i class="fas fa-truck"></i> توصيل مجاني للطلبات فوق 500 ج.م</span>
            <span><i class="fas fa-phone"></i> 01012345678</span>
            <span><i class="fas fa-shield-alt"></i> ضمان استعادة النقود 30 يوم</span>
        </div>
    </div>

    <header class="header">
        <div class="container">
            <div class="header-inner">
                <div class="logo">
                    <a href="index.php">
                        <i class="fas fa-gem"></i>
                        <span>Jewelry <span class="gold">Store</span></span>
                    </a>
                </div>
                <nav class="nav" id="mainNav">
                    <ul>
                        <li><a href="index.php">الرئيسية</a></li>
                        <li><a href="products.php">المتجر</a></li>
                        <li><a href="products.php?category=necklaces">قلادات</a></li>
                        <li><a href="products.php?category=rings">خواتم</a></li>
                        <li><a href="products.php?category=bracelets">أساور</a></li>
                        <li><a href="products.php?category=earrings">أقراط</a></li>
                    </ul>
                </nav>
                <div class="header-actions">
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-count" id="cartCount"><?php echo getCartCount(); ?></span>
                    </a>
                    <div class="user-menu">
                        <?php if (isLoggedIn()): ?>
                            <a href="profile.php"><i class="fas fa-user"></i></a>
                            <?php if (isAdmin()): ?>
                                <a href="admin/index.php"><i class="fas fa-cog"></i></a>
                            <?php endif; ?>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i></a>
                        <?php else: ?>
                            <a href="login.php"><i class="fas fa-user"></i></a>
                        <?php endif; ?>
                    </div>
                    <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
                </div>
            </div>
        </div>
    </header>
    <main>
