<?php
require_once 'includes/config.php';
$page_title = 'Privacy Policy';
include 'includes/header.php';
?>

<div class="bg-light py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12 border border-gray-100">
            <h1 class="font-bricolage font-bold text-3xl md:text-4xl text-dark mb-6">Privacy Policy</h1>
            <p class="text-gray-500 mb-8 text-sm">We are committed to protecting your privacy in accordance with the Nigeria Data Protection Regulation (NDPR).</p>

            <div class="space-y-8 text-gray-700 leading-relaxed font-outfit">
                
                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">1. Information We Collect</h2>
                    <p>To provide you with our food delivery services, we collect the following personal information:</p>
                    <ul class="list-disc pl-5 space-y-2 mt-2">
                        <li><strong>Personal Identity:</strong> Name, Email Address.</li>
                        <li><strong>Contact Details:</strong> Phone Number, Delivery Address.</li>
                        <li><strong>Transaction Data:</strong> Details about payments to and from you and other details of products you have purchased from us. (Note: We do not store your credit card details; these are handled securely by our payment partners).</li>
                    </ul>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">2. How We Use Your Information</h2>
                    <p>We use your data to:</p>
                    <ul class="list-disc pl-5 space-y-2 mt-2">
                        <li>Process and deliver your orders.</li>
                        <li>Manage your account registration.</li>
                        <li>Communicate with you regarding your order status.</li>
                        <li>Improve our website and customer service.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">3. Data Sharing</h2>
                    <p>We do not sell your personal data. However, we share necessary data with:</p>
                    <ul class="list-disc pl-5 mt-2">
                        <li><strong>Delivery Riders:</strong> Your name, phone number, and address are shared with riders to facilitate delivery.</li>
                        <li><strong>Payment Processors:</strong> To process your payments securely.</li>
                        <li><strong>Law Enforcement:</strong> If required by law to comply with a legal process.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">4. Data Security</h2>
                    <p>We have implemented appropriate security measures to prevent your personal data from being accidentally lost, used, or accessed in an unauthorized way. Access to your personal data is limited to employees and partners who have a business need to know.</p>
                </section>

                <section>
                    <h2 class="font-bold text-xl text-dark mb-3">5. Your Rights</h2>
                    <p>Under the NDPR, you have the right to request access to your personal data, request correction of your data, or request erasure of your data. To exercise these rights, please contact us at <?php echo SITE_EMAIL; ?>.</p>
                </section>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>