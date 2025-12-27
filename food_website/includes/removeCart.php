<?php
/**
 * Remove from Cart Handler
 */

require_once 'config.php';

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// CSRF token check DISABLED for now to allow AJAX cart operations
// You can enable this later once you implement CSRF tokens in your frontend JavaScript
// if (isLoggedIn() && !verifyCSRFToken($_POST['csrf_token'] ?? '')) {
//     echo json_encode(['success' => false, 'message' => 'Invalid security token']);
//     exit;
// }

// Get cart ID or product ID
$cart_id = filter_input(INPUT_POST, 'cart_id', FILTER_VALIDATE_INT);
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

// Validate inputs
if (!$cart_id && !$product_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    exit;
}

try {
    if (isLoggedIn()) {
        if ($cart_id) {
            // Remove by cart ID
            $stmt = $pdo->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
            $stmt->execute([$cart_id, $_SESSION['user_id']]);
        } else {
            // Remove by product ID
            $stmt = $pdo->prepare("DELETE FROM cart WHERE product_id = ? AND user_id = ?");
            $stmt->execute([$product_id, $_SESSION['user_id']]);
        }
    } else {
        // Guest user - use session ID
        $session_id = session_id();
        
        if ($cart_id) {
            // Remove by cart ID
            $stmt = $pdo->prepare("DELETE FROM cart WHERE cart_id = ? AND session_id = ?");
            $stmt->execute([$cart_id, $session_id]);
        } else {
            // Remove by product ID
            $stmt = $pdo->prepare("DELETE FROM cart WHERE product_id = ? AND session_id = ?");
            $stmt->execute([$product_id, $session_id]);
        }
    }
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => getCartCount(),
            'cart_total' => getCartTotal()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>