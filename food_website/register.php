<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = sanitize($_POST['phone']);
    
    // Check if email exists
    $check = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->execute([$email]);
    
    if ($check->rowCount() > 0) {
        setFlashMessage('error', 'Email already registered.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, phone, user_type) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $password, $phone, 'customer'])) {
            setFlashMessage('success', 'Registration successful! Please login.');
            redirect('login.php');
        } else {
            setFlashMessage('error', 'Registration failed. Try again.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    
    <!-- Favicon - Multiple formats for best browser compatibility -->
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="apple-touch-icon" href="assets/images/favicon.svg">
    <meta name="theme-color" content="#FF6B35">
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen py-10">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Create Account</h2>
        </div>
        
        <?php $flash = getFlashMessage(); if($flash): ?>
            <div class="p-4 rounded-lg mb-4 text-sm text-center flex items-center justify-center gap-2
                <?php 
                if ($flash['type'] == 'success') {
                    echo 'bg-green-50 text-green-700 border border-green-200';
                } else {
                    echo 'bg-red-50 text-red-700 border border-red-200';
                }
                ?>">
                <i class="bi <?php echo $flash['type'] == 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'; ?>"></i>
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="full_name" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
            </div>
             <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel" name="phone" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-600">
            </div>
            <button type="submit" class="w-full bg-[#0066CC] text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">Register</button>
        </form>
        
        <p class="mt-6 text-center text-sm text-gray-600">
            Already have an account? <a href="login.php" class="text-[#0066CC] font-bold hover:underline">Login</a>
        </p>
    </div>
</body>
</html>