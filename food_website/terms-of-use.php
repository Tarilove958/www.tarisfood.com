<?php
require_once 'includes/config.php';
$page_title = 'Terms of Use';
include 'includes/header.php';
?>

<div class="bg-light py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12 border border-gray-100">
            <h1 class="font-bricolage font-bold text-3xl md:text-4xl text-dark mb-6">Terms of Use</h1>
            <p class="text-gray-500 mb-8 text-sm">Last Updated: <?php echo date('F d, Y'); ?></p>

            <div class="space-y-8 text-gray-700 leading-relaxed font-outfit">
                
                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">1. Acceptance of Terms</h2>
                    <p>By accessing and using <?php echo SITE_NAME; ?> ("the Website"), you accept and agree to be bound by the terms and provision of this agreement. In addition, when using this Website's particular services, you shall be subject to any posted guidelines or rules applicable to such services.</p>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">2. Description of Service</h2>
                    <p><?php echo SITE_NAME; ?> provides an online food ordering and delivery platform. We act as an intermediary between you and our kitchen/vendors. We are responsible for the transaction processing and the coordination of delivery.</p>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">3. User Account</h2>
                    <p>To access certain features of the Website, you may be required to create an account. You are responsible for maintaining the confidentiality of your account and password and for restricting access to your computer. You agree to accept responsibility for all activities that occur under your account or password.</p>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">4. Orders and Pricing</h2>
                    <ul class="list-disc pl-5 space-y-2">
                        <li>All orders are subject to availability.</li>
                        <li>Prices listed on the website are in Nigerian Naira (<?php echo CURRENCY; ?>) and are inclusive of relevant taxes unless stated otherwise.</li>
                        <li>Delivery fees are calculated based on your location and will be displayed at checkout.</li>
                        <li>We reserve the right to cancel any order in the event of a pricing error or stock unavailability.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">5. Payments</h2>
                    <p>We accept payments via Paystack and Flutterwave. By placing an order, you confirm that the payment details provided are valid and correct. Payment is deducted at the time the order is placed.</p>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">6. User Conduct</h2>
                    <p>You agree not to use the Website for any unlawful purpose. You must not transmit any worms, viruses, or any code of a destructive nature.</p>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">7. Limitation of Liability</h2>
                    <p>To the fullest extent permitted by applicable law, <?php echo SITE_NAME; ?> shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues.</p>
                </section>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>