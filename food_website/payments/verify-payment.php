<?php
/**
 * Payment Verification Handler
 * Handles payment confirmation from payment gateways
 */

require_once '../includes/config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $order_id = isset($input['order_id']) ? (int)$input['order_id'] : 0;
    $payment_method = isset($input['payment_method']) ? sanitize($input['payment_method']) : '';
    $status = isset($input['status']) ? sanitize($input['status']) : 'pending';
    
    if (!$order_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
        exit;
    }
    
    // Get order
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch();
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }
    
    // Update order payment status
    if ($status === 'completed') {
        $update_stmt = $pdo->prepare("UPDATE orders SET payment_status = ?, order_status = ? WHERE order_id = ?");
        $update_stmt->execute(['completed', 'confirmed', $order_id]);
        
        setFlashMessage('success', 'Payment successful! Your order #' . $order['order_number'] . ' has been confirmed.');
    } else {
        $update_stmt = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE order_id = ?");
        $update_stmt->execute(['failed', $order_id]);
        
        setFlashMessage('error', 'Payment failed. Please try again.');
    }
    
    echo json_encode(['success' => true, 'message' => 'Payment verified', 'order_id' => $order_id]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
