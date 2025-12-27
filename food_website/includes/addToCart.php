<?php
/**
 * Add to Cart Handler
 */

require_once 'config.php';

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Verify CSRF token only if it's provided (skip for guest users for now)
// You can enable this later once you implement CSRF tokens in frontend
// if (isLoggedIn() && !verifyCSRFToken($_POST['csrf_token'] ?? '')) {
//     echo json_encode(['success' => false, 'message' => 'Invalid security token']);
//     exit;
// }

// Get product ID and quantity
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?: 1;

// Validate inputs
if (!$product_id || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
    exit;
}

try {
    // Check if product exists and is available
    $stmt = $pdo->prepare("SELECT product_id, product_name, price, stock_quantity, status FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    if ($product['status'] !== 'available') {
        echo json_encode(['success' => false, 'message' => 'Product is not available']);
        exit;
    }
    
    // Check stock availability
    if ($product['stock_quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Insufficient stock available. Only ' . $product['stock_quantity'] . ' items in stock.']);
        exit;
    }
    
    // Check if item already in cart
    if (isLoggedIn()) {
        $checkStmt = $pdo->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $checkStmt->execute([$_SESSION['user_id'], $product_id]);
        $existingItem = $checkStmt->fetch();
        
        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            
            // Check if new quantity exceeds stock
            if ($newQuantity > $product['stock_quantity']) {
                echo json_encode(['success' => false, 'message' => 'Cannot add more items. Stock limit reached']);
                exit;
            }
            
            $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE cart_id = ?");
            $updateStmt->execute([$newQuantity, $existingItem['cart_id']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cart_count' => getCartCount(),
                'action' => 'updated'
            ]);
        } else {
            // Insert new item
            $insertStmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $insertStmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Product added to cart',
                'cart_count' => getCartCount(),
                'action' => 'added'
            ]);
        }
    } else {
        // Guest user - use session ID
        $session_id = session_id();
        
        $checkStmt = $pdo->prepare("SELECT cart_id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
        $checkStmt->execute([$session_id, $product_id]);
        $existingItem = $checkStmt->fetch();
        
        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            
            // Check if new quantity exceeds stock
            if ($newQuantity > $product['stock_quantity']) {
                echo json_encode(['success' => false, 'message' => 'Cannot add more items. Stock limit reached']);
                exit;
            }
            
            $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE cart_id = ?");
            $updateStmt->execute([$newQuantity, $existingItem['cart_id']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cart_count' => getCartCount(),
                'action' => 'updated'
            ]);
        } else {
            // Insert new item
            $insertStmt = $pdo->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)");
            $insertStmt->execute([$session_id, $product_id, $quantity]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Product added to cart',
                'cart_count' => getCartCount(),
                'action' => 'added'
            ]);
        }
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>