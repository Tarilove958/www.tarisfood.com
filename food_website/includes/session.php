<?php
/**
 * Session Management and Security
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('Direct access not allowed');
}

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Lax');

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Check if session has timed out
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        return true;
    }
    $_SESSION['last_activity'] = time();
    return false;
}

// Regenerate session ID periodically
function regenerateSession() {
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 300) { // 5 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// Validate session
function validateSession() {
    if (isLoggedIn()) {
        // Check if user still exists in database
        global $pdo;
        $stmt = $pdo->prepare("SELECT user_id, status FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user || $user['status'] !== 'active') {
            session_unset();
            session_destroy();
            return false;
        }
    }
    return true;
}

// CSRF Token Functions
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function getCSRFInput() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// User login function
function loginUser($user_id, $email, $user_type = 'customer') {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_type'] = $user_type;
    $_SESSION['last_activity'] = time();
    $_SESSION['created'] = time();
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}

// User logout function
function logoutUser() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

// Check user permissions
function requireLogin($redirect = true) {
    if (!isLoggedIn()) {
        if ($redirect) {
            setFlashMessage('error', 'Please login to continue');
            redirect(SITE_URL . '/login.php');
        }
        return false;
    }
    return true;
}

function requireAdmin($redirect = true) {
    if (!isAdmin()) {
        if ($redirect) {
            setFlashMessage('error', 'Access denied. Admin privileges required.');
            redirect(SITE_URL . '/index.php');
        }
        return false;
    }
    return true;
}

// Transfer guest cart to user cart on login
function transferGuestCart($user_id) {
    global $pdo;
    
    $session_id = session_id();
    
    try {
        // Get guest cart items
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE session_id = ?");
        $stmt->execute([$session_id]);
        $guestItems = $stmt->fetchAll();
        
        foreach ($guestItems as $item) {
            // Check if product already in user cart
            $checkStmt = $pdo->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $checkStmt->execute([$user_id, $item['product_id']]);
            $existingItem = $checkStmt->fetch();
            
            if ($existingItem) {
                // Update quantity
                $newQuantity = $existingItem['quantity'] + $item['quantity'];
                $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
                $updateStmt->execute([$newQuantity, $existingItem['cart_id']]);
            } else {
                // Insert new item
                $insertStmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $insertStmt->execute([$user_id, $item['product_id'], $item['quantity']]);
            }
        }
        
        // Delete guest cart items
        $deleteStmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
        $deleteStmt->execute([$session_id]);
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Run session checks
checkSessionTimeout();
regenerateSession();
validateSession();
?>