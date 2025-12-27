<?php
require_once 'includes/config.php';

// Redirect if cart is empty
if (getCartCount() == 0) {
    setFlashMessage('warning', 'Your cart is empty.');
    redirect('menu.php');
}

if (!isLoggedIn()) {
    setFlashMessage('info', 'Please login to checkout.');
    redirect('login.php?redirect=checkout.php');
}

$page_title = 'Checkout';
include 'includes/header.php';

// Get cart details
$cartTotal = getCartTotal();

// Ensure cart total is a valid number
if (!is_numeric($cartTotal) || $cartTotal < 0) {
    $cartTotal = 0;
}

// Get delivery fee from settings (default to 1500 if not set)
$deliveryFee = 1500; // You can modify this to read from admin settings
try {
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'delivery_fee' LIMIT 1");
    $setting = $stmt->fetch();
    if ($setting && !empty($setting['setting_value'])) {
        $deliveryFee = floatval($setting['setting_value']);
    }
} catch (Exception $e) {
    // Use default if settings table doesn't exist
    $deliveryFee = 1500;
}

// Ensure delivery fee is a valid number
if (!is_numeric($deliveryFee) || $deliveryFee < 0) {
    $deliveryFee = 1500;
}

// Calculate grand total (subtotal + delivery fee)
$grandTotal = floatval($cartTotal) + floatval($deliveryFee);
?>

<div class="bg-light py-12">
    <div class="max-w-4xl mx-auto px-4">
        <h1 class="font-bricolage font-bold text-3xl mb-8">Checkout</h1>
        
        <form action="includes/processOrder.php" method="POST" class="grid md:grid-cols-2 gap-8" id="checkoutForm">
            <?php echo getCSRFInput(); ?>
            <!-- Hidden inputs for totals - These are required for order processing -->
            <input type="hidden" name="subtotal" value="<?php echo number_format($cartTotal, 2, '.', ''); ?>">
            <input type="hidden" name="delivery_fee" value="<?php echo number_format($deliveryFee, 2, '.', ''); ?>">
            <input type="hidden" name="total_amount" value="<?php echo number_format($grandTotal, 2, '.', ''); ?>">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg mb-6 flex items-center gap-2 pb-2 border-b">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-primary flex items-center justify-center text-sm"><i class="bi bi-geo-alt-fill"></i></div>
                    Delivery Information
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="full_name" value="<?php echo $_SESSION['user_name'] ?? ''; ?>" required class="w-full border rounded-lg p-2.5 focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" name="phone" required class="w-full border rounded-lg p-2.5 focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                        <textarea name="address" rows="3" required class="w-full border rounded-lg p-2.5 focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="city" required class="w-full border rounded-lg p-2.5 focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <input type="text" name="state" required class="w-full border rounded-lg p-2.5 focus:border-primary focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-lg mb-4">Payment Method</h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer hover:border-primary hover:bg-blue-50 transition-all group">
                            <input type="radio" name="payment_method" value="paystack" checked class="text-primary focus:ring-primary w-5 h-5">
                            <div class="flex-1">
                                <span class="font-semibold block">Paystack</span>
                                <span class="text-xs text-gray-500">Cards, Bank Transfer, USSD</span>
                            </div>
                            <i class="bi bi-credit-card text-gray-400 group-hover:text-primary"></i>
                        </label>
                        <label class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer hover:border-primary hover:bg-blue-50 transition-all group">
                            <input type="radio" name="payment_method" value="flutterwave" class="text-primary focus:ring-primary w-5 h-5">
                            <div class="flex-1">
                                <span class="font-semibold block">Flutterwave</span>
                                <span class="text-xs text-gray-500">Secure Payment Gateway</span>
                            </div>
                            <i class="bi bi-wallet2 text-gray-400 group-hover:text-primary"></i>
                        </label>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-lg mb-6 flex items-center gap-2 pb-4 border-b">
                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm"><i class="bi bi-wallet2"></i></div>
                        Order Summary
                    </h3>
                    
                    <!-- Order Items -->
                    <div class="space-y-3 mb-6 pb-6 border-b">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold text-dark"><?php echo formatPrice($cartTotal); ?></span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Delivery Fee</span>
                            <span class="font-semibold text-secondary"><?php echo formatPrice($deliveryFee); ?></span>
                        </div>
                    </div>
                    
                    <!-- Total Amount -->
                    <div class="flex justify-between items-center text-3xl font-bold text-dark mb-6 font-bricolage">
                        <span>Total</span>
                        <span class="text-primary"><?php echo formatPrice($grandTotal); ?></span>
                    </div>
                    
                    <button type="submit" class="w-full py-4 bg-primary text-white rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-xl transition-all hover:scale-[1.02]">
                        Pay Now <?php echo formatPrice($grandTotal); ?>
                    </button>
                    <p class="text-xs text-center text-gray-400 mt-4 flex items-center justify-center gap-1">
                        <i class="bi bi-lock-fill"></i> Payments are 100% secure and encrypted.
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/checkout.js"></script>

<?php include 'includes/footer.php'; ?>