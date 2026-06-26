CREATE DATABASE IF NOT EXISTS jewelry_store;
USE jewelry_store;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    image VARCHAR(255),
    material VARCHAR(100),
    weight_grams DECIMAL(8,2),
    stock INT DEFAULT 0,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
    shipping_address TEXT,
    phone VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200),
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

INSERT INTO categories (name, slug, image) VALUES
('قلادات', 'necklaces', 'necklace.jpg'),
('أساور', 'bracelets', 'bracelet.jpg'),
('خواتم', 'rings', 'ring.jpg'),
('أقراط', 'earrings', 'earrings.jpg'),
('ساعات', 'watches', 'watch.jpg');

INSERT INTO products (category_id, name, slug, description, price, sale_price, image, material, weight_grams, stock, featured) VALUES
(1, 'قلادة قلب من الذهب الأبيض', 'white-gold-heart-necklace', 'قلادة أنيقة على شكل قلب مصنوعة من الذهب الأبيض عيار 18 مع حجر زركون', 2500.00, 2200.00, 'necklace-1.jpg', 'ذهب أبيض عيار 18', 8.50, 15, 1),
(1, 'قلادة ماسة نادرة', 'rare-diamond-necklace', 'قلادة فاخرة بماسة طبيعية نادرة محاطة بالألماس', 8500.00, NULL, 'necklace-2.jpg', 'ذهب أصفر عيار 21', 12.00, 5, 1),
(1, 'قلادة لؤلؤ طبيعي', 'natural-pearl-necklace', 'قلادة من اللؤلؤ الطبيعي المستورد مع مشبك ذهبي', 1800.00, 1500.00, 'necklace-3.jpg', 'لؤلؤ + ذهب', 6.00, 20, 0),
(2, 'سوار تيفاني المستوحى', 'tiffany-inspired-bracelet', 'سوار راقي بتصميم مستوحى من تيفاني مع حجر أزرق', 3200.00, NULL, 'bracelet-1.jpg', 'فضة إسترليني + ذهب', 12.00, 10, 1),
(2, 'سوار ماسي متعدد الطبقات', 'multi-layer-diamond-bracelet', 'سوار ماسي ثلاثي الطبقات فاخر', 6500.00, 5500.00, 'bracelet-2.jpg', 'ذهب وردي عيار 18', 15.00, 8, 1),
(3, 'خاتم خطوبة ألماس', 'diamond-engagement-ring', 'خاتم خطوبة فاخر بألماسة مركزية 1 قيراط', 12000.00, NULL, 'ring-1.jpg', 'ذهب أبيض عيار 18', 5.00, 3, 1),
(3, 'خاتم زفاف كلاسيكي', 'classic-wedding-band', 'خاتم زفاف كلاسيكي ناعم مناسب للأزواج', 3500.00, 3000.00, 'ring-2.jpg', 'ذهب أصفر عيار 21', 6.00, 25, 0),
(4, 'أقراط متدلية بالماس', 'dangling-diamond-earrings', 'أقراط متدلية أنيقة مرصعة بالألماس الطبيعي', 4500.00, 3800.00, 'earrings-1.jpg', 'ذهب أبيض عيار 18', 4.00, 12, 1),
(4, 'أقراط دائرية ذهبية', 'golden-hoop-earrings', 'أقراط دائرية كلاسيكية من الذهب الأصفر', 1200.00, NULL, 'earrings-2.jpg', 'ذهب أصفر عيار 21', 7.00, 30, 0),
(5, 'ساعة سويسرية فاخرة', 'luxury-swiss-watch', 'ساعة سويسرية أصلية بميناء أزرق وسوار جلدي', 15000.00, 12500.00, 'watch-1.jpg', 'ستانلس ستيل + جلد', 80.00, 7, 1);

INSERT INTO users (name, email, password, phone, address, is_admin) VALUES
('مدير الموقع', 'admin@jewelry.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01000000000', 'القاهرة، مصر', 1);
