<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login.php?redirect=user/index.php');
    exit;
}

$page_title = 'My Dashboard';
include '../includes/header.php';

// Get user info
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get user's recent orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_orders = $stmt->fetchAll();

// Get order statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_orders FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$order_stats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT SUM(total_amount) as total_spent FROM orders WHERE user_id = ? AND order_status != 'cancelled'");
$stmt->execute([$user_id]);
$spend_stats = $stmt->fetch();
$total_spent = $spend_stats['total_spent'] ?? 0;

$stmt = $pdo->prepare("SELECT COUNT(*) as total_items FROM order_items oi JOIN orders o ON oi.order_id = o.order_id WHERE o.user_id = ?");
$stmt->execute([$user_id]);
$items_stats = $stmt->fetch();
?>

<!-- Dashboard Header -->
<div class="bg-gradient-to-r from-primary to-blue-600 text-white py-12">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="font-bricolage font-bold text-4xl mb-2">Welcome, <?php echo htmlspecialchars($user['full_name']); ?>! 👋</h1>
        <p class="text-blue-100">Manage your profile, orders, and preferences</p>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="max-w-6xl mx-auto px-4 py-12">
    <!-- Stats Cards -->
    <div class="grid md:grid-cols-4 gap-6 mb-12">
        <!-- Total Orders -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Total Orders</p>
                    <p class="font-bricolage font-bold text-3xl text-dark"><?php echo $order_stats['total_orders']; ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-bag-check text-blue-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Lifetime orders</p>
        </div>

        <!-- Total Spent -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Total Spent</p>
                    <p class="font-bricolage font-bold text-3xl text-dark"><?php echo formatPrice($total_spent); ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-cash-coin text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Completed orders only</p>
        </div>

        <!-- Items Ordered -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Items Ordered</p>
                    <p class="font-bricolage font-bold text-3xl text-dark"><?php echo $items_stats['total_items']; ?></p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-basket text-orange-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Total items purchased</p>
        </div>

        <!-- Account Status -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold mb-2">Account Status</p>
                    <p class="font-bricolage font-bold text-lg text-dark">
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Active</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-shield-check text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
        </div>
    </div>

    <!-- Recent Orders & Quick Actions -->
    <div class="grid md:grid-cols-3 gap-8 mb-12">
        <!-- Recent Orders -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-bricolage font-bold text-2xl text-dark">Recent Orders</h2>
                    <a href="orders.php" class="text-primary text-sm font-semibold hover:underline flex items-center gap-1">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <?php if (count($recent_orders) > 0): ?>
                    <div class="space-y-3">
                        <?php foreach ($recent_orders as $order): ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-bag text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-dark"><?php echo 'Order #' . $order['order_id']; ?></p>
                                    <p class="text-xs text-gray-500"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-dark"><?php echo formatPrice($order['total_amount']); ?></p>
                                <span class="inline-block text-xs font-semibold px-2 py-1 rounded-full 
                                    <?php 
                                    if ($order['order_status'] == 'delivered') echo 'bg-green-100 text-green-700';
                                    elseif ($order['order_status'] == 'pending') echo 'bg-yellow-100 text-yellow-700';
                                    elseif ($order['order_status'] == 'cancelled') echo 'bg-red-100 text-red-700';
                                    else echo 'bg-blue-100 text-blue-700';
                                    ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="bi bi-inbox text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">No orders yet</p>
                        <a href="../menu.php" class="text-primary text-sm font-semibold mt-2 inline-block hover:underline">Start ordering now →</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-bricolage font-bold text-xl text-dark mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="profile.php" class="flex items-center gap-3 p-4 rounded-lg bg-blue-50 hover:bg-blue-100 transition-colors group">
                        <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center group-hover:bg-blue-300">
                            <i class="bi bi-person text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-dark text-sm">Edit Profile</p>
                            <p class="text-xs text-gray-500">Update your info</p>
                        </div>
                    </a>

                    <a href="orders.php" class="flex items-center gap-3 p-4 rounded-lg bg-orange-50 hover:bg-orange-100 transition-colors group">
                        <div class="w-10 h-10 bg-orange-200 rounded-lg flex items-center justify-center group-hover:bg-orange-300">
                            <i class="bi bi-bag-check text-orange-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-dark text-sm">View Orders</p>
                            <p class="text-xs text-gray-500">Track your orders</p>
                        </div>
                    </a>

                    <a href="change-password.php" class="flex items-center gap-3 p-4 rounded-lg bg-green-50 hover:bg-green-100 transition-colors group">
                        <div class="w-10 h-10 bg-green-200 rounded-lg flex items-center justify-center group-hover:bg-green-300">
                            <i class="bi bi-shield-lock text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-dark text-sm">Change Password</p>
                            <p class="text-xs text-gray-500">Secure your account</p>
                        </div>
                    </a>

                    <a href="../menu.php" class="flex items-center gap-3 p-4 rounded-lg bg-purple-50 hover:bg-purple-100 transition-colors group">
                        <div class="w-10 h-10 bg-purple-200 rounded-lg flex items-center justify-center group-hover:bg-purple-300">
                            <i class="bi bi-basket text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-dark text-sm">Order Food</p>
                            <p class="text-xs text-gray-500">Browse menu</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- User Info Card -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 mt-6">
                <h3 class="font-bricolage font-bold text-lg text-dark mb-4">Account Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Email:</span>
                        <span class="font-semibold text-dark"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <hr>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Phone:</span>
                        <span class="font-semibold text-dark"><?php echo $user['phone'] ?? 'Not provided'; ?></span>
                    </div>
                    <hr>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Member Since:</span>
                        <span class="font-semibold text-dark"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
