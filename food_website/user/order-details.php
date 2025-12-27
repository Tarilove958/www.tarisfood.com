<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login.php?redirect=user/order-details.php');
    exit;
}

$page_title = 'Order Details';
include '../includes/header.php';

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    setFlashMessage('error', 'Order not found');
    header('Location: orders.php');
    exit;
}

// Get order items
$stmt = $pdo->prepare("SELECT oi.*, p.product_name FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>

<!-- Page Header -->
<div class="bg-gradient-to-r from-primary to-blue-600 text-white py-12">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center gap-2 mb-4">
            <a href="orders.php" class="flex items-center gap-1 text-blue-100 hover:text-white">
                <i class="bi bi-chevron-left"></i> Back to Orders
            </a>
        </div>
        <h1 class="font-bricolage font-bold text-4xl mb-2">Order #<?php echo $order['order_id']; ?></h1>
        <p class="text-blue-100">Placed on <?php echo date('F d, Y • g:i A', strtotime($order['order_date'])); ?></p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 py-12">
    <?php if ($order['order_status'] == 'cancelled'): ?>
    <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500">
        <div class="flex items-start gap-3">
            <i class="bi bi-exclamation-circle text-red-600 text-xl mt-0.5"></i>
            <div>
                <p class="font-bold text-red-700">Order Rejected</p>
                <p class="text-sm text-red-600">This order has been rejected by the restaurant. Please contact support if you have any questions.</p>
            </div>
        </div>
    </div>
    <?php elseif ($order['order_status'] == 'confirmed'): ?>
    <div class="mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500">
        <div class="flex items-start gap-3">
            <i class="bi bi-check-circle text-green-600 text-xl mt-0.5"></i>
            <div>
                <p class="font-bold text-green-700">Order Accepted</p>
                <p class="text-sm text-green-600">Your order has been confirmed by the restaurant and is being prepared.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="grid md:grid-cols-3 gap-8">
        <!-- Order Items -->
        <div class="md:col-span-2">
            <!-- Order Status -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-8">
                <h2 class="font-bricolage font-bold text-xl text-dark mb-4">Order Status</h2>
                <div class="flex items-center justify-between mb-4">
                    <div class="text-center flex-1">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="bi bi-check text-green-600 text-xl font-bold"></i>
                        </div>
                        <p class="text-sm font-semibold text-dark">Ordered</p>
                    </div>
                    <div class="flex-1 h-1 <?php echo ($order['order_status'] != 'pending') ? 'bg-green-200' : 'bg-gray-200'; ?>"></div>
                    <div class="text-center flex-1">
                        <div class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center
                            <?php echo ($order['order_status'] == 'cancelled') ? 'bg-red-100' : (($order['order_status'] != 'pending') ? 'bg-green-100' : 'bg-gray-100'); ?>">
                            <i class="bi <?php echo ($order['order_status'] == 'cancelled') ? 'bi-x text-red-600' : (($order['order_status'] != 'pending') ? 'bi-check text-green-600' : 'bi-clock text-gray-400'); ?> text-xl font-bold"></i>
                        </div>
                        <p class="text-sm font-semibold text-dark">Confirmed</p>
                    </div>
                    <div class="flex-1 h-1 <?php echo ($order['order_status'] == 'processing' || $order['order_status'] == 'out_for_delivery' || $order['order_status'] == 'delivered') ? 'bg-green-200' : 'bg-gray-200'; ?>"></div>
                    <div class="text-center flex-1">
                        <div class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center
                            <?php echo ($order['order_status'] == 'processing' || $order['order_status'] == 'out_for_delivery' || $order['order_status'] == 'delivered') ? 'bg-green-100' : 'bg-gray-100'; ?>">
                            <i class="bi <?php echo ($order['order_status'] == 'processing' || $order['order_status'] == 'out_for_delivery' || $order['order_status'] == 'delivered') ? 'bi-check text-green-600' : 'bi-clock text-gray-400'; ?> text-xl font-bold"></i>
                        </div>
                        <p class="text-sm font-semibold text-dark">Processing</p>
                    </div>
                    <div class="flex-1 h-1 <?php echo $order['order_status'] == 'delivered' ? 'bg-green-200' : 'bg-gray-200'; ?>"></div>
                    <div class="text-center flex-1">
                        <div class="w-12 h-12 mx-auto mb-2 rounded-full flex items-center justify-center
                            <?php echo $order['order_status'] == 'delivered' ? 'bg-green-100' : 'bg-gray-100'; ?>">
                            <i class="bi <?php echo $order['order_status'] == 'delivered' ? 'bi-check text-green-600' : 'bi-box text-gray-400'; ?> text-xl font-bold"></i>
                        </div>
                        <p class="text-sm font-semibold text-dark">Delivered</p>
                    </div>
                </div>
                <div class="text-center">
                    <span class="inline-block text-sm font-bold px-4 py-2 rounded-full
                        <?php 
                        if ($order['order_status'] == 'delivered') echo 'bg-green-100 text-green-700';
                        elseif ($order['order_status'] == 'confirmed' || $order['order_status'] == 'processing' || $order['order_status'] == 'out_for_delivery') echo 'bg-blue-100 text-blue-700';
                        elseif ($order['order_status'] == 'pending') echo 'bg-yellow-100 text-yellow-700';
                        elseif ($order['order_status'] == 'cancelled') echo 'bg-red-100 text-red-700';
                        else echo 'bg-gray-100 text-gray-700';
                        ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?>
                    </span>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-8">
                <h2 class="font-bricolage font-bold text-xl text-dark mb-4">Order Items</h2>
                <div class="space-y-3">
                    <?php foreach ($order_items as $item): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="font-semibold text-dark"><?php echo htmlspecialchars($item['product_name']); ?></p>
                            <p class="text-sm text-gray-500">Qty: <?php echo $item['quantity']; ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-dark"><?php echo formatPrice($item['price']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo formatPrice($item['subtotal']); ?> total</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Order Summary & Delivery Info -->
        <div>
            <!-- Order Summary -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
                <h3 class="font-bricolage font-bold text-lg text-dark mb-4">Order Summary</h3>
                <div class="space-y-3 text-sm mb-4 pb-4 border-b border-gray-200">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold text-dark"><?php echo formatPrice($order['subtotal']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-semibold text-dark"><?php echo formatPrice($order['tax']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery Fee</span>
                        <span class="font-semibold text-dark"><?php echo formatPrice($order['delivery_fee']); ?></span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="font-bricolage font-bold text-dark">Total</span>
                    <span class="font-bricolage font-bold text-2xl text-secondary"><?php echo formatPrice($order['total_amount']); ?></span>
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
                <h3 class="font-bricolage font-bold text-lg text-dark mb-4 flex items-center gap-2">
                    <i class="bi bi-geo-alt text-primary"></i> Delivery Address
                </h3>
                <p class="text-gray-700 leading-relaxed">
                    <?php echo htmlspecialchars($order['delivery_address']); ?>
                </p>
                <?php if ($order['delivery_notes']): ?>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Delivery Notes</p>
                    <p class="text-sm text-gray-700"><?php echo htmlspecialchars($order['delivery_notes']); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Contact -->
            <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
                <h3 class="font-bricolage font-bold text-lg text-dark mb-4 flex items-center gap-2">
                    <i class="bi bi-telephone text-primary"></i> Need Help?
                </h3>
                <p class="text-sm text-gray-700 mb-4">
                    If you have any questions about your order, please contact us.
                </p>
                <a href="../contact.php" class="inline-block w-full text-center px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors text-sm">
                    <i class="bi bi-chat-dots me-1"></i> Contact Support
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
