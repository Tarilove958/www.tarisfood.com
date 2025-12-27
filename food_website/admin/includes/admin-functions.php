<?php
/**
 * Admin Functions
 * Functions needed for admin dashboard and management pages
 */

// Ensure DB connection is available
// Load the main config from root includes folder
if (!defined('SITE_NAME')) {
    require_once dirname(dirname(__DIR__)) . '/includes/config.php';
}

/* -------------------------------------------------------------------------- */
/* Helper Functions (With Safety Checks)                                      */
/* -------------------------------------------------------------------------- */

// 1. Sanitize Inputs
if (!function_exists('sanitize')) {
    function sanitize($data) {
        global $conn;
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        // Check if connection exists before escaping
        if($conn) {
            return mysqli_real_escape_string($conn, $data);
        }
        return $data;
    }
}

// 2. Redirect Helper
if (!function_exists('redirect')) {
    function redirect($url) {
        if (!headers_sent()) {
            header("Location: " . $url);
            exit();
        } else {
            echo '<script>window.location.href="' . $url . '";</script>';
            exit();
        }
    }
}

// 3. Format Currency
if (!function_exists('formatPrice')) {
    function formatPrice($price) {
        return '₦' . number_format($price, 2);
    }
}

// 4. Check Login Status
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

// 5. Check Admin Status
if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
    }
}

// 6. Flash Message System
if (!function_exists('setFlashMessage')) {
    function setFlashMessage($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

if (!function_exists('getFlashMessage')) {
    function getFlashMessage() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}

// 7. Email Validation (The one causing your error!)
if (!function_exists('isValidEmail')) {
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

// 8. Time Ago
if (!function_exists('timeAgo')) {
    function timeAgo($datetime) {
        $timestamp = strtotime($datetime);
        $difference = time() - $timestamp;
        
        $periods = [
            'year' => 31536000, 'month' => 2592000, 'week' => 604800,
            'day' => 86400, 'hour' => 3600, 'minute' => 60, 'second' => 1
        ];
        
        foreach ($periods as $key => $value) {
            if ($difference >= $value) {
                $time = floor($difference / $value);
                return $time . ' ' . $key . ($time > 1 ? 's' : '') . ' ago';
            }
        }
        return 'Just now';
    }
}

/* -------------------------------------------------------------------------- */
/* Cart Functions                                                             */
/* -------------------------------------------------------------------------- */

if (!function_exists('getCartCount')) {
    function getCartCount() {
        global $conn;
        if (!isset($conn) || !$conn) return 0;

        $count = 0;
        if (isLoggedIn()) {
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
        } else {
            $session_id = session_id();
            $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE session_id = ?");
            $stmt->bind_param("s", $session_id);
        }
        
        if ($stmt && $stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count = $row['total'] ?? 0;
        }
        return $count;
    }
}

if (!function_exists('getCartTotal')) {
    function getCartTotal() {
        global $conn;
        if (!isset($conn) || !$conn) return 0;
        
        $total = 0;
        if (isLoggedIn()) {
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT c.quantity, p.price, p.discount_price 
                    FROM cart c 
                    JOIN products p ON c.product_id = p.product_id 
                    WHERE c.user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
        } else {
            $session_id = session_id();
            $sql = "SELECT c.quantity, p.price, p.discount_price 
                    FROM cart c 
                    JOIN products p ON c.product_id = p.product_id 
                    WHERE c.session_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $session_id);
        }
        
        if ($stmt && $stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $price = $row['discount_price'] > 0 ? $row['discount_price'] : $row['price'];
                $total += ($price * $row['quantity']);
            }
        }
        return $total;
    }
}

/* -------------------------------------------------------------------------- */
/* Admin Dashboard Functions                                                  */
/* -------------------------------------------------------------------------- */

if (!function_exists('getDashboardStats')) {
    function getDashboardStats() {
        global $conn;
        
        $stats = [
            'revenue' => 0,
            'orders' => 0,
            'products' => 0,
            'customers' => 0
        ];
        
        if (!$conn) return $stats;
        
        // Total Revenue
        $result = $conn->query("SELECT SUM(total_amount) as revenue FROM orders WHERE order_status = 'delivered'");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['revenue'] = $row['revenue'] ?? 0;
        }
        
        // Total Orders
        $result = $conn->query("SELECT COUNT(order_id) as total FROM orders");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['orders'] = $row['total'] ?? 0;
        }
        
        // Total Products
        $result = $conn->query("SELECT COUNT(product_id) as total FROM products");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['products'] = $row['total'] ?? 0;
        }
        
        // Total Customers
        $result = $conn->query("SELECT COUNT(user_id) as total FROM users WHERE user_type = 'customer'");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['customers'] = $row['total'] ?? 0;
        }
        
        return $stats;
    }
}

if (!function_exists('getRecentOrders')) {
    function getRecentOrders($limit = 5) {
        global $conn;
        
        if (!$conn) return [];
        
        $limit = (int)$limit;
        $result = $conn->query("
            SELECT o.*, u.full_name, u.email 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.user_id 
            ORDER BY o.order_date DESC 
            LIMIT $limit
        ");
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

if (!function_exists('getAllUsers')) {
    function getAllUsers($limit = null) {
        global $conn;
        
        if (!$conn) return [];
        
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $result = $conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

if (!function_exists('getAllProducts')) {
    function getAllProducts() {
        global $conn;
        
        if (!$conn) return [];
        
        $result = $conn->query("
            SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            ORDER BY p.created_at DESC
        ");
        
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

if (!function_exists('getAllCategories')) {
    function getAllCategories() {
        global $conn;
        
        if (!$conn) return [];
        
        $result = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

if (!function_exists('getCategoryById')) {
    function getCategoryById($category_id) {
        global $conn;
        
        if (!$conn) return null;
        
        $category_id = (int)$category_id;
        $result = $conn->query("SELECT * FROM categories WHERE category_id = $category_id LIMIT 1");
        return $result ? $result->fetch_assoc() : null;
    }
}

if (!function_exists('getProductById')) {
    function getProductById($product_id) {
        global $conn;
        
        if (!$conn) return null;
        
        $product_id = (int)$product_id;
        $result = $conn->query("
            SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id = $product_id LIMIT 1
        ");
        return $result ? $result->fetch_assoc() : null;
    }
}

if (!function_exists('getUserById')) {
    function getUserById($user_id) {
        global $conn;
        
        if (!$conn) return null;
        
        $user_id = (int)$user_id;
        $result = $conn->query("SELECT * FROM users WHERE user_id = $user_id LIMIT 1");
        return $result ? $result->fetch_assoc() : null;
    }
}

if (!function_exists('getOrderById')) {
    function getOrderById($order_id) {
        global $conn;
        
        if (!$conn) return null;
        
        $order_id = (int)$order_id;
        $result = $conn->query("
            SELECT o.*, u.full_name, u.email, u.phone 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.user_id 
            WHERE o.order_id = $order_id LIMIT 1
        ");
        return $result ? $result->fetch_assoc() : null;
    }
}

if (!function_exists('getOrderItems')) {
    function getOrderItems($order_id) {
        global $conn;
        
        if (!$conn) return [];
        
        $order_id = (int)$order_id;
        $result = $conn->query("SELECT * FROM order_items WHERE order_id = $order_id");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>