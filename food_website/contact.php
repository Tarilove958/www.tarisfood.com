<?php
require_once 'includes/config.php';
$page_title = 'Contact Us';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    // Insert into DB
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $subject, $message])) {
        setFlashMessage('success', 'Message sent successfully! We will get back to you soon.');
    } else {
        setFlashMessage('error', 'Failed to send message.');
    }
}

include 'includes/header.php';
?>

<section class="py-16 max-w-6xl mx-auto px-4">
    <div class="grid md:grid-cols-2 gap-12">
        <div>
            <h1 class="font-bricolage font-bold text-4xl mb-4">Get in Touch</h1>
            <p class="text-gray-600 mb-8">Have a question about your order or our menu? Send us a message.</p>
            
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-light flex items-center justify-center text-primary">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <p><?php echo SITE_EMAIL; ?></p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-light flex items-center justify-center text-primary">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <p>Bayelsa, Nigeria</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-8 rounded-3xl shadow-lg shadow-gray-100 border border-gray-100">
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" name="name" required class="w-full border rounded-lg p-3 focus:border-primary focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" required class="w-full border rounded-lg p-3 focus:border-primary focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Subject</label>
                    <input type="text" name="subject" required class="w-full border rounded-lg p-3 focus:border-primary focus:outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Message</label>
                    <textarea name="message" rows="4" required class="w-full border rounded-lg p-3 focus:border-primary focus:outline-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-dark text-white py-3 rounded-lg font-bold hover:bg-primary transition-colors">Send Message</button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>