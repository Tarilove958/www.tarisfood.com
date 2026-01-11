<?php
/**
 * Configuration File
 * Contains database connection, site settings, and global configurations
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Africa/Lagos');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'food_website');

// Site Configuration
define('SITE_URL', 'http://localhost/food_website');
define('SITE_NAME', 'Food Brand');
define('SITE_EMAIL', 'info@Tarisfood.com');
define('ADMIN_EMAIL', 'admin@foodbrand.com');

// File Upload Configuration
define('UPLOAD_PATH', __DIR__ . '/../assets/images/uploads/');
define('PRODUCT_IMAGE_PATH', __DIR__ . '/../assets/images/products/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']);

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 20);
define('TESTIMONIALS_PER_PAGE', 10);

// Currency
define('CURRENCY', 'NGN');
define('CURRENCY_SYMBOL', '₦');

// Payment Gateway Configuration
// Paystack
define('PAYSTACK_PUBLIC_KEY', 'pk_test_xxxxxxxxxxxxxxxxxxxx');
define('PAYSTACK_SECRET_KEY', 'sk_test_xxxxxxxxxxxxxxxxxxxx');
define('PAYSTACK_CALLBACK_URL', SITE_URL . '/payment-callback.php');

// Flutterwave
define('FLUTTERWAVE_PUBLIC_KEY', 'FLWPUBK_TEST-xxxxxxxxxxxxxxxxxxxx');
define('FLUTTERWAVE_SECRET_KEY', 'FLWSECK_TEST-xxxxxxxxxxxxxxxxxxxx');
define('FLUTTERWAVE_ENCRYPTION_KEY', 'FLWSECK_TESTxxxxxxxxxxxx');
define('FLUTTERWAVE_CALLBACK_URL', SITE_URL . '/payment-callback.php');

// Security
define('PASSWORD_HASH_COST', 12);
define('SESSION_LIFETIME', 3600); // 1 hour

// Database Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage());
}

// PDO Connection (Alternative for prepared statements)
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("PDO Connection Error: " . $e->getMessage());
}

/**
 * Get active/current theme
 * Returns the user's selected theme or falls back to default
 * Priority: Active Global Theme > User Preference > Session > Cookie > Default
 */
if (!function_exists('getActiveTheme')) {
    function getActiveTheme() {
        global $conn;
        
        // PRIMARY: Get the globally active theme (set by admin) - THIS IS THE DEFAULT FOR EVERYONE
        $sql = "SELECT * FROM themes WHERE is_active = TRUE LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $activeTheme = $result->fetch_assoc();
            
            // SECONDARY: Check if logged-in user has a personal preference override
            if (isLoggedIn()) {
                $user_id = $_SESSION['user_id'];
                $sql = "SELECT * FROM theme_user_preferences WHERE user_id = ? LIMIT 1";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $pref = $result->fetch_assoc();
                        $theme_sql = "SELECT * FROM themes WHERE theme_id = ? LIMIT 1";
                        $theme_stmt = $conn->prepare($theme_sql);
                        if ($theme_stmt) {
                            $theme_stmt->bind_param("i", $pref['theme_id']);
                            $theme_stmt->execute();
                            $theme_result = $theme_stmt->get_result();
                            if ($theme_result->num_rows > 0) {
                                return $theme_result->fetch_assoc();
                            }
                        }
                    }
                }
            }
            
            // Return the global active theme
            return $activeTheme;
        }
        
        // Check session for guest theme preference
        if (isset($_SESSION['theme_id'])) {
            $theme_id = $_SESSION['theme_id'];
            $sql = "SELECT * FROM themes WHERE theme_id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $theme_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    return $result->fetch_assoc();
                }
            }
        }
        
        // Check cookie
        if (isset($_COOKIE['theme_id'])) {
            $theme_id = (int)$_COOKIE['theme_id'];
            $sql = "SELECT * FROM themes WHERE theme_id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $theme_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    return $result->fetch_assoc();
                }
            }
        }
        
        // Fall back to default theme
        $sql = "SELECT * FROM themes WHERE is_default = TRUE LIMIT 1";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        // Last resort - return a basic default theme array
        return [
            'theme_id' => 1,
            'theme_name' => 'Default Theme',
            'primary_color' => '#3b82f6',
            'secondary_color' => '#f97316',
            'dark_color' => '#1f2937',
            'light_color' => '#f9fafb',
        ];
    }
}

/**
 * Apply global theme activation
 * Called by admin when changing the website theme
 * This ensures all users see the new theme
 */
if (!function_exists('applyGlobalTheme')) {
    function applyGlobalTheme($theme_id) {
        global $conn;
        
        // Verify theme exists
        $sql = "SELECT * FROM themes WHERE theme_id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $theme_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return false;
        }
        
        // Deactivate all themes
        $sql = "UPDATE themes SET is_active = FALSE, is_default = FALSE";
        $conn->query($sql);
        
        // Activate the selected theme
        $sql = "UPDATE themes SET is_active = TRUE, is_default = TRUE WHERE theme_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $theme_id);
        
        return $stmt->execute();
    }
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $conn->real_escape_string($data);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

/**
 * Check if current page is in user path
 */
function isUserPath() {
    return strpos($_SERVER['PHP_SELF'], '/user/') !== false;
}

/**
 * Redirect to a specific page
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

/**
 * Check if flash message exists
 */
function hasFlashMessage() {
    return isset($_SESSION['flash_message']) && !empty($_SESSION['flash_message']);
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Format price with currency symbol
 */
function formatPrice($amount) {
    // Handle null or empty values
    if ($amount === null || $amount === '' || !is_numeric($amount)) {
        return CURRENCY_SYMBOL . '0.00';
    }
    return CURRENCY_SYMBOL . number_format(floatval($amount), 2);
}

/**
 * Generate unique order number
 */
function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

/**
 * Generate session ID for guest users
 */
function getSessionId() {
    if (!isset($_SESSION['guest_session_id'])) {
        $_SESSION['guest_session_id'] = uniqid('guest_', true);
    }
    return $_SESSION['guest_session_id'];
}

/**
 * Get cart count
 */
function getCartCount() {
    global $pdo;
    
    try {
        if (isLoggedIn()) {
            $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE session_id = ?");
            $stmt->execute([getSessionId()]);
        }
        
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Calculate cart total
 */
function getCartTotal() {
    global $pdo;
    
    try {
        if (isLoggedIn()) {
            $stmt = $pdo->prepare("
                SELECT SUM(c.quantity * p.price) as total 
                FROM cart c 
                JOIN products p ON c.product_id = p.product_id 
                WHERE c.user_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("
                SELECT SUM(c.quantity * p.price) as total 
                FROM cart c 
                JOIN products p ON c.product_id = p.product_id 
                WHERE c.session_id = ?
            ");
            $stmt->execute([getSessionId()]);
        }
        
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Upload image file
 */
function uploadImage($file, $targetDir = PRODUCT_IMAGE_PATH) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload'];
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error occurred'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds limit'];
    }
    
    // Check file type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $targetPath = $targetDir . $filename;
    
    // Create directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

/**
 * Delete image file
 */
function deleteImage($filename, $directory = PRODUCT_IMAGE_PATH) {
    $filePath = $directory . $filename;
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    return false;
}

/**
 * Get site setting from database
 */
function getSiteSetting($key, $default = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

/**
 * Log activity (for admin actions)
 */
function logActivity($action, $description, $user_id = null) {
    global $pdo;
    
    if ($user_id === null && isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, action, description, ip_address, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $action, $description, $_SERVER['REMOTE_ADDR']]);
    } catch (PDOException $e) {
        // Silently fail - logging shouldn't break the application
    }
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token input field
 */
function getCSRFInput() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Adjust brightness of a hex color
 * @param string $hex Hex color (with or without #)
 * @param int $percent Percentage to adjust (-100 to 100)
 * @return string Adjusted hex color
 */
function adjustBrightness($hex, $percent) {
    // Remove # if present
    $hex = str_replace('#', '', $hex);
    
    // Convert to RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Adjust brightness
    $r = intval($r * (1 + $percent / 100));
    $g = intval($g * (1 + $percent / 100));
    $b = intval($b * (1 + $percent / 100));
    
    // Clamp values to 0-255
    $r = max(0, min(255, $r));
    $g = max(0, min(255, $g));
    $b = max(0, min(255, $b));
    
    // Convert back to hex
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . 
                 str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . 
                 str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

// Generate CSRF token for the session
generateCSRFToken();

// Set default timezone for the application
ini_set('date.timezone', 'Africa/Lagos');
?>