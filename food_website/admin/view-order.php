<?php
// admin/view-order.php
require_once '../includes/config.php';
require_once 'includes/admin-functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$order_id = (int)($_GET['id'] ?? 0);
if ($order_id === 0) {
    redirect('manage-orders.php');
}

$order = getOrderById($order_id);
if (!$order) {
    setFlashMessage('error', 'Order not found');
    redirect('manage-orders.php');
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_status = sanitize($_POST['order_status'] ?? '');
    $payment_status = sanitize($_POST['payment_status'] ?? '');

    $sql = "UPDATE orders SET order_status = ?, payment_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $order_status, $payment_status, $order_id);

    if ($stmt->execute()) {
        setFlashMessage('success', 'Order updated successfully!');
        // Reload order data
        $order = getOrderById($order_id);
    }
}

// Load order items and include header AFTER POST processing
$order_items = getOrderItems($order_id);
require_once 'includes/admin-header.php';
?>

<div class="mb-8">
    <a href="manage-orders.php" class="text-primary font-bold mb-4 inline-flex items-center gap-2 hover:underline">
        <i class="bi bi-arrow-left"></i> Back to Orders
    </a>
    <h1 class="font-heading text-3xl font-bold text-gray-800">Order Details</h1>
    <p class="text-gray-500">Order #<?php echo htmlspecialchars($order['order_number']); ?></p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Order Info -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="font-heading text-xl font-bold text-gray-800 mb-4">Order Information</h2>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Order Date</p>
                    <p class="text-lg font-bold text-dark"><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Payment Method</p>
                    <p class="text-lg font-bold text-dark"><?php echo ucfirst(htmlspecialchars($order['payment_method'])); ?></p>
                </div>
            </div>

            <hr class="border-gray-100 mb-6">

            <h3 class="font-bold text-gray-800 mb-4">Delivery Address</h3>
            <div class="text-gray-600">
                <p class="font-bold"><?php echo htmlspecialchars($order['full_name'] ?? 'N/A'); ?></p>
                <p><?php echo htmlspecialchars($order['delivery_address'] ?? ''); ?></p>
                <p><?php echo htmlspecialchars($order['delivery_city'] ?? ''); ?>, <?php echo htmlspecialchars($order['delivery_state'] ?? ''); ?></p>
                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($order['delivery_phone'] ?? ''); ?></p>
            </div>

            <?php if (!empty($order['delivery_notes'])): ?>
            <hr class="border-gray-100 my-6">
            <h3 class="font-bold text-gray-800 mb-2">Delivery Notes</h3>
            <p class="text-gray-600"><?php echo htmlspecialchars($order['delivery_notes']); ?></p>
            <?php endif; ?>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-heading text-xl font-bold text-gray-800 mb-4">Order Items</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-900 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($order_items as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-bold text-dark"><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td class="px-4 py-3"><?php echo $item['quantity']; ?></td>
                            <td class="px-4 py-3">₦<?php echo number_format($item['price'], 2); ?></td>
                            <td class="px-4 py-3 font-bold">₦<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="flex justify-end mb-2">
                    <div class="w-64">
                        <div class="flex justify-between text-gray-600 mb-2">
                            <span>Subtotal:</span>
                            <span>₦<?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                        <div class="border-t border-gray-200 pt-2">
                            <div class="flex justify-between font-bold text-lg text-dark">
                                <span>Total:</span>
                                <span>₦<?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update -->
    <div>
        <form method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-heading text-xl font-bold text-gray-800 mb-4">Update Order</h2>

            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Order Status</label>
                <select name="order_status" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none">
                    <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $order['order_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="processing" <?php echo $order['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="out_for_delivery" <?php echo $order['order_status'] == 'out_for_delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                    <option value="delivered" <?php echo $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold mb-2">Payment Status</label>
                <select name="payment_status" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none">
                    <option value="pending" <?php echo $order['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="paid" <?php echo $order['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                    <option value="failed" <?php echo $order['payment_status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                    <option value="refunded" <?php echo $order['payment_status'] == 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors">
                <i class="bi bi-check-lg"></i> Update Order
            </button>

            <button type="button" class="w-full mt-3 px-6 py-3 rounded-xl font-bold border border-gray-300 hover:bg-gray-50 transition-colors" onclick="window.print()">
                <i class="bi bi-printer"></i> Print Order
            </button>
        </form>

        <!-- Customer Info -->
        <?php if (!empty($order['user_id'])): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
            <h3 class="font-bold text-gray-800 mb-3">Customer</h3>
            <p class="font-bold text-dark"><?php echo htmlspecialchars($order['full_name']); ?></p>
            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($order['email']); ?></p>
            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($order['phone']); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
