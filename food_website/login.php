<?php
require_once 'includes/config.php';

// If user is already logged in, redirect appropriately
if (isLoggedIn()) {
    if (isAdmin()) {
        // Admin users go to admin dashboard
        redirect('admin/index.php');
    } else {
        // Regular users go to user dashboard
        redirect('user/index.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Get user from database - check user_type to determine routing
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] !== 'active') {
            setFlashMessage('error', 'Account is suspended. Please contact support.');
        } else {
            // Set session variables for user
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['full_name'];
            // CRITICAL: Get user_type from database - 'admin' or 'customer'
            $_SESSION['user_type'] = $user['user_type'] ?: 'customer';
            
            // Redirect based on user type
            if ($_SESSION['user_type'] === 'admin') {
                // Admin redirects to admin dashboard
                redirect('admin/index.php');
            } else {
                // Regular customers redirect to user dashboard
                redirect('user/index.php');
            }
        }
    } else {
        setFlashMessage('error', 'Invalid email or password.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    
    <!-- Favicon - Multiple formats for best browser compatibility -->
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="apple-touch-icon" href="assets/images/favicon.svg">
    <meta name="theme-color" content="#FF6B35">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
        <div class="text-center mb-8">
            <a href="index.php">
                <img src="assets/images/logo.png" alt="Logo" class="h-12 mx-auto mb-4 object-contain hover-scale">
            </a>
            <h2 class="text-2xl font-bold text-gray-800 font-bricolage">Welcome Back</h2>
            <p class="text-gray-500 text-sm">Please enter your details to sign in.</p>
        </div>
        
        <?php $flash = getFlashMessage(); if($flash): ?>
            <div class="p-4 rounded-xl mb-6 text-sm text-center border flex items-center justify-center gap-2
                <?php 
                if ($flash['type'] == 'success') {
                    echo 'bg-green-50 text-green-700 border-green-100';
                } else {
                    echo 'bg-red-50 text-red-700 border-red-100';
                }
                ?>">
                <i class="bi <?php echo $flash['type'] == 'success' ? 'bi-check-circle-fill text-green-600' : 'bi-exclamation-circle-fill text-red-600'; ?> text-lg"></i>
                <span><?php echo $flash['message']; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:border-[#0066CC] focus:ring-2 focus:ring-blue-100 transition-all bg-gray-50 focus:bg-white">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:border-[#0066CC] focus:ring-2 focus:ring-blue-100 transition-all bg-gray-50 focus:bg-white">
            </div>
            <button type="submit" class="w-full bg-[#0066CC] text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition hover:shadow-lg shadow-blue-200">Login</button>
        </form>
        
        <p class="mt-8 text-center text-sm text-gray-600">
            Don't have an account? <a href="register.php" class="text-[#0066CC] font-bold hover:underline">Register</a>
        </p>
         <p class="mt-4 text-center text-sm">
            <a href="index.php" class="text-gray-400 hover:text-gray-600">← Back to Home</a>
        </p>
    </div>
</body>
</html>