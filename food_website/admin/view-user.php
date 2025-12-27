<?php
// admin/view-user.php
require_once '../includes/config.php';

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] != 'admin') {
    redirect('/admin/login.php');
}

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$user_id) {
    setFlashMessage('Invalid user ID', 'danger');
    redirect('/admin/manage-users.php');
}

// Get user details
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    setFlashMessage('User not found', 'danger');
    redirect('/admin/manage-users.php');
}

// Get user's orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$orders = $orders_result->fetch_all(MYSQLI_ASSOC);

// Enrich orders with item count
foreach ($orders as &$order) {
    $stmt = $conn->prepare("SELECT SUM(quantity) as total_items FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order['order_id']);
    $stmt->execute();
    $item_result = $stmt->get_result();
    $item_row = $item_result->fetch_assoc();
    $order['total_items'] = $item_row['total_items'] ?? 0;
}
?>

<?php include 'includes/admin-header.php'; ?>

<div class="mb-8 flex justify-between items-center">
    <div>
        <a href="manage-users.php" class="text-primary hover:text-blue-700 text-sm mb-4 inline-block">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
        <h1 class="font-heading text-3xl font-bold text-gray-800">Customer Details</h1>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Customer Information Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Customer Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Full Name</label>
                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($user['full_name']); ?></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Email</label>
                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Phone</label>
                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Address</label>
                    <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">City</label>
                        <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($user['city'] ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">State</label>
                        <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($user['state'] ?? 'N/A'); ?></p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Account Type</label>
                    <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $user['user_type'] == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                        <?php echo ucfirst($user['user_type']); ?>
                    </span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                    <span class="px-3 py-1 rounded-full text-xs font-bold 
                        <?php 
                        if ($user['status'] == 'active') echo 'bg-green-100 text-green-800';
                        elseif ($user['status'] == 'inactive') echo 'bg-gray-100 text-gray-800';
                        else echo 'bg-red-100 text-red-800';
                        ?>
                    ">
                        <?php echo ucfirst($user['status']); ?>
                    </span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Member Since</label>
                    <p class="text-gray-800 font-semibold"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Order History</h2>
            
            <?php if (!empty($orders)): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-gray-900 font-bold uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">Order ID</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Items</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Order Status</th>
                                <th class="px-4 py-3">Payment</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-bold"><?php echo htmlspecialchars($order['order_number']); ?></td>
                                <td class="px-4 py-3 text-sm"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td class="px-4 py-3 text-sm"><?php echo $order['total_items']; ?> item(s)</td>
                                <td class="px-4 py-3 font-semibold">₦<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold 
                                        <?php 
                                        $status = strtolower($order['order_status']);
                                        if ($status == 'delivered') echo 'bg-green-100 text-green-800';
                                        elseif ($status == 'processing' || $status == 'out_for_delivery') echo 'bg-blue-100 text-blue-800';
                                        elseif ($status == 'pending' || $status == 'confirmed') echo 'bg-yellow-100 text-yellow-800';
                                        elseif ($status == 'cancelled') echo 'bg-red-100 text-red-800';
                                        else echo 'bg-gray-100 text-gray-800';
                                        ?>
                                    ">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold 
                                        <?php 
                                        $payment = strtolower($order['payment_status']);
                                        if ($payment == 'paid' || $payment == 'completed') echo 'bg-green-100 text-green-800';
                                        elseif ($payment == 'pending') echo 'bg-yellow-100 text-yellow-800';
                                        else echo 'bg-red-100 text-red-800';
                                        ?>
                                    ">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="view-order.php?id=<?php echo $order['order_id']; ?>" class="text-primary hover:text-blue-700 font-bold text-sm" title="View Details">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <?php if ($order['order_status'] == 'pending'): ?>
                                        <form method="POST" action="update-order-status.php" class="inline">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <input type="hidden" name="action" value="accept">
                                            <button type="submit" class="text-green-600 hover:text-green-800 font-bold text-sm" title="Accept Order">
                                                <i class="bi bi-check-circle"></i> Accept
                                            </button>
                                        </form>
                                        <form method="POST" action="update-order-status.php" class="inline">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-sm" title="Reject Order" onclick="return confirm('Are you sure you want to reject this order?');">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <p class="text-gray-500">No orders found for this customer</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
