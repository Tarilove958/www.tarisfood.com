<?php
// 1. FORCE ERROR REPORTING (So you see errors instead of a white screen)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Load Config Safely
$config_file = __DIR__ . '/config.php';
if (file_exists($config_file)) {
    require_once $config_file;
}

// 3. Start Session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 4. DEFINE DEFAULTS (These prevent the blank screen if config.php fails)
if (!defined('SITE_NAME')) define('SITE_NAME', 'FoodHub');
if (!defined('ASSETS_URL')) define('ASSETS_URL', 'assets');

// 5. HELPER LOGIC (Checks if functions exist before using them)
$cartCount = 0;
if (function_exists('getCartCount')) {
    $cartCount = getCartCount();
}

$is_logged_in = false;
if (function_exists('isLoggedIn')) {
    $is_logged_in = isLoggedIn();
}

// 6. Get Page Title
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title><?php echo defined('PAGE_TITLE') ? PAGE_TITLE . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/images/favicon.png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0066CC', // Blue
                        secondary: '#DC3545', // Red
                        dark: '#212529',
                        light: '#F8F9FA',
                    },
                    fontFamily: {
                        heading: ['"Bricolage Grotesque"', 'sans-serif'],
                        body: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .font-heading { font-family: 'Bricolage Grotesque', sans-serif; }
    </style>
    <!-- Main JavaScript - Load First! -->
<script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>

<!-- Cart JavaScript - Load After main.js -->
<script src="<?php echo ASSETS_URL; ?>/js/cart.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                
                <a href="/food_website/index.php" class="flex items-center gap-2">
                    <img src="/food_website/<?php echo ASSETS_URL; ?>/images/logo.png" alt="<?php echo SITE_NAME; ?>" class="h-10 w-auto object-contain">
                </a>

                <nav class="hidden md:flex items-center gap-8 font-medium">
                    <a href="/food_website/index.php" class="<?php echo $current_page == 'index' ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary'; ?>">Home</a>
                    <a href="/food_website/menu.php" class="<?php echo $current_page == 'menu' ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary'; ?>">Menu</a>
                    <a href="/food_website/about.php" class="<?php echo $current_page == 'about' ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary'; ?>">About</a>
                    <a href="/food_website/testimonial.php" class="<?php echo $current_page == 'testimonial' ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary'; ?>">Testimonials</a>
                    <a href="/food_website/contact.php" class="<?php echo $current_page == 'contact' ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary'; ?>">Contact</a>
                </nav>

                <div class="flex items-center gap-4">
                    <a href="/food_website/cart.php" class="relative p-2 text-gray-700 hover:text-primary transition-colors duration-200 group">
                        <i class="bi bi-cart3 text-2xl group-hover:scale-110 transition-transform duration-200"></i>
                        <?php if ($cartCount > 0): ?>
                        <span class="absolute top-0 right-0 w-5 h-5 bg-secondary text-white text-[10px] font-bold flex items-center justify-center rounded-full animate-pulse" data-cart-count>
                            <?php echo $cartCount; ?>
                        </span>
                        <?php endif; ?>
                    </a>

                    <?php if ($is_logged_in): ?>
                        <div class="hidden md:flex items-center gap-3">
                            <a href="/food_website/user/index.php" class="text-sm font-bold text-gray-700">Dashboard</a>
                            <a href="/food_website/logout.php" class="px-4 py-2 bg-secondary text-white rounded-full text-sm font-bold hover:bg-red-700">Logout</a>
                        </div>
                    <?php else: ?>
                        <div class="hidden md:flex items-center gap-3">
                            <a href="/food_website/login.php" class="text-sm font-bold text-gray-600 hover:text-primary">Login</a>
                            <a href="/food_website/register.php" class="px-5 py-2 bg-primary text-white rounded-full text-sm font-bold hover:bg-blue-700">Register</a>
                        </div>
                    <?php endif; ?>

                    <button id="mobile-menu-btn" class="md:hidden text-2xl text-gray-700">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 p-4">
            <nav class="flex flex-col gap-3">
                <a href="/food_website/index.php" class="text-gray-700 hover:text-primary">Home</a>
                <a href="/food_website/menu.php" class="text-gray-700 hover:text-primary">Menu</a>
                <a href="/food_website/about.php" class="text-gray-700 hover:text-primary">About</a>
                <a href="/food_website/contact.php" class="text-gray-700 hover:text-primary">Contact</a>
                <hr>
                <?php if ($is_logged_in): ?>
                    <a href="/food_website/user/index.php" class="font-bold text-primary">Dashboard</a>
                    <a href="/food_website/logout.php" class="text-secondary">Logout</a>
                <?php else: ?>
                    <a href="/food_website/login.php" class="font-bold text-gray-700">Login</a>
                    <a href="/food_website/register.php" class="font-bold text-primary">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            var menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>

    <main class="flex-grow">