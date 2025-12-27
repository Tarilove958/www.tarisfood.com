<?php
require_once 'includes/config.php';
$page_title = 'Cookie Policy';
include 'includes/header.php';
?>

<div class="bg-light py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12 border border-gray-100">
            <h1 class="font-bricolage font-bold text-3xl md:text-4xl text-dark mb-6">Cookie Policy</h1>

            <div class="space-y-8 text-gray-700 leading-relaxed font-outfit">
                
                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">1. What Are Cookies?</h2>
                    <p>Cookies are small text files that are stored on your device (computer, tablet, or mobile) when you visit a website. They help the website function properly, make it more secure, provide better user experience, and understand how the website performs.</p>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">2. How We Use Cookies</h2>
                    <p>At <?php echo SITE_NAME; ?>, we use cookies for the following purposes:</p>
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div class="p-4 border rounded-lg">
                            <h3 class="font-bold text-primary mb-2">Essential Cookies</h3>
                            <p class="text-sm">Necessary for the website to function. These include cookies that allow you to log into secure areas, use the shopping cart, and make payments.</p>
                        </div>
                        <div class="p-4 border rounded-lg">
                            <h3 class="font-bold text-primary mb-2">Functional Cookies</h3>
                            <p class="text-sm">These help us remember your settings and preferences, such as your delivery location or language.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">3. Third-Party Cookies</h2>
                    <p>In addition to our own cookies, we may also use various third-parties cookies to report usage statistics of the Service and deliver advertisements on and through the Service. This includes:</p>
                    <ul class="list-disc pl-5 mt-2">
                        <li>Payment Gateways (Paystack, Flutterwave) for secure transaction processing.</li>
                        <li>Google Analytics for traffic analysis.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">4. Managing Cookies</h2>
                    <p>You can control and/or delete cookies as you wish. You can delete all cookies that are already on your computer and you can set most browsers to prevent them from being placed. If you do this, however, you may have to manually adjust some preferences every time you visit a site and some services and functionalities (like the Shopping Cart) may not work.</p>
                </section>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>