<?php
// admin/index.php - ADMIN ONLY DASHBOARD
require_once 'includes/admin-header.php';

// Access Control: Check if user is logged in and is an admin
if (!isLoggedIn()) {
    redirect('../login.php');
}

if (!isAdmin()) {
    // If logged in but not admin, redirect to user dashboard
    redirect('../user/index.php');
}

$stats = getDashboardStats();
?>

<div class="mb-8 flex justify-between items-start">
    <div>
        <h1 class="font-heading text-4xl font-bold text-dark mb-2">Dashboard Overview</h1>
        <p class="text-gray-500 text-sm font-medium">Welcome back, here's your business performance today.</p>
    </div>
    <div class="text-right text-sm text-gray-500">
        <p>Today: <span class="font-bold text-dark"><?php echo date('l, F d, Y'); ?></span></p>
    </div>
</div>

<!-- Statistics Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Total Revenue -->
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide mb-1">Total Revenue</p>
                <h3 class="text-3xl font-bold text-dark mb-1">₦<?php echo number_format($stats['revenue']); ?></h3>
                <p class="text-xs text-green-600 font-medium">
                    <i class="bi bi-arrow-up"></i> <?php echo isset($stats['revenue_growth']) ? $stats['revenue_growth'] . '%' : '12%'; ?> this month
                </p>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); color: #16A34A;">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide mb-1">Total Orders</p>
                <h3 class="text-3xl font-bold text-dark mb-1"><?php echo $stats['orders']; ?></h3>
                <p class="text-xs text-blue-600 font-medium">
                    <i class="bi bi-arrow-up"></i> <?php echo isset($stats['orders_growth']) ? $stats['orders_growth'] : '8'; ?> new orders
                </p>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.1) 100%); color: var(--primary);">
                <i class="bi bi-cart-check"></i>
            </div>
        </div>
    </div>

    <!-- Food Items -->
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide mb-1">Food Items</p>
                <h3 class="text-3xl font-bold text-dark mb-1"><?php echo $stats['products']; ?></h3>
                <p class="text-xs text-orange-600 font-medium">
                    <i class="bi bi-arrow-up"></i> <?php echo isset($stats['products_active']) ? $stats['products_active'] : $stats['products']; ?> active
                </p>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(234, 88, 12, 0.1) 100%); color: #EA580C;">
                <i class="bi bi-egg-fried"></i>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide mb-1">Customers</p>
                <h3 class="text-3xl font-bold text-dark mb-1"><?php echo $stats['customers']; ?></h3>
                <p class="text-xs text-purple-600 font-medium">
                    <i class="bi bi-arrow-up"></i> <?php echo isset($stats['customers_new']) ? $stats['customers_new'] : '3'; ?> new customers
                </p>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(168, 85, 247, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%); color: #A855F7;">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Section -->
<div class="admin-card overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="font-heading text-xl font-bold text-dark">Recent Orders</h2>
            <p class="text-xs text-gray-500 mt-1">Latest customer orders and their status</p>
        </div>
        <a href="manage-orders.php" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-bold hover:shadow-lg transition-smooth">
            View All <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 font-bold text-gray-700 text-xs uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php
                $order_sql = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 8";
                $order_res = $conn->query($order_sql);
                
                if($order_res && $order_res->num_rows > 0):
                    while($order = $order_res->fetch_assoc()):
                        // Status Badge Logic
                        $statusClass = 'badge-processing';
                        $statusIcon = 'bi-hourglass-split';
                        if($order['order_status'] == 'delivered') {
                            $statusClass = 'badge-success';
                            $statusIcon = 'bi-check-circle';
                        }
                        if($order['order_status'] == 'pending') {
                            $statusClass = 'badge-pending';
                            $statusIcon = 'bi-clock';
                        }
                        if($order['order_status'] == 'cancelled') {
                            $statusClass = 'badge-cancelled';
                            $statusIcon = 'bi-x-circle';
                        }
                ?>
                <tr class="hover:bg-gray-50 transition-smooth">
                    <td class="px-6 py-4">
                        <span class="font-bold text-dark">#<?php echo $order['order_number']; ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">
                                <?php echo strtoupper(substr($order['user_id'], 0, 1)); ?>
                            </div>
                            <span class="text-gray-700">User #<?php echo $order['user_id']; ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold text-dark">₦<?php echo number_format($order['total_amount']); ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold <?php echo $statusClass; ?>">
                            <i class="bi <?php echo $statusIcon; ?>"></i>
                            <?php echo ucfirst($order['order_status']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-xs">
                        <?php echo date('M d, Y • h:i A', strtotime($order['order_date'])); ?>
                    </td>
                    <td class="px-6 py-4">
                        <a href="view-order.php?id=<?php echo $order['order_id']; ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 text-primary font-bold hover:text-primary transition-smooth" title="View Details">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <i class="bi bi-inbox text-3xl mb-2 block"></i>
                            <p class="font-medium">No recent orders found.</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/admin-footer.php'; ?>