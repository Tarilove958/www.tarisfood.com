<?php
// admin/manage-orders.php
require_once 'includes/admin-header.php';
require_once 'includes/admin-functions.php';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get total orders
$total_result = $conn->query("SELECT COUNT(order_id) as total FROM orders");
$total_row = $total_result->fetch_assoc();
$total_orders = $total_row['total'];
$total_pages = ceil($total_orders / $per_page);

// Get orders for current page
$sql = "SELECT o.*, u.full_name, u.email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.user_id 
        ORDER BY o.order_date DESC 
        LIMIT $offset, $per_page";
$orders = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<div class="mb-8">
    <h1 class="font-heading text-3xl font-bold text-gray-800">Manage Orders</h1>
    <p class="text-gray-500">Total: <?php echo $total_orders; ?> orders</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-900 font-bold uppercase text-xs">
                <tr>
                    <th class="px-6 py-4">Order #</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Payment</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-dark"><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-bold"><?php echo htmlspecialchars($order['full_name'] ?? 'Guest'); ?></p>
                                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold">₦<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td class="px-6 py-4">
                            <?php 
                            $statusClass = 'bg-gray-100 text-gray-800';
                            if ($order['order_status'] == 'pending') $statusClass = 'bg-yellow-100 text-yellow-800';
                            if ($order['order_status'] == 'confirmed') $statusClass = 'bg-blue-100 text-blue-800';
                            if ($order['order_status'] == 'processing') $statusClass = 'bg-purple-100 text-purple-800';
                            if ($order['order_status'] == 'out_for_delivery') $statusClass = 'bg-indigo-100 text-indigo-800';
                            if ($order['order_status'] == 'delivered') $statusClass = 'bg-green-100 text-green-800';
                            if ($order['order_status'] == 'cancelled') $statusClass = 'bg-red-100 text-red-800';
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $statusClass; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $paymentClass = 'bg-gray-100 text-gray-800';
                            if ($order['payment_status'] == 'pending') $paymentClass = 'bg-yellow-100 text-yellow-800';
                            if ($order['payment_status'] == 'paid') $paymentClass = 'bg-green-100 text-green-800';
                            if ($order['payment_status'] == 'failed') $paymentClass = 'bg-red-100 text-red-800';
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $paymentClass; ?>">
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs"><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                        <td class="px-6 py-4">
                            <a href="view-order.php?id=<?php echo $order['order_id']; ?>" class="text-primary hover:text-blue-700 font-bold text-sm">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No orders found
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center gap-2">
        <?php if ($page > 1): ?>
            <a href="?page=1" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">First</a>
            <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Prev</a>
        <?php endif; ?>

        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="px-3 py-2 rounded-lg <?php echo $i === $page ? 'bg-primary text-white' : 'bg-white border border-gray-300 hover:bg-gray-50'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
            <a href="?page=<?php echo $total_pages; ?>" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Last</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
