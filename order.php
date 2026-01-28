<?php
include 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Sample orders data (in real app, fetch from database)
$orders = [
    [
        'id' => 'ORD-' . rand(1000, 9999),
        'date' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'total' => 2450.00,
        'status' => 'Delivered',
        'items' => [
            ['name' => 'Fresh Tomatoes', 'quantity' => 2, 'price' => 150.00],
            ['name' => 'Cooking Oil 2L', 'quantity' => 1, 'price' => 450.00],
            ['name' => 'Rice 5kg', 'quantity' => 1, 'price' => 800.00]
        ]
    ],
    [
        'id' => 'ORD-' . rand(1000, 9999),
        'date' => date('Y-m-d H:i:s', strtotime('-1 week')),
        'total' => 1250.00,
        'status' => 'Delivered',
        'items' => [
            ['name' => 'Sugar 2kg', 'quantity' => 1, 'price' => 250.00],
            ['name' => 'Wheat Flour 2kg', 'quantity' => 2, 'price' => 300.00]
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Uchumi Grocery</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary-green: #2e7d32;
            --dark-green: #1b5e20;
            --light-green: #4caf50;
            --accent-orange: #ff9800;
            --accent-red: #e53935;
            --light-bg: #f9f9f9;
            --dark-text: #333333;
            --light-text: #666666;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --shadow-light: 0 5px 15px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark-text);
            background: var(--light-bg);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles (same as checkout) */
        .announcement-bar {
            background: linear-gradient(90deg, var(--primary-green), var(--light-green));
            color: var(--white);
            padding: 12px 0;
            font-size: 14px;
        }

        .announcement-bar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .announcement-text {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .offer-tag {
            background: var(--accent-red);
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .main-header {
            background: var(--white);
            box-shadow: var(--shadow-light);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo i {
            font-size: 28px;
            color: var(--white);
        }

        .logo-text h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .action-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: var(--dark-text);
            position: relative;
            transition: var(--transition);
        }

        .action-item:hover {
            color: var(--primary-green);
        }

        .action-icon {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--accent-red);
            color: var(--white);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Orders Page */
        .orders-page {
            padding: 60px 0;
            min-height: 70vh;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-green);
            position: relative;
            padding-bottom: 15px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--accent-orange);
            border-radius: 2px;
        }

        .page-subtitle {
            color: var(--light-text);
            font-size: 16px;
            margin-top: 10px;
        }

        /* Orders List */
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .order-card {
            background: var(--white);
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .order-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-5px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .order-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .order-id {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-green);
        }

        .order-date {
            color: var(--light-text);
            font-size: 14px;
        }

        .order-status {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .status-delivered {
            background: #e8f5e9;
            color: var(--primary-green);
        }

        .status-pending {
            background: #fff3e0;
            color: #ff9800;
        }

        .status-processing {
            background: #e3f2fd;
            color: #2196f3;
        }

        .order-details {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        @media (max-width: 768px) {
            .order-details {
                grid-template-columns: 1fr;
            }
        }

        .order-items {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .order-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--light-green), var(--primary-green));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .item-quantity {
            color: var(--light-text);
            font-size: 14px;
        }

        .item-price {
            font-weight: 600;
            color: var(--primary-green);
        }

        .order-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            height: fit-content;
        }

        .summary-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark-text);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .summary-label {
            color: var(--light-text);
        }

        .summary-value {
            font-weight: 600;
        }

        .summary-total {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-green);
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
        }

        .order-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-green);
            border: 2px solid var(--primary-green);
        }

        .btn-outline:hover {
            background: var(--primary-green);
            color: var(--white);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: var(--white);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--dark-green), var(--primary-green));
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow-light);
        }

        .empty-icon {
            font-size: 80px;
            color: #e0e0e0;
            margin-bottom: 20px;
        }

        .empty-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--light-text);
            margin-bottom: 15px;
        }

        .empty-text {
            color: var(--light-text);
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer (same as checkout) */
        .main-footer {
            background: #1a1a1a;
            color: var(--white);
            padding: 60px 0 30px;
            margin-top: 60px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-bottom {
            padding-top: 30px;
            border-top: 1px solid #333;
            text-align: center;
            color: #999;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-title {
                font-size: 28px;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .order-status {
                align-self: flex-start;
            }
            
            .order-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <div class="container">
            <div class="announcement-text">
                <i class="fas fa-truck"></i>
                <span>Free delivery in Kahawa Wendani for orders above KSh 1,000</span>
                <span class="offer-tag">NEW OFFER</span>
            </div>
            <div class="announcement-text">
                <i class="fas fa-phone"></i>
                <span>Call us: 0712 345 678</span>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-container">
                <!-- Logo -->
                <div class="logo-section">
                    <div class="logo">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <div class="logo-text">
                        <h1>Uchumi Grocery</h1>
                        <p class="logo-tagline" style="color: var(--accent-orange); font-size: 14px; font-weight: 500; letter-spacing: 1px;">FRESH KENYAN PRODUCE</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="header-actions">
                    <a href="index.php" class="action-item">
                        <i class="fas fa-home action-icon"></i>
                        <span class="action-text">Home</span>
                    </a>
                    <a href="product.php" class="action-item">
                        <i class="fas fa-store action-icon"></i>
                        <span class="action-text">Shop</span>
                    </a>
                    <a href="cart.php" class="action-item">
                        <i class="fas fa-shopping-cart action-icon"></i>
                        <span class="action-text">Cart</span>
                        <span class="cart-count">0</span>
                    </a>
                    <a href="order.php" class="action-item">
                        <i class="fas fa-box action-icon"></i>
                        <span class="action-text">Orders</span>
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="logout.php" class="action-item">
                            <i class="fas fa-sign-out-alt action-icon"></i>
                            <span class="action-text">Logout</span>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="action-item">
                            <i class="fas fa-user action-icon"></i>
                            <span class="action-text">Login</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Orders Page -->
    <section class="orders-page">
        <div class="container">
            <div class="page-header animate__animated animate__fadeIn">
                <h1 class="page-title">My Orders</h1>
                <p class="page-subtitle">Track and manage your recent purchases</p>
            </div>

            <?php if (empty($orders)): ?>
                <!-- Empty State -->
                <div class="empty-state animate__animated animate__fadeIn">
                    <div class="empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3 class="empty-title">No orders yet</h3>
                    <p class="empty-text">
                        You haven't placed any orders yet. Start shopping to fill your cart and place your first order!
                    </p>
                    <a href="product.php" class="btn btn-primary">
                        <i class="fas fa-store"></i> Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <!-- Orders List -->
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card animate__animated animate__fadeIn">
                            <div class="order-header">
                                <div class="order-info">
                                    <div class="order-id"><?php echo $order['id']; ?></div>
                                    <div class="order-date">
                                        <i class="far fa-calendar"></i>
                                        <?php echo date('F j, Y, g:i a', strtotime($order['date'])); ?>
                                    </div>
                                </div>
                                <div class="order-status status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo $order['status']; ?>
                                </div>
                            </div>

                            <div class="order-details">
                                <div class="order-items">
                                    <h4 style="margin-bottom: 15px; color: var(--dark-text);">Order Items</h4>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div class="order-item">
                                            <div class="item-image">
                                                <i class="fas fa-shopping-bag"></i>
                                            </div>
                                            <div class="item-info">
                                                <div class="item-name"><?php echo $item['name']; ?></div>
                                                <div class="item-quantity">Quantity: <?php echo $item['quantity']; ?></div>
                                            </div>
                                            <div class="item-price">
                                                KSh <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="order-summary">
                                    <h4 class="summary-title">Order Summary</h4>
                                    <div class="summary-row">
                                        <span class="summary-label">Subtotal:</span>
                                        <span class="summary-value">KSh <?php echo number_format($order['total'] - 200 - ($order['total'] * 0.16), 2); ?></span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Delivery:</span>
                                        <span class="summary-value">KSh 200.00</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">Tax (16%):</span>
                                        <span class="summary-value">KSh <?php echo number_format(($order['total'] - 200) * 0.16, 2); ?></span>
                                    </div>
                                    <div class="summary-row summary-total">
                                        <span class="summary-label">Total:</span>
                                        <span class="summary-value">KSh <?php echo number_format($order['total'], 2); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="order-actions">
                                <a href="#" class="btn btn-outline">
                                    <i class="fas fa-redo"></i> Reorder
                                </a>
                                <a href="#" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download Invoice
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-shopping-basket"></i>
                        <h2>Uchumi Grocery</h2>
                    </div>
                    <p class="footer-description">
                        Your trusted source for fresh Kenyan produce in Kahawa Wendani. 
                        Quality groceries delivered to your doorstep.
                    </p>
                </div>
                
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul class="footer-links" style="list-style: none;">
                        <li><a href="index.php" style="color: #999; text-decoration: none; transition: var(--transition); display: block; padding: 5px 0;">
                            <i class="fas fa-chevron-right" style="margin-right: 10px;"></i> Home
                        </a></li>
                        <li><a href="product.php" style="color: #999; text-decoration: none; transition: var(--transition); display: block; padding: 5px 0;">
                            <i class="fas fa-chevron-right" style="margin-right: 10px;"></i> Products
                        </a></li>
                        <li><a href="order.php" style="color: #999; text-decoration: none; transition: var(--transition); display: block; padding: 5px 0;">
                            <i class="fas fa-chevron-right" style="margin-right: 10px;"></i> My Orders
                        </a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Contact Info</h3>
                    <ul class="contact-info" style="list-style: none;">
                        <li style="margin-bottom: 15px;">
                            <i class="fas fa-map-marker-alt" style="color: var(--accent-orange); margin-right: 10px;"></i>
                            <span>Kahawa Wendani, Nairobi</span>
                        </li>
                        <li style="margin-bottom: 15px;">
                            <i class="fas fa-phone" style="color: var(--accent-orange); margin-right: 10px;"></i>
                            <span>0712 345 678</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Uchumi Grocery Store, Kahawa Wendani. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Add animation to order cards
        document.addEventListener('DOMContentLoaded', function() {
            const orderCards = document.querySelectorAll('.order-card');
            orderCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html>