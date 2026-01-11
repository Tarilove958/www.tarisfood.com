<?php
// Get current page name to highlight active link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside id="sidebar" class="bg-gradient-to-b from-gray-900 to-gray-950 text-white w-64 flex-shrink-0 flex flex-col transition-all duration-300 fixed left-0 top-0 bottom-0 md:static hidden md:flex z-30 border-r border-gray-800 h-screen">
    <!-- Logo Section -->
    <div class="h-20 flex items-center justify-center border-b border-gray-800/50 bg-gray-900/50 sticky top-0 z-10">
        <a href="../index.php" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold">
                <i class="bi bi-shop"></i>
            </div>
            <span class="font-heading font-extrabold text-lg tracking-wider hidden sm:block">
                Food<span class="text-primary">Hub</span>
            </span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-6 px-3 scroll-smooth">
        <!-- Dashboard Section -->
        <div class="mb-2">
            <li class="list-none">
                <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'bg-primary shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium relative group">
                    <i class="bi bi-grid-fill text-lg"></i>
                    <span>Dashboard</span>
                    <?php if($current_page == 'index.php'): ?>
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-l"></div>
                    <?php endif; ?>
                </a>
            </li>
        </div>

        <!-- Store Management -->
        <div class="mt-6 mb-4">
            <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Store Management</p>
            <ul class="space-y-1">
                <li>
                    <a href="manage-orders.php" class="<?php echo strpos($current_page, 'order') !== false ? 'bg-primary shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium relative group">
                        <i class="bi bi-receipt text-lg"></i>
                        <span>Orders</span>
                        <?php if(strpos($current_page, 'order') !== false): ?>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-l"></div>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="manage-products.php" class="<?php echo strpos($current_page, 'product') !== false ? 'bg-primary shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium relative group">
                        <i class="bi bi-basket2-fill text-lg"></i>
                        <span>Products</span>
                        <?php if(strpos($current_page, 'product') !== false): ?>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-l"></div>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="manage-categories.php" class="<?php echo strpos($current_page, 'categor') !== false ? 'bg-primary shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium relative group">
                        <i class="bi bi-tags-fill text-lg"></i>
                        <span>Categories</span>
                        <?php if(strpos($current_page, 'categor') !== false): ?>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-l"></div>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Users & Settings -->
        <div class="mt-6 mb-4">
            <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Users & Settings</p>
            <ul class="space-y-1">
                <li>
                    <a href="manage-users.php" class="<?php echo $current_page == 'manage-users.php' ? 'bg-primary shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium relative group">
                        <i class="bi bi-people-fill text-lg"></i>
                        <span>Customers</span>
                        <?php if($current_page == 'manage-users.php'): ?>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-l"></div>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="manage-testimonials.php" class="<?php echo $current_page == 'manage-testimonials.php' ? 'bg-primary shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium relative group">
                        <i class="bi bi-chat-quote-fill text-lg"></i>
                        <span>Testimonials</span>
                        <?php if($current_page == 'manage-testimonials.php'): ?>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-l"></div>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="manage-themes.php" class="<?php echo strpos($current_page, 'theme') !== false ? 'bg-primary shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium relative group">
                        <i class="bi bi-palette-fill text-lg"></i>
                        <span>Themes</span>
                        <?php if(strpos($current_page, 'theme') !== false): ?>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-l"></div>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="settings.php" class="<?php echo $current_page == 'settings.php' ? 'bg-primary shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white'; ?> flex items-center gap-3 px-4 py-3 rounded-lg transition-all font-medium relative group">
                        <i class="bi bi-gear-fill text-lg"></i>
                        <span>Settings</span>
                        <?php if($current_page == 'settings.php'): ?>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-l"></div>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Logout Section -->
    <div class="p-4 border-t border-gray-800/50 bg-gray-900/50">
        <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all font-bold">
            <i class="bi bi-box-arrow-right text-lg"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>