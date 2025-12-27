<?php
require_once 'includes/config.php';
$page_title = 'Return & Refund Policy';
include 'includes/header.php';
?>

<div class="bg-light py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12 border border-gray-100">
            <h1 class="font-bricolage font-bold text-3xl md:text-4xl text-dark mb-6">Return & Refund Policy</h1>
            
            <div class="bg-blue-50 border-l-4 border-primary p-4 mb-8">
                <p class="text-blue-800 font-medium">Note: Due to the perishable nature of food items, we generally do not accept returns. However, we are committed to ensuring you get exactly what you ordered.</p>
            </div>

            <div class="space-y-8 text-gray-700 leading-relaxed font-outfit">
                
                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">1. Wrong or Missing Items</h2>
                    <p>If you receive an item that is different from what you ordered, or if items are missing from your order, please contact us immediately (within 30 minutes of delivery) at <strong><?php echo SITE_EMAIL; ?></strong> or via our Help Center. We will verify the issue and arrange for the correct item to be delivered or issue a refund for the missing item.</p>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">2. Damaged or Spoiled Food</h2>
                    <p>We take great care in packaging and hygiene. However, if your food arrives spilled, damaged, or legally spoiled:</p>
                    <ul class="list-disc pl-5 space-y-2 mt-2">
                        <li>Take a clear photo of the item immediately upon opening.</li>
                        <li>Do not consume the item.</li>
                        <li>Contact support with your Order Number and the photo evidence.</li>
                    </ul>
                    <p class="mt-2">Upon verification, we will offer a replacement or a full refund.</p>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">3. Order Cancellation</h2>
                    <p>Once an order has been confirmed and the kitchen has started preparation (usually within 5 minutes of placing the order), it cannot be cancelled or refunded. If you cancel before preparation starts, a full refund will be processed.</p>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">4. Refunds</h2>
                    <p>Refunds are processed to the original payment method (Paystack/Flutterwave). Depending on your bank, it may take 5-10 business days for the funds to appear in your account.</p>
                </section>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>