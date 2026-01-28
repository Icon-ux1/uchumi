# Project Appendix: Uchumi Grocery E-commerce Platform

This appendix serves as a technical reference guide for the Uchumi Grocery E-commerce Platform, detailing the system architecture, database schema, administrative access, and deployment procedures.

## 1. System Architecture

The Uchumi Grocery platform is built on a traditional LAMP stack architecture, utilizing PHP for server-side logic and MySQL for data persistence. The system is configured for easy deployment on a local development environment such as XAMPP.

### 1.1 Technology Stack

| Component | Technology | Role |
| :--- | :--- | :--- |
| **Server-Side Language** | PHP (Hypertext Preprocessor) | Handles all business logic, database interaction, and dynamic content generation. |
| **Database** | MySQL | Stores all application data, including user accounts, product information, and order history. |
| **Frontend** | HTML5, CSS3, JavaScript | Provides the user interface and interactive elements for both the public site and the admin dashboard. |
| **Web Server** | Apache (via XAMPP) | Serves the PHP and static files to the client. |

### 1.2 Project File Structure

The core application files reside in the root directory, with a dedicated subdirectory for the newly implemented administrative interface.

| Directory/File | Description |
| :--- | :--- |
| `/` | Root directory of the website (e.g., `htdocs/uchumi`). |
| `config.php` | Central configuration file containing database connection details and core utility functions (e.g., `isLoggedIn`, `requireAdmin`, `sanitizeInput`). |
| `uchumi_database.sql` | SQL file containing the complete database schema and initial data for import. |
| `/admin` | Contains all files related to the administrative dashboard. |
| `/images/products` | Directory for storing product images, used by the new product management feature. |
| `index.php`, `product.php`, etc. | Core public-facing pages of the e-commerce site. |

## 2. Database Reference

The application utilizes a relational database named `uchumi`. The connection parameters are configured in `config.php` to use the default XAMPP settings.

| Parameter | Value |
| :--- | :--- |
| **Server Name** | `localhost` |
| **Username** | `root` |
| **Password** | `(empty)` |
| **Database Name** | `uchumi` |

### 2.1 Database Schema

The database is structured around five primary tables: `users`, `products`, `orders`, `order_items`, and `activity_logs`.

#### Table: `users` (User Accounts and Authentication)

| Column | Data Type | Description |
| :--- | :--- | :--- |
| `id` | `INT(11)` (PK, AI) | Unique identifier for the user. |
| `username` | `VARCHAR(50)` | User's login name (unique). |
| `password` | `VARCHAR(255)` | Hashed password using `PASSWORD_DEFAULT`. |
| `email` | `VARCHAR(100)` | User's email address (unique). |
| `full_name` | `VARCHAR(100)` | User's full name. |
| `user_role` | `VARCHAR(20)` | User's role (`user` or `admin`). **New column for admin functionality.** |
| `created_at` | `TIMESTAMP` | Date and time of account creation. |

#### Table: `products` (Product Catalog)

| Column | Data Type | Description |
| :--- | :--- | :--- |
| `id` | `INT(11)` (PK, AI) | Unique identifier for the product. |
| `name` | `VARCHAR(100)` | Name of the product. |
| `category` | `VARCHAR(50)` | Product category (e.g., 'Vegetables', 'Fruits'). |
| `price` | `DECIMAL(10,2)` | Selling price of the product. |
| `unit` | `VARCHAR(20)` | Unit of measure (e.g., 'kg', 'bunch'). |
| `description` | `TEXT` | Detailed product description. |
| `image_url` | `VARCHAR(255)` | Path to the product image (e.g., `images/products/tomatoes.jpg`). |
| `status` | `ENUM` | Product availability (`active` or `inactive`). |

#### Table: `orders` (Customer Orders)

| Column | Data Type | Description |
| :--- | :--- | :--- |
| `id` | `INT(11)` (PK, AI) | Unique identifier for the order. |
| `user_id` | `INT(11)` (FK) | ID of the user who placed the order (NULL for guests). |
| `total_amount` | `DECIMAL(10,2)` | Total cost of the order. |
| `status` | `ENUM` | Current status of the order (`pending`, `processing`, `shipped`, `delivered`, `cancelled`). |
| `shipping_address` | `TEXT` | Delivery address provided by the customer. |
| `created_at` | `TIMESTAMP` | Date and time the order was placed. |

#### Table: `order_items` (Items within an Order)

| Column | Data Type | Description |
| :--- | :--- | :--- |
| `id` | `INT(11)` (PK, AI) | Unique identifier for the order item. |
| `order_id` | `INT(11)` (FK) | ID of the parent order. |
| `product_id` | `INT(11)` (FK) | ID of the product ordered. |
| `quantity` | `INT(11)` | Number of units ordered. |
| `price` | `DECIMAL(10,2)` | Price of the product at the time of purchase. |

## 3. Admin Dashboard

The administrative dashboard provides a centralized interface for managing the e-commerce operations.

### 3.1 Access Credentials

The following credentials have been configured for the initial administrative account:

| Field | Value |
| :--- | :--- |
| **Admin Username** | `Icon` |
| **Admin Password** | `Icon147*` |
| **Access URL** | `http://localhost/uchumi/admin` |

### 3.2 Dashboard Functionality

The `/admin` directory contains the following key management pages:

| File | Functionality |
| :--- | :--- |
| `index.php` | Dashboard overview with key statistics (product count, user count, order count) and recent activity. |
| `products.php` | **Product Management:** CRUD (Create, Read, Update, Delete) operations for the product catalog, including image upload handling. |
| `orders.php` | **Order Management:** List of all orders with the ability to update the order status. |
| `order_details.php` | Detailed view of a specific order, including customer information and itemized list. |
| `users.php` | **User Management:** List of all registered users with the ability to change their `user_role` (e.g., promote to admin). |

## 4. Security and Best Practices

The `config.php` file includes several security enhancements to protect the application:

*   **Password Hashing:** Passwords are stored using `password_hash()` and verified with `password_verify()`, utilizing the secure `PASSWORD_DEFAULT` algorithm.
*   **Input Sanitization:** The `sanitizeInput()` function uses `trim()`, `stripslashes()`, `htmlspecialchars()`, and `mysqli_real_escape_string()` to prevent XSS and SQL injection attacks.
*   **CSRF Protection:** The `generateCSRFToken()` and `verifyCSRFToken()` functions are implemented to protect forms from Cross-Site Request Forgery.
*   **Session Security:** Session fixation is prevented by regenerating the session ID periodically, and session hijacking is mitigated by implementing an inactivity timeout.
*   **Admin Access Control:** The `requireAdmin()` function ensures that only users with the `admin` role can access the pages within the `/admin` directory.

## 5. Deployment and Maintenance Guide

### 5.1 XAMPP Deployment

1.  **Installation:** Ensure XAMPP is installed and running with Apache and MySQL services started.
2.  **File Placement:** Copy the contents of the provided project folder (e.g., `Uchumi`) into the XAMPP web root directory (`htdocs`).
3.  **Database Creation:**
    *   Access phpMyAdmin via `http://localhost/phpmyadmin`.
    *   Create a new database named **`uchumi`**.
    *   Select the new database and navigate to the **Import** tab.
    *   Upload and execute the **`uchumi_database.sql`** file.
4.  **Initial Access:** The public site is available at `http://localhost/uchumi/` and the admin dashboard at `http://localhost/uchumi/admin`.

### 5.2 Maintenance Procedures

| Task | Description | Procedure |
| :--- | :--- | :--- |
| **Backup** | Regularly back up the database and application files. | Export the `uchumi` database from phpMyAdmin and archive the project folder. |
| **Updates** | Keep the XAMPP environment (PHP, MySQL) updated to the latest stable versions. | Follow XAMPP's official update procedures. |
| **Admin Password Reset** | If the admin password is lost. | Manually update the `password` field for the `Icon` user in the `users` table with a new hash generated by a PHP password hashing utility. |
| **Product Image Uploads** | New product images are stored on the file system. | Images are saved to the `/images/products` directory. Ensure this directory has write permissions (usually handled by XAMPP). |
