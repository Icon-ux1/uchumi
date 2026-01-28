<?php
// config.php - Fixed version with session check

// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uchumi";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8mb4");

// Initialize cart session with proper structure
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Initialize other session variables if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        // Store current page for redirect after login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit();
    }
}

// Security: Prevent session fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    // Regenerate session ID every 30 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Error reporting (for development - disable in production)
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', 'php-errors.log');
}

// Set timezone
date_default_timezone_set('Africa/Nairobi');

// Helper function for password hashing
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Helper function for verifying password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Helper function for input sanitization
function sanitizeInput($data) {
    global $conn;
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $conn->real_escape_string($data);
}

// Helper function for output escaping
function escapeOutput($data) {
    if (is_array($data)) {
        return array_map('escapeOutput', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// CSRF protection functions
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Function to get user information
function getUserInfo($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, username, email, full_name, phone, address, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Cart helper functions
function getCartCount() {
    $count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            if (is_array($item) && isset($item['quantity'])) {
                $count += $item['quantity'];
            }
        }
    }
    return $count;
}

function getCartTotal() {
    $total = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            if (is_array($item) && isset($item['quantity']) && isset($item['price'])) {
                $total += $item['quantity'] * $item['price'];
            }
        }
    }
    return $total;
}

// Function to validate product ID
function validateProductId($product_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM products WHERE id = ? AND status = 'active'");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Function to get product details
function getProductDetails($product_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to send JSON response
function jsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Function to require admin access
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

// Function to log errors/activities
function logActivity($action, $details = '') {
    global $conn;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $action, $details, $ip_address, $user_agent);
    $stmt->execute();
}

// Check if maintenance mode is enabled (you can set this in a settings table)
define('MAINTENANCE_MODE', false);

if (MAINTENANCE_MODE && !isset($_SESSION['admin'])) {
    header('HTTP/1.1 503 Service Unavailable');
    include 'maintenance.php';
    exit();
}

// Add Content Security Policy header
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data: https: http:;");

// Prevent clickjacking
header('X-Frame-Options: SAMEORIGIN');

// Prevent MIME type sniffing
header('X-Content-Type-Options: nosniff');

// Enable XSS protection
header('X-XSS-Protection: 1; mode=block');

// Database table creation check (optional - for first-time setup)
function checkDatabaseTables() {
    global $conn;
    
    $tables = ['users', 'products', 'categories', 'orders', 'order_items'];
    
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows == 0) {
            // Table doesn't exist - you could create it here or show an error
            return false;
        }
    }
    return true;
}

// Optional: Check database on first load
if (!isset($_SESSION['db_checked'])) {
    if (!checkDatabaseTables()) {
        // Redirect to setup page or show error
        // header("Location: setup.php");
        // exit();
    }
    $_SESSION['db_checked'] = true;
}

// Function to format currency
function formatCurrency($amount) {
    return 'KSh ' . number_format($amount, 2);
}

// Function to calculate delivery fee
function calculateDeliveryFee($subtotal) {
    return $subtotal >= 1000 ? 0 : 200;
}

// Function to calculate tax
function calculateTax($subtotal, $tax_rate = 0.16) {
    return $subtotal * $tax_rate;
}

// Function to get total with fees
function calculateTotal($subtotal) {
    $delivery = calculateDeliveryFee($subtotal);
    $tax = calculateTax($subtotal);
    return $subtotal + $delivery + $tax;
}

// Auto logout after 30 minutes of inactivity
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset();
    session_destroy();
    // Start a new session if needed
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
$_SESSION['LAST_ACTIVITY'] = time();

// Regenerate session ID every 5 minutes
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} elseif (time() - $_SESSION['CREATED'] > 300) {
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}
?>
