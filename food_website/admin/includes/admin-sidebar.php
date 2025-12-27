<?php
// Get current page name to highlight active link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside id="sidebar" class="bg-gray-900 text-white w-64 min-h-screen flex-col transition-all duration-300 hidden md:flex fixed md:relative z-30">
    <div class="h-20 flex items-center justify-center border-b border-gray-800 bg-gray-900 sticky top-0">
        <a href="../index.php" class="flex items-center gap-2">
            <span class="font-heading font-extrabold text-2xl tracking-wider">
                Food<span class="text-primary">Hub</span>
            </span>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-3">
        <ul class="space-y-1">
            <li>
                <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="pt-4 pb-2 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Store Management</li>

            <li>
                <a href="manage-orders.php" class="<?php echo strpos($current_page, 'order') !== false ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium">
                    <i class="bi bi-receipt"></i>
                    <span>Orders</span>
                    </a>
            </li>

            <li>
                <a href="manage-products.php" class="<?php echo strpos($current_page, 'product') !== false ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium">
                    <i class="bi bi-basket2-fill"></i>
                    <span>Products</span>
                </a>
            </li>

            <li>
                <a href="manage-categories.php" class="<?php echo strpos($current_page, 'categor') !== false ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium">
                    <i class="bi bi-tags-fill"></i>
                    <span>Categories</span>
                </a>
            </li>

            <li class="pt-4 pb-2 px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Users & Settings</li>

            <li>
                <a href="manage-users.php" class="<?php echo $current_page == 'manage-users.php' ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium">
                    <i class="bi bi-people-fill"></i>
                    <span>Customers</span>
                </a>
            </li>

            <li>
                <a href="manage-testimonials.php" class="<?php echo $current_page == 'manage-testimonials.php' ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium">
                    <i class="bi bi-chat-quote-fill"></i>
                    <span>Testimonials</span>
                </a>
            </li>

            <li>
                <a href="settings.php" class="<?php echo $current_page == 'settings.php' ? 'bg-primary text-white shadow-lg' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-xl transition-all font-medium">
                    <i class="bi bi-gear-fill"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="p-4 border-t border-gray-800">
        <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all font-bold">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>