<?php
/**
 * Process Order Handler
 * Handles order creation and payment processing
 */

require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'You must be logged in to place an order']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlashMessage('error', 'Security token invalid. Please try again.');
    redirect('../checkout.php');
}

try {
    // Validate required fields
    $required_fields = ['full_name', 'phone', 'address', 'city', 'state', 'payment_method', 'total_amount'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            setFlashMessage('error', 'All fields are required');
            redirect('../checkout.php');
        }
    }

    // Get form data
    $user_id = $_SESSION['user_id'];
    $full_name = sanitize($_POST['full_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $state = sanitize($_POST['state']);
    $payment_method = sanitize($_POST['payment_method']);
    $special_instructions = sanitize($_POST['special_instructions'] ?? '');
    
    $subtotal = floatval($_POST['subtotal'] ?? 0);
    $delivery_fee = floatval($_POST['delivery_fee'] ?? 0);
    $total_amount = floatval($_POST['total_amount'] ?? 0);
    
    // Validate amounts
    if ($total_amount <= 0) {
        setFlashMessage('error', 'Invalid order amount');
        redirect('../checkout.php');
    }

    // Get cart items
    $stmt = $pdo->prepare("SELECT c.*, p.product_name, p.price FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();

    if (empty($cart_items)) {
        setFlashMessage('error', 'Your cart is empty');
        redirect('../checkout.php');
    }

    // Generate order number
    $order_number = generateOrderNumber();

    // Start transaction
    $pdo->beginTransaction();

    // Create order
    $sql = "INSERT INTO orders (
        user_id, order_number, subtotal, delivery_fee, total_amount,
        payment_method, payment_status, order_status,
        delivery_address, delivery_city, delivery_state, delivery_phone,
        special_instructions
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $user_id,
        $order_number,
        $subtotal,
        $delivery_fee,
        $total_amount,
        $payment_method,
        'pending',
        'pending',
        $address,
        $city,
        $state,
        $phone,
        $special_instructions
    ]);

    $order_id = $pdo->lastInsertId();

    // Add order items
    foreach ($cart_items as $item) {
        $item_subtotal = $item['price'] * $item['quantity'];
        $item_sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
        $item_stmt = $pdo->prepare($item_sql);
        $item_stmt->execute([
            $order_id,
            $item['product_id'],
            $item['product_name'],
            $item['quantity'],
            $item['price'],
            $item_subtotal
        ]);

        // Update product stock
        $stock_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
        $stock_stmt = $pdo->prepare($stock_sql);
        $stock_stmt->execute([$item['quantity'], $item['product_id']]);
    }

    // Clear cart
    $clear_sql = "DELETE FROM cart WHERE user_id = ?";
    $clear_stmt = $pdo->prepare($clear_sql);
    $clear_stmt->execute([$user_id]);

    // Commit transaction
    $pdo->commit();

    // Process payment based on method
    if ($payment_method === 'paystack') {
        // Redirect to Paystack payment
        header('Location: ../payments/paystack-callback.php?order_id=' . $order_id . '&amount=' . intval($total_amount * 100));
        exit;
    } elseif ($payment_method === 'flutterwave') {
        // Redirect to Flutterwave payment
        header('Location: ../payments/flutterwave-callback.php?order_id=' . $order_id . '&amount=' . $total_amount);
        exit;
    } else {
        // For other payment methods, mark as pending
        setFlashMessage('success', 'Order placed successfully! Order #' . $order_number);
        redirect('../user/index.php');
    }

} catch (PDOException $e) {
    // Rollback on error
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    
    setFlashMessage('error', 'Error processing order: ' . $e->getMessage());
    redirect('../checkout.php');
} catch (Exception $e) {
    setFlashMessage('error', 'An error occurred: ' . $e->getMessage());
    redirect('../checkout.php');
}
?>
