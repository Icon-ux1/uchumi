<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing product ID']);
        exit;
    }
    
    $product_id = intval($_POST['id']);
    
    // Remove from cart session
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    
    // Calculate cart totals after removal
    $cart_total = 0;
    $cart_count = 0;
    $item_count = 0;
    
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $id => $item) {
            $item_price = $item['price'];
            if (isset($item['discount']) && $item['discount'] > 0) {
                $item_price = $item['price'] * (1 - $item['discount']/100);
            }
            $cart_total += $item_price * $item['quantity'];
            $cart_count += $item['quantity'];
            $item_count++;
        }
    }
    
    echo json_encode([
        'success' => true,
        'cart_total' => $cart_total,
        'cart_count' => $cart_count,
        'item_count' => $item_count,
        'message' => 'Item removed from cart'
    ]);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>