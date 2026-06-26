<?php
function getCartCount() {
    return isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
}

function getCartTotal() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) return 0;
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $price = isset($item['sale_price']) && $item['sale_price'] > 0 ? $item['sale_price'] : $item['price'];
        $total += $price * $item['quantity'];
    }
    return $total;
}

function addToCart($product) {
    $id = $product['id'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        $_SESSION['cart'][$id] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'sale_price' => $product['sale_price'],
            'image' => $product['image'],
            'quantity' => 1
        ];
    }
}

function updateCartQuantity($id, $quantity) {
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$id]);
    } else {
        $_SESSION['cart'][$id]['quantity'] = $quantity;
    }
}

function removeFromCart($id) {
    unset($_SESSION['cart'][$id]);
}

function formatPrice($price) {
    return number_format($price, 2) . ' ج.م';
}

function getProductImage($image) {
    if ($image && file_exists("uploads/products/$image")) {
        return "uploads/products/$image";
    }
    return 'https://placehold.co/600x600/1a1a2e/FFD700?text=Jewelry';
}

function truncateText($text, $limit = 100) {
    if (mb_strlen($text) > $limit) {
        return mb_substr($text, 0, $limit) . '...';
    }
    return $text;
}
