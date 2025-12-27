<?php
/**
 * Flutterwave Payment Callback Handler
 */

require_once '../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$amount = isset($_GET['amount']) ? (float)$_GET['amount'] : 0;

if (!$order_id) {
    setFlashMessage('error', 'Invalid order. Please try again.');
    redirect('../checkout.php');
}

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    setFlashMessage('error', 'Order not found.');
    redirect('../menu.php');
}

$page_title = 'Payment - Flutterwave';
include '../includes/header.php';
?>

<div class="bg-light py-12 min-h-[70vh]">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-wallet2 text-3xl text-purple-600"></i>
                </div>
                <h1 class="font-bricolage font-bold text-2xl mb-2">Secure Payment</h1>
                <p class="text-gray-600">Complete your payment via Flutterwave</p>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-600">Order Number</p>
                        <p class="font-bold text-lg"><?php echo htmlspecialchars($order['order_number']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Amount</p>
                        <p class="font-bold text-lg text-primary"><?php echo formatPrice($order['total_amount']); ?></p>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Delivery Address</p>
                    <p class="text-sm text-dark"><?php echo htmlspecialchars($order['delivery_address']); ?>, <?php echo htmlspecialchars($order['delivery_city']); ?>, <?php echo htmlspecialchars($order['delivery_state']); ?></p>
                </div>
            </div>

            <div class="space-y-4">
                <button id="flutterwaveBtn" class="w-full py-3 bg-primary text-white rounded-xl font-bold hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                    <i class="bi bi-shield-lock"></i>
                    Pay with Flutterwave (₦<?php echo number_format($order['total_amount'], 2); ?>)
                </button>
                
                <a href="../user/orders.php" class="block text-center py-3 border border-gray-300 text-dark rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                    View Order Details
                </a>
            </div>

            <p class="text-xs text-center text-gray-500 mt-6">
                <i class="bi bi-info-circle"></i>
                You will be redirected to Flutterwave to complete the payment securely.
            </p>
        </div>
    </div>
</div>

<script>
    document.getElementById('flutterwaveBtn').addEventListener('click', function() {
        // Simulate Flutterwave redirect - In production, integrate Flutterwave SDK
        this.disabled = true;
        this.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Redirecting to Flutterwave...';
        
        // For now, mark payment as completed after 2 seconds
        setTimeout(() => {
            // In production, use actual Flutterwave API
            fetch('../payments/verify-payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    order_id: <?php echo $order_id; ?>,
                    payment_method: 'flutterwave',
                    status: 'completed'
                })
            }).then(() => {
                window.location.href = '../user/orders.php?success=1';
            });
        }, 2000);
    });
</script>

<?php include '../includes/footer.php'; ?>
