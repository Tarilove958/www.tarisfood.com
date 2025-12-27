<?php
require_once 'includes/config.php';
$page_title = 'Help Center';
include 'includes/header.php';
?>

<section class="py-16 max-w-4xl mx-auto px-4">
    <h1 class="font-bricolage font-bold text-4xl mb-8 text-center">How can we help?</h1>
    
    <div class="space-y-4">
        <div class="border border-gray-200 rounded-xl p-6 bg-white">
            <h3 class="font-bold text-lg mb-2 text-primary">How long does delivery take?</h3>
            <p class="text-gray-600">We aim to deliver within 30-45 minutes of order confirmation within Lagos.</p>
        </div>
        
        <div class="border border-gray-200 rounded-xl p-6 bg-white">
            <h3 class="font-bold text-lg mb-2 text-primary">What payment methods do you accept?</h3>
            <p class="text-gray-600">We accept secure payments via Paystack and Flutterwave (Cards, Bank Transfer, USSD).</p>
        </div>
        
        <div class="border border-gray-200 rounded-xl p-6 bg-white">
            <h3 class="font-bold text-lg mb-2 text-primary">Can I cancel my order?</h3>
            <p class="text-gray-600">You can cancel your order within 5 minutes of placing it via your user dashboard.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>