<?php
require_once 'includes/config.php';
$page_title = 'My Cart';
include 'includes/header.php';

// Fetch Cart Items
$cartItems = [];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT c.*, p.product_name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->prepare("SELECT c.*, p.product_name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.session_id = ?");
    $stmt->execute([getSessionId()]);
}
$cartItems = $stmt->fetchAll();
$total = 0;

// Get delivery fee
$deliveryFee = 1500;
try {
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'delivery_fee' LIMIT 1");
    $setting = $stmt->fetch();
    if ($setting && !empty($setting['setting_value'])) {
        $deliveryFee = floatval($setting['setting_value']);
    }
} catch (Exception $e) {
    $deliveryFee = 1500;
}

// Calculate totals
$grandTotal = $total + $deliveryFee;
?>

<section class="py-12 max-w-4xl mx-auto px-4 min-h-[60vh]">
    <h1 class="font-bricolage font-bold text-3xl mb-8">Shopping Cart</h1>

    <?php if(count($cartItems) > 0): ?>
    <div class="grid md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-4">
            <?php foreach($cartItems as $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <div class="flex gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100 items-center animate-fade-in">
                <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    <?php if($item['image']): ?>
                        <?php 
                        $imageUrl = (!empty($item['image']) && strpos($item['image'], 'http') === 0) 
                            ? $item['image'] 
                            : 'assets/images/products/' . $item['image'];
                        ?>
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="bi bi-image"></i></div>
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-dark text-lg"><?php echo $item['product_name']; ?></h3>
                    <p class="text-gray-500 text-sm mb-2">Price: <span data-item-price="<?php echo $item['price']; ?>"><?php echo formatPrice($item['price']); ?></span></p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center border border-gray-200 rounded-lg">
                            <input type="number" 
                                   class="cart-qty-input w-16 text-center py-1 bg-transparent focus:outline-none font-semibold text-dark" 
                                   value="<?php echo $item['quantity']; ?>" 
                                   min="1"
                                   data-cart-id="<?php echo $item['cart_id']; ?>">
                        </div>
                        
                        <form action="includes/removeCart.php" method="POST" class="ajax-remove-cart">
                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                            <button type="submit" class="text-red-500 text-sm hover:text-red-700 font-medium flex items-center gap-1 transition-colors">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        </form>
                    </div>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="font-bold text-lg text-primary" data-item-subtotal data-item-total="<?php echo $subtotal; ?>"><?php echo formatPrice($subtotal); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            
            <form action="includes/clearCart.php" method="POST" class="text-right ajax-clear-cart">
                 <?php echo getCSRFInput(); ?>
                 <button type="submit" class="text-sm text-red-500 font-semibold hover:underline mt-4 px-4 py-2 hover:bg-red-50 rounded transition-colors">
                    <i class="bi bi-x-circle"></i> Clear Entire Cart
                 </button>
            </form>
        </div>

        <div class="md:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-lg shadow-gray-100 border border-gray-100 sticky top-24">
                <h3 class="font-bold text-lg mb-4 pb-4 border-b">Order Summary</h3>
                <div class="flex justify-between mb-2 text-gray-600">
                    <span>Subtotal</span>
                    <span data-cart-subtotal><?php echo formatPrice($total); ?></span>
                </div>
                <div class="flex justify-between mb-4 text-gray-600">
                    <span>Delivery Fee</span>
                    <span class="font-semibold text-secondary"><?php echo formatPrice($deliveryFee); ?></span>
                </div>
                <div class="border-t pt-4 flex justify-between font-bold text-xl text-dark mb-6">
                    <span>Total</span>
                    <span data-cart-total><?php echo formatPrice($total + $deliveryFee); ?></span>
                </div>
                <a href="checkout.php" class="block w-full py-3 bg-secondary text-white text-center rounded-xl font-bold hover:bg-red-700 transition-colors shadow-lg shadow-red-100 hover:shadow-xl hover:-translate-y-1">
                    Proceed to Checkout
                </a>
                <a href="menu.php" class="block text-center mt-4 text-sm text-gray-500 hover:text-dark">Continue Shopping</a>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-dashed border-gray-200">
            <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                <i class="bi bi-cart-x text-4xl text-primary"></i>
            </div>
            <h2 class="text-2xl font-bold text-dark mb-2">Your cart is empty</h2>
            <p class="text-gray-500 mb-6">Hungry? Explore our delicious menu now.</p>
            <a href="menu.php" class="px-8 py-3 bg-primary text-white rounded-full font-bold hover:bg-blue-700 transition-all hover:shadow-lg">Browse Menu</a>
        </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>