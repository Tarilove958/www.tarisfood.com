<?php
// admin/index.php
require_once 'includes/admin-header.php';

$stats = getDashboardStats();
?>

<div class="mb-8">
    <h1 class="font-heading text-3xl font-bold text-gray-800">Dashboard Overview</h1>
    <p class="text-gray-500">Welcome back, here is what's happening today.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-gray-500 text-sm font-bold uppercase">Total Revenue</p>
            <h3 class="text-2xl font-bold text-dark mt-1">₦<?php echo number_format($stats['revenue']); ?></h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl">
            <i class="bi bi-wallet2"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-gray-500 text-sm font-bold uppercase">Total Orders</p>
            <h3 class="text-2xl font-bold text-dark mt-1"><?php echo $stats['orders']; ?></h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-blue-100 text-primary flex items-center justify-center text-xl">
            <i class="bi bi-cart-check"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-gray-500 text-sm font-bold uppercase">Food Items</p>
            <h3 class="text-2xl font-bold text-dark mt-1"><?php echo $stats['products']; ?></h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xl">
            <i class="bi bi-egg-fried"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <p class="text-gray-500 text-sm font-bold uppercase">Customers</p>
            <h3 class="text-2xl font-bold text-dark mt-1"><?php echo $stats['customers']; ?></h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-xl">
            <i class="bi bi-people"></i>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h2 class="font-heading text-lg font-bold">Recent Orders</h2>
        <a href="manage-orders.php" class="text-sm text-primary font-bold hover:underline">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-900 font-bold uppercase text-xs">
                <tr>
                    <th class="px-6 py-4">Order ID</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Total</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php
                $order_sql = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 5";
                $order_res = $conn->query($order_sql);
                
                if($order_res && $order_res->num_rows > 0):
                    while($order = $order_res->fetch_assoc()):
                        // Status Badge Color Logic
                        $statusClass = 'bg-gray-100 text-gray-800';
                        if($order['order_status'] == 'delivered') $statusClass = 'bg-green-100 text-green-800';
                        if($order['order_status'] == 'pending') $statusClass = 'bg-yellow-100 text-yellow-800';
                        if($order['order_status'] == 'cancelled') $statusClass = 'bg-red-100 text-red-800';
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-dark"><?php echo $order['order_number']; ?></td>
                    <td class="px-6 py-4">User #<?php echo $order['user_id']; ?></td>
                    <td class="px-6 py-4 font-bold">₦<?php echo number_format($order['total_amount']); ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $statusClass; ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                    <td class="px-6 py-4">
                        <a href="view-order.php?id=<?php echo $order['order_id']; ?>" class="text-primary hover:text-blue-800 font-bold">View</a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">No recent orders found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/admin-footer.php'; ?>