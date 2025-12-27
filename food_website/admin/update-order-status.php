<?php
/**
 * Update Order Status Handler
 * Handles admin accepting or rejecting orders
 */

require_once '../includes/config.php';

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] != 'admin') {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $action = isset($_POST['action']) ? sanitize($_POST['action']) : '';
    
    if (!$order_id || !in_array($action, ['accept', 'reject'])) {
        setFlashMessage('error', 'Invalid request');
        header('Location: manage-orders.php');
        exit;
    }
    
    // Get order details to find user_id for redirect
    $stmt = $conn->prepare("SELECT user_id FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    if (!$order) {
        setFlashMessage('error', 'Order not found');
        header('Location: manage-orders.php');
        exit;
    }
    
    $user_id = $order['user_id'];
    
    // Update order status based on action
    if ($action === 'accept') {
        $new_status = 'confirmed';
        $message = 'Order accepted successfully';
    } else {
        $new_status = 'cancelled';
        $message = 'Order rejected successfully';
    }
    
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        setFlashMessage('success', $message);
    } else {
        setFlashMessage('error', 'Error updating order status');
    }
    
    // Redirect back to customer details
    header('Location: view-user.php?id=' . $user_id);
    exit;
    
} catch (Exception $e) {
    setFlashMessage('error', 'Error: ' . $e->getMessage());
    header('Location: manage-orders.php');
    exit;
}
?>
