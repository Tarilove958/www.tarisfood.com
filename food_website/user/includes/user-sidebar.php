<?php
// User Dashboard Sidebar - Can be used for user navigation menu
if (!defined('ABSPATH')) {
    exit('Direct access not allowed');
}

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="bg-white rounded-2xl border border-gray-100 p-6">
    <h3 class="font-bricolage font-bold text-lg text-dark mb-4">Navigation</h3>
    <nav class="space-y-2">
        <a href="index.php" class="block px-4 py-2 rounded-lg transition-colors <?php echo $current_page == 'index' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a href="orders.php" class="block px-4 py-2 rounded-lg transition-colors <?php echo $current_page == 'orders' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="bi bi-bag-check me-2"></i> My Orders
        </a>
        <a href="profile.php" class="block px-4 py-2 rounded-lg transition-colors <?php echo $current_page == 'profile' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="bi bi-person me-2"></i> Profile
        </a>
        <a href="change-password.php" class="block px-4 py-2 rounded-lg transition-colors <?php echo $current_page == 'change-password' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="bi bi-shield-lock me-2"></i> Change Password
        </a>
    </nav>
</aside>
