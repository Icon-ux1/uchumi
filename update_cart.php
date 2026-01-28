<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        exit;
    }
    
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    // Validate quantity
    if ($quantity < 1 || $quantity > 99) {
        echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
        exit;
    }
    
    // Check if product exists in database
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    $product = $result->fetch_assoc();
    
    // Check stock if available
    if (isset($product['stock']) && $quantity > $product['stock']) {
        echo json_encode([
            'success' => false, 
            'message' => 'Only ' . $product['stock'] . ' items available in stock',
            'original_quantity' => $_SESSION['cart'][$product_id]['quantity'] ?? 1
        ]);
        exit;
    }
    
    // Update cart session
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    }
    
    // Calculate cart totals
    $cart_total = 0;
    $cart_count = 0;
    $item_count = 0;
    
    foreach ($_SESSION['cart'] as $id => $item) {
        $item_price = $item['price'];
        if (isset($item['discount']) && $item['discount'] > 0) {
            $item_price = $item['price'] * (1 - $item['discount']/100);
        }
        $cart_total += $item_price * $item['quantity'];
        $cart_count += $item['quantity'];
        $item_count++;
    }
    
    echo json_encode([
        'success' => true,
        'cart_total' => $cart_total,
        'cart_count' => $cart_count,
        'item_count' => $item_count,
        'message' => 'Cart updated successfully'
    ]);
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>