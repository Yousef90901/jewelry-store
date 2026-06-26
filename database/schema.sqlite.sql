CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    image TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category_id INTEGER,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT,
    price REAL NOT NULL,
    sale_price REAL,
    image TEXT,
    material TEXT,
    weight_grams REAL,
    stock INTEGER DEFAULT 0,
    featured INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    phone TEXT,
    address TEXT,
    is_admin INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    order_number TEXT NOT NULL UNIQUE,
    total REAL NOT NULL,
    status TEXT DEFAULT 'pending',
    payment_method TEXT,
    payment_status TEXT DEFAULT 'pending',
    shipping_address TEXT,
    phone TEXT,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER,
    product_name TEXT,
    quantity INTEGER NOT NULL,
    price REAL NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

INSERT OR IGNORE INTO categories (id, name, slug, image) VALUES
(1, 'قلادات', 'necklaces', 'necklace.jpg'),
(2, 'أساور', 'bracelets', 'bracelet.jpg'),
(3, 'خواتم', 'rings', 'ring.jpg'),
(4, 'أقراط', 'earrings', 'earrings.jpg'),
(5, 'ساعات', 'watches', 'watch.jpg');

INSERT OR IGNORE INTO products (id, category_id, name, slug, description, price, sale_price, image, material, weight_grams, stock, featured) VALUES
(1, 1, 'قلادة قلب من الذهب الأبيض', 'white-gold-heart-necklace', 'قلادة أنيقة على شكل قلب مصنوعة من الذهب الأبيض عيار 18 مع حجر زركون', 2500.00, 2200.00, NULL, 'ذهب أبيض عيار 18', 8.50, 15, 1),
(2, 1, 'قلادة ماسة نادرة', 'rare-diamond-necklace', 'قلادة فاخرة بماسة طبيعية نادرة محاطة بالألماس', 8500.00, NULL, NULL, 'ذهب أصفر عيار 21', 12.00, 5, 1),
(3, 1, 'قلادة لؤلؤ طبيعي', 'natural-pearl-necklace', 'قلادة من اللؤلؤ الطبيعي المستورد مع مشبك ذهبي', 1800.00, 1500.00, NULL, 'لؤلؤ + ذهب', 6.00, 20, 0),
(4, 2, 'سوار تيفاني المستوحى', 'tiffany-inspired-bracelet', 'سوار راقي بتصميم مستوحى من تيفاني مع حجر أزرق', 3200.00, NULL, NULL, 'فضة إسترليني + ذهب', 12.00, 10, 1),
(5, 2, 'سوار ماسي متعدد الطبقات', 'multi-layer-diamond-bracelet', 'سوار ماسي ثلاثي الطبقات فاخر', 6500.00, 5500.00, NULL, 'ذهب وردي عيار 18', 15.00, 8, 1),
(6, 3, 'خاتم خطوبة ألماس', 'diamond-engagement-ring', 'خاتم خطوبة فاخر بألماسة مركزية 1 قيراط', 12000.00, NULL, NULL, 'ذهب أبيض عيار 18', 5.00, 3, 1),
(7, 3, 'خاتم زفاف كلاسيكي', 'classic-wedding-band', 'خاتم زفاف كلاسيكي ناعم مناسب للأزواج', 3500.00, 3000.00, NULL, 'ذهب أصفر عيار 21', 6.00, 25, 0),
(8, 4, 'أقراط متدلية بالماس', 'dangling-diamond-earrings', 'أقراط متدلية أنيقة مرصعة بالألماس الطبيعي', 4500.00, 3800.00, NULL, 'ذهب أبيض عيار 18', 4.00, 12, 1),
(9, 4, 'أقراط دائرية ذهبية', 'golden-hoop-earrings', 'أقراط دائرية كلاسيكية من الذهب الأصفر', 1200.00, NULL, NULL, 'ذهب أصفر عيار 21', 7.00, 30, 0),
(10, 5, 'ساعة سويسرية فاخرة', 'luxury-swiss-watch', 'ساعة سويسرية أصلية بميناء أزرق وسوار جلدي', 15000.00, 12500.00, NULL, 'ستانلس ستيل + جلد', 80.00, 7, 1);

INSERT OR IGNORE INTO users (id, name, email, password, phone, address, is_admin) VALUES
(1, 'مدير الموقع', 'admin@jewelry.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01000000000', 'القاهرة، مصر', 1);
