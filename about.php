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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Uchumi Grocery Store</title>
    
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

        /* Header */
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
                        url('https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
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
            margin: 0 auto 30px;
            opacity: 0.9;
        }

        /* Content Sections */
        .section {
            padding: 60px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-size: 36px;
            color: var(--primary-green);
            margin-bottom: 15px;
        }

        .section-title p {
            color: var(--light-text);
            max-width: 700px;
            margin: 0 auto;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 28px;
        }

        .card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--dark-text);
        }

        /* Team Section */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .team-member {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .member-image {
            height: 200px;
            background: linear-gradient(135deg, var(--light-green), var(--primary-green));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }

        .member-info {
            padding: 25px;
        }

        .member-name {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .member-role {
            color: var(--primary-green);
            font-weight: 500;
            margin-bottom: 15px;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-top: 40px;
        }

        .stat-item h3 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--accent-orange);
        }

        /* Footer */
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
            
            .section-title h2 {
                font-size: 28px;
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
            <h1 class="hero-title">About Uchumi Grocery</h1>
            <p class="hero-subtitle">
                Serving Kahawa Wendani with fresh, quality groceries since 2015. 
                We're your trusted neighborhood grocery store committed to bringing 
                the best Kenyan produce to your doorstep.
            </p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Our Mission & Values</h2>
                <p>Driven by quality, community, and convenience</p>
            </div>
            
            <div class="content-grid">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h3>Freshness Guaranteed</h3>
                    <p>We work directly with local farmers to bring you the freshest produce within 24 hours of harvest.</p>
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Community First</h3>
                    <p>Supporting local farmers and businesses is at our core. We believe in growing together with our community.</p>
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Convenience Delivered</h3>
                    <p>Quality groceries delivered to your doorstep within 2 hours. We make grocery shopping hassle-free.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="section" style="background: #f5f5f5;">
        <div class="container">
            <div class="section-title">
                <h2>Our Story</h2>
                <p>From humble beginnings to community favorite</p>
            </div>
            
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <p style="font-size: 18px; line-height: 1.8; margin-bottom: 30px;">
                    Uchumi Grocery started as a small family-run shop in 2015 with a simple mission: 
                    to provide fresh, affordable groceries to our Kahawa Wendani neighbors. What began 
                    with just a few shelves of basic essentials has grown into a trusted community hub 
                    serving thousands of families.
                </p>
                <p style="font-size: 18px; line-height: 1.8;">
                    Today, while we've grown in size and scope, we remain true to our roots. 
                    We're still family-owned, still community-focused, and still passionate 
                    about bringing you the best Kenyan produce.
                </p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="section-title">
                <h2>Our Impact</h2>
                <p>Numbers that tell our story</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>8+</h3>
                    <p>Years Serving</p>
                </div>
                <div class="stat-item">
                    <h3>5,000+</h3>
                    <p>Happy Customers</p>
                </div>
                <div class="stat-item">
                    <h3>200+</h3>
                    <p>Local Products</p>
                </div>
                <div class="stat-item">
                    <h3>24/7</h3>
                    <p>Support</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Meet Our Team</h2>
                <p>The passionate people behind Uchumi Grocery</p>
            </div>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">David Kamau</h3>
                        <p class="member-role">Founder & CEO</p>
                        <p>With 15 years in the grocery industry, David started Uchumi to bring quality produce to his neighborhood.</p>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-image">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Sarah Mwangi</h3>
                        <p class="member-role">Quality Control</p>
                        <p>Ensures every product meets our high standards before it reaches our customers.</p>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-image">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">John Ochieng</h3>
                        <p class="member-role">Operations Manager</p>
                        <p>Manages our delivery fleet and ensures timely deliveries across Kahawa Wendani.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                    <h3>Contact Info</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Kahawa Wendani, Nairobi</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>0721 234 567 | 0710 987 654</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>info@uchumigrocery.co.ke</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Mon-Sat: 6AM - 10PM</span>
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
        // Simple animation for stats counter
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.stat-item h3');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent);
                let current = 0;
                const increment = target / 50;
                
                const updateCounter = () => {
                    if (current < target) {
                        current += increment;
                        counter.textContent = Math.ceil(current) + (counter.textContent.includes('+') ? '+' : '');
                        setTimeout(updateCounter, 20);
                    } else {
                        counter.textContent = target + (counter.textContent.includes('+') ? '+' : '');
                    }
                };
                
                // Start counter when section is visible
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCounter();
                            observer.unobserve(entry.target);
                        }
                    });
                });
                
                observer.observe(counter.closest('.stats-section'));
            });
        });
    </script>
</body>
</html>
