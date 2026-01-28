<?php
// Include config file (session starts automatically in config.php)
include 'config.php';

// Get cart count for header
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (isset($item['quantity'])) {
            $cart_count += $item['quantity'];
        }
    }
}

// Handle contact form submission
$message_sent = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate CSRF token if not exists
    $csrf_token = generateCSRFToken();
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error_message = "Security token invalid. Please try again.";
    } else {
        // Sanitize inputs
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $subject = sanitizeInput($_POST['subject'] ?? '');
        $message = sanitizeInput($_POST['message'] ?? '');
        
        // Basic validation
        if (empty($name) || empty($email) || empty($message)) {
            $error_message = "Please fill in all required fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Please enter a valid email address.";
        } else {
            // In a real application, you would:
            // 1. Save to database
            // 2. Send email notification
            // 3. Log the contact request
            
            // For now, we'll just show success message
            $message_sent = true;
            
            // You could add database saving here:
            /*
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
            $stmt->execute();
            $stmt->close();
            */
        }
    }
}

// Generate CSRF token for the form
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Uchumi Grocery Store</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-green: #2e7d32;
            --dark-green: #1b5e20;
            --light-green: #4caf50;
            --accent-orange: #ff9800;
            --light-bg: #f9f9f9;
            --dark-text: #333333;
            --light-text: #666666;
            --white: #ffffff;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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

        /* Header (same as about.php) */
        .main-header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .logo-text h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 5px;
        }

        .logo-text span {
            color: var(--accent-orange);
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-link {
            color: var(--dark-text);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link:hover {
            color: var(--primary-green);
        }

        .cart-link {
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e53935;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 60px;
        }

        .hero-title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero-subtitle {
            font-size: 18px;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0.9;
        }

        /* Contact Grid */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-bottom: 60px;
        }

        @media (max-width: 992px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Contact Form */
        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        .form-title {
            font-size: 28px;
            color: var(--primary-green);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-text);
        }

        .required {
            color: #e53935;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary-green);
            background: white;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .form-textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn {
            padding: 14px 30px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            width: 100%;
        }

        .btn:hover {
            background: linear-gradient(135deg, var(--dark-green), var(--primary-green));
            transform: translateY(-2px);
        }

        /* Contact Info */
        .contact-info {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }

        .info-title {
            font-size: 28px;
            margin-bottom: 30px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .info-content h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .info-content p {
            opacity: 0.9;
        }

        .info-content a {
            color: white;
            text-decoration: none;
        }

        .info-content a:hover {
            color: var(--accent-orange);
        }

        /* Map Section */
        .map-section {
            margin-bottom: 60px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .map-placeholder {
            height: 300px;
            background: linear-gradient(135deg, var(--light-green), var(--primary-green));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .map-placeholder i {
            font-size: 60px;
            margin-bottom: 20px;
        }

        /* Hours Section */
        .hours-section {
            background: var(--accent-orange);
            color: white;
            padding: 40px;
            border-radius: 10px;
            margin-bottom: 60px;
            text-align: center;
        }

        .hours-title {
            font-size: 28px;
            margin-bottom: 30px;
        }

        .hours-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .hour-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        .day {
            font-weight: 600;
            margin-bottom: 8px;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: none;
        }

        .alert.show {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .alert-success {
            background: #e8f5e9;
            color: var(--primary-green);
            border: 2px solid #c8e6c9;
        }

        .alert-error {
            background: #ffebee;
            color: #e53935;
            border: 2px solid #ffcdd2;
        }

        /* Footer (same as about.php) */
        .main-footer {
            background: #1a1a1a;
            color: #999;
            padding: 60px 0 30px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-col h3 {
            color: white;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #999;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: white;
        }

        .contact-info li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .contact-info i {
            color: var(--accent-orange);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            color: white;
            font-size: 20px;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: var(--primary-green);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #333;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 20px;
            }
            
            .hero-title {
                font-size: 36px;
            }
            
            .contact-form, .contact-info, .hours-section {
                padding: 30px 20px;
            }
            
            .hours-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <a href="index.php" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <div class="logo-text">
                        <h1>Uchumi Grocery</h1>
                        <span>FRESH KENYAN PRODUCE</span>
                    </div>
                </a>

                <!-- Navigation -->
                <nav class="nav-links">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a href="product.php" class="nav-link">
                        <i class="fas fa-store"></i> Shop
                    </a>
                    <a href="about.php" class="nav-link">
                        <i class="fas fa-info-circle"></i> About
                    </a>
                    <a href="contact.php" class="nav-link">
                        <i class="fas fa-phone"></i> Contact
                    </a>
                    <a href="cart.php" class="nav-link cart-link">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <?php if($cart_count > 0): ?>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <?php if(isLoggedIn()): ?>
                        <a href="logout.php" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link">
                            <i class="fas fa-user"></i> Login
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Contact Us</h1>
            <p class="hero-subtitle">
                We're here to help! Whether you have questions about our products, 
                need assistance with your order, or want to provide feedback, 
                our team is ready to assist you.
            </p>
        </div>
    </section>

    <div class="container">
        <!-- Alert Messages -->
        <?php if($message_sent): ?>
            <div class="alert alert-success show">
                <i class="fas fa-check-circle"></i> Thank you for contacting us! We'll get back to you within 24 hours.
            </div>
        <?php elseif($error_message): ?>
            <div class="alert alert-error show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form">
                <h2 class="form-title">Send Us a Message</h2>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text" name="name" class="form-input" required 
                               placeholder="Enter your full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" name="email" class="form-input" required 
                               placeholder="your.email@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-input" 
                               placeholder="0712 345 678" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <select name="subject" class="form-input">
                            <option value="">Select a subject</option>
                            <option value="order" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'order') ? 'selected' : ''; ?>>Order Inquiry</option>
                            <option value="delivery" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'delivery') ? 'selected' : ''; ?>>Delivery Issue</option>
                            <option value="product" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'product') ? 'selected' : ''; ?>>Product Question</option>
                            <option value="feedback" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'feedback') ? 'selected' : ''; ?>>Feedback</option>
                            <option value="other" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Message <span class="required">*</span></label>
                        <textarea name="message" class="form-textarea" required placeholder="How can we help you?"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
            
            <!-- Contact Information -->
            <div class="contact-info">
                <h2 class="info-title">Contact Information</h2>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Our Location</h3>
                        <p>
                            Kahawa Wendani, Along Thika Road<br>
                            Nairobi, Kenya<br>
                            Near Wendani Market
                        </p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h3>Phone Numbers</h3>
                        <p>
                            <a href="tel:+254721234567">0721 234 567</a><br>
                            <a href="tel:+254710987654">0710 987 654</a><br>
                            <a href="tel:+254700123456">0700 123 456 (WhatsApp)</a>
                        </p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h3>Email Address</h3>
                        <p>
                            <a href="mailto:info@uchumigrocery.co.ke">info@uchumigrocery.co.ke</a><br>
                            <a href="mailto:support@uchumigrocery.co.ke">support@uchumigrocery.co.ke</a><br>
                            <a href="mailto:orders@uchumigrocery.co.ke">orders@uchumigrocery.co.ke</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Map Section -->
        <div class="map-section">
            <div class="map-placeholder">
                <i class="fas fa-map-marked-alt"></i>
                <h3>Find Us in Kahawa Wendani</h3>
                <p>Located along Thika Road, near Wendani Market. Easy access and ample parking available.</p>
            </div>
        </div>
        
        <!-- Business Hours -->
        <div class="hours-section">
            <h2 class="hours-title">Business Hours</h2>
            <div class="hours-grid">
                <div class="hour-item">
                    <div class="day">Monday - Friday</div>
                    <div>6:00 AM - 10:00 PM</div>
                </div>
                <div class="hour-item">
                    <div class="day">Saturday</div>
                    <div>6:00 AM - 10:00 PM</div>
                </div>
                <div class="hour-item">
                    <div class="day">Sunday</div>
                    <div>8:00 AM - 8:00 PM</div>
                </div>
                <div class="hour-item">
                    <div class="day">Public Holidays</div>
                    <div>9:00 AM - 6:00 PM</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>Uchumi Grocery</h3>
                    <p>Your trusted source for fresh Kenyan produce in Kahawa Wendani. Quality groceries delivered to your doorstep since 2015.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="product.php">Shop</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="cart.php">Cart</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Emergency Contacts</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-headset"></i>
                            <span>Customer Support: 0721 234 567</span>
                        </li>
                        <li>
                            <i class="fas fa-truck"></i>
                            <span>Delivery Hotline: 0710 987 654</span>
                        </li>
                        <li>
                            <i class="fab fa-whatsapp"></i>
                            <span>WhatsApp: 0700 123 456</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>Email: urgent@uchumigrocery.co.ke</span>
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
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.querySelector('input[name="name"]').value.trim();
            const email = document.querySelector('input[name="email"]').value.trim();
            const message = document.querySelector('textarea[name="message"]').value.trim();
            
            if (!name || !email || !message) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }
            
            return true;
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert.show');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>