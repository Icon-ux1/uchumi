<?php
// add_to_cart.php - Fixed version
include 'config.php';

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method!'
    ]);
    exit;
}

// Check if user is logged in (if your system requires login for cart)
if (!isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to add items to cart!',
        'redirect' => 'login.php'
    ]);
    exit;
}

// Validate product_id
if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID!'
    ]);
    exit;
}

$product_id = intval($_POST['product_id']);

// Get product details from database
try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Check if product is active/available
        if (isset($product['status']) && $product['status'] !== 'active') {
            echo json_encode([
                'success' => false,
                'message' => 'Product is not available!'
            ]);
            exit;
        }
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if adding would exceed available stock
        $current_quantity = isset($_SESSION['cart'][$product_id]) ? 
            $_SESSION['cart'][$product_id]['quantity'] : 0;
        
        if (isset($product['stock']) && ($current_quantity + 1) > $product['stock']) {
            echo json_encode([
                'success' => false,
                'message' => 'Cannot add more items. Only ' . $product['stock'] . ' available in stock!'
            ]);
            exit;
        }
        
        // Add product to cart or update quantity if already exists
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
                'image' => $product['image'] ?? 'placeholder.jpg',
                'category' => $product['category'] ?? 'Grocery',
                'discount' => $product['discount'] ?? 0
            ];
        }
        
        // Calculate updated cart count
        $cart_count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += $item['quantity'];
        }
        
        // Get the updated quantity for this specific item
        $item_quantity = $_SESSION['cart'][$product_id]['quantity'];
        
        echo json_encode([
            'success' => true,
            'cart_count' => $cart_count,
            'item_quantity' => $item_quantity,
            'product_name' => $product['name'],
            'message' => $product['name'] . ' added to cart!'
        ]);
        
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found!'
        ]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Cart Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again!'
    ]);
}

$conn->close();
?>