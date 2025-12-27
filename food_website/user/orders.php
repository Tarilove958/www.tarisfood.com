<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login.php?redirect=user/orders.php');
    exit;
}

$page_title = 'My Orders';
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Get all orders for the user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!-- Page Header -->
<div class="bg-gradient-to-r from-primary to-blue-600 text-white py-12">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="font-bricolage font-bold text-4xl mb-2">My Orders</h1>
        <p class="text-blue-100">View and track all your food orders</p>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-12">
    <?php if (count($orders) > 0): ?>
        <div class="space-y-4">
            <?php foreach ($orders as $order): ?>
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all">
                    <div class="p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-3">
                                <h3 class="font-bold text-lg text-dark">Order #<?php echo $order['order_id']; ?></h3>
                                <span class="inline-block text-xs font-bold px-3 py-1 rounded-full 
                                    <?php 
                                    if ($order['order_status'] == 'delivered') echo 'bg-green-100 text-green-700';
                                    elseif ($order['order_status'] == 'pending') echo 'bg-yellow-100 text-yellow-700';
                                    elseif ($order['order_status'] == 'cancelled') echo 'bg-red-100 text-red-700';
                                    elseif ($order['order_status'] == 'processing' || $order['order_status'] == 'out_for_delivery') echo 'bg-blue-100 text-blue-700';
                                    else echo 'bg-gray-100 text-gray-700';
                                    ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="bi bi-calendar-event me-2"></i>
                                <?php echo date('F d, Y • g:i A', strtotime($order['order_date'])); ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="bi bi-geo-alt me-2"></i>
                                <?php echo htmlspecialchars($order['delivery_address']); ?>
                            </p>
                        </div>
                        <div class="text-right w-full md:w-auto">
                            <p class="font-bricolage font-bold text-2xl text-secondary mb-2">
                                <?php echo formatPrice($order['total_amount']); ?>
                            </p>
                            <a href="order-details.php?order_id=<?php echo $order['order_id']; ?>" 
                               class="inline-block px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                                View Details <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-20">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-inbox text-5xl text-gray-300"></i>
            </div>
            <h3 class="font-bricolage font-bold text-2xl text-dark mb-2">No Orders Yet</h3>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                You haven't placed any orders yet. Start exploring our delicious menu and place your first order!
            </p>
            <a href="../menu.php" class="inline-block px-8 py-4 bg-primary text-white rounded-full font-bold hover:bg-blue-700 transition-colors">
                <i class="bi bi-bag-plus me-2"></i> Order Now
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
