<?php
// 1. FORCE ERROR REPORTING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Load Config Safely
$config_file = __DIR__ . '/config.php';
if (file_exists($config_file)) {
    require_once $config_file;
}

// 2.5 Load Theme Manager
$theme_manager_file = __DIR__ . '/theme-manager.php';
if (file_exists($theme_manager_file)) {
    require_once $theme_manager_file;
}

// 3. Start Session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 4. DEFINE DEFAULTS
if (!defined('SITE_NAME')) define('SITE_NAME', 'FoodHub');
if (!defined('ASSETS_URL')) define('ASSETS_URL', 'assets');

// 5. HELPER LOGIC
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
    
    <!-- Favicon - Multiple formats for best browser compatibility -->
    <link rel="icon" type="image/svg+xml" href="<?php echo ASSETS_URL; ?>/images/favicon.svg">
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/images/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo ASSETS_URL; ?>/images/favicon.svg">
    <meta name="theme-color" content="#FF6B35">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;800&family=Outfit:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Mapping reference colors to your variables for compatibility
                        brand: {
                            blue: '#0066CC', // Default if var fails
                            red: '#DC3545',
                            black: '#212529',
                            light: '#F8F9FA'
                        },
                        // Keep your original logical names mapped to CSS variables
                        primary: 'var(--primary, #0066CC)',
                        secondary: 'var(--secondary, #DC3545)',
                        dark: 'var(--dark, #212529)',
                        light: 'var(--light, #F8F9FA)',
                    },
                    fontFamily: {
                        heading: ['"Bricolage Grotesque"', 'sans-serif'],
                        body: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">

    <?php 
        $active_theme = function_exists('getActiveTheme') ? getActiveTheme() : null;
        if ($active_theme && isset($active_theme['theme_slug'])) {
            $theme_css_path = ASSETS_URL . '/themes/' . htmlspecialchars($active_theme['theme_slug']) . '.css?v=' . $active_theme['theme_id'];
        } else {
            $theme_css_path = ASSETS_URL . '/themes/default.css?v=1';
        }
    ?>
    <link rel="stylesheet" href="<?php echo $theme_css_path; ?>" id="theme-stylesheet">

    <style>
        /* Core Animations & Design (From Reference) */
        :root {
            /* Default fallback colors matching your reference request */
            --primary: #0066CC; 
            --secondary: #DC3545;
            --dark: #212529;
            --light: #F8F9FA;
            
            /* Theme overrides if they exist */
            <?php 
            if ($active_theme) {
                echo "--primary: " . htmlspecialchars($active_theme['primary_color']) . " !important;\n";
                echo "--secondary: " . htmlspecialchars($active_theme['secondary_color']) . " !important;\n";
                echo "--dark: " . htmlspecialchars($active_theme['dark_color']) . " !important;\n";
                echo "--light: " . htmlspecialchars($active_theme['light_color']) . " !important;\n";
            }
            ?>
        }

        body { font-family: 'Outfit', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Bricolage Grotesque', sans-serif; }

        .float-animation { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        /* Glassmorphism Nav Blob */
        .nav-blob {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 102, 204, 0.1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Mobile Menu Transition */
        #mobile-menu { transition: all 0.3s ease-in-out; }
    </style>
    
    <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/cart.js"></script>
</head>
<body class="font-body bg-gray-50 text-gray-800 antialiased flex flex-col min-h-screen selection:bg-brand-red selection:text-white">

    <nav class="fixed top-0 left-0 right-0 z-50 pt-4 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="nav-blob rounded-full px-6 py-3 flex items-center justify-between transition-all duration-300">
                
                <a href="/food_website/index.php" class="group">
                    <img src="/food_website/<?php echo ASSETS_URL; ?>/images/logo.png" alt="<?php echo SITE_NAME; ?>" class="h-10 w-auto object-contain group-hover:scale-105 transition-transform">
                </a>

                <div class="hidden md:flex items-center gap-8 font-medium text-sm text-gray-600">
                    <a href="/food_website/index.php" class="<?php echo $current_page == 'index' ? 'text-brand-blue font-bold' : 'hover:text-brand-red'; ?> transition-colors">Home</a>
                    <a href="/food_website/menu.php" class="<?php echo $current_page == 'menu' ? 'text-brand-blue font-bold' : 'hover:text-brand-red'; ?> transition-colors">Menu</a>
                    <a href="/food_website/about.php" class="<?php echo $current_page == 'about' ? 'text-brand-blue font-bold' : 'hover:text-brand-red'; ?> transition-colors">About</a>
                    <a href="/food_website/testimonial.php" class="<?php echo $current_page == 'testimonial' ? 'text-brand-blue font-bold' : 'hover:text-brand-red'; ?> transition-colors">Reviews</a>
                    <a href="/food_website/contact.php" class="<?php echo $current_page == 'contact' ? 'text-brand-blue font-bold' : 'hover:text-brand-red'; ?> transition-colors">Contact</a>
                </div>

                <div class="flex items-center gap-4">
                    <button class="p-2 text-gray-500 hover:text-brand-blue transition-colors">
                        <i class="bi bi-search text-lg"></i>
                    </button>

                    <a href="/food_website/cart.php" class="relative p-2 hover:bg-blue-50 rounded-full transition-colors group">
                        <i class="bi bi-basket2-fill text-xl text-brand-blue group-hover:scale-110 transition-transform"></i>
                        <?php if ($cartCount > 0): ?>
                        <span class="absolute top-0 right-0 w-5 h-5 bg-brand-red text-white text-xs font-bold flex items-center justify-center rounded-full border-2 border-white animate-pulse" data-cart-count>
                            <?php echo $cartCount; ?>
                        </span>
                        <?php endif; ?>
                    </a>

                    <?php if ($is_logged_in): ?>
                        <div class="hidden sm:flex items-center gap-3 pl-4 border-l border-gray-200">
                            <a href="/food_website/user/index.php" class="text-sm font-semibold hover:text-brand-blue">Dashboard</a>
                            <a href="/food_website/logout.php" class="p-2 text-gray-400 hover:text-brand-red transition-colors" title="Logout">
                                <i class="bi bi-box-arrow-right text-lg"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="hidden sm:flex items-center gap-2">
                            <a href="/food_website/login.php" class="px-5 py-2 rounded-full bg-brand-blue text-white text-sm font-semibold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 hover:-translate-y-0.5">
                                Login
                            </a>
                        </div>
                    <?php endif; ?>

                    <button id="mobile-menu-btn" class="md:hidden text-2xl text-brand-black p-1">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden absolute top-full left-0 right-0 mt-2 px-4 md:hidden z-40 pb-4">
            <div class="bg-white/95 backdrop-blur-md rounded-3xl p-6 shadow-xl border border-gray-100 flex flex-col gap-4 text-center">
                <a href="/food_website/index.php" class="py-2 font-medium text-gray-700 hover:text-brand-blue">Home</a>
                <a href="/food_website/menu.php" class="py-2 font-medium text-gray-700 hover:text-brand-blue">Menu</a>
                <a href="/food_website/about.php" class="py-2 font-medium text-gray-700 hover:text-brand-blue">About</a>
                <a href="/food_website/testimonial.php" class="py-2 font-medium text-gray-700 hover:text-brand-blue">Reviews</a>
                <a href="/food_website/contact.php" class="py-2 font-medium text-gray-700 hover:text-brand-blue">Contact</a>
                <hr class="border-gray-100">
                <?php if ($is_logged_in): ?>
                    <a href="/food_website/user/index.php" class="py-3 bg-brand-blue text-white rounded-xl font-bold shadow-lg shadow-blue-200">My Dashboard</a>
                    <a href="/food_website/logout.php" class="py-2 text-brand-red font-medium">Logout</a>
                <?php else: ?>
                    <a href="/food_website/login.php" class="py-3 bg-brand-blue text-white rounded-xl font-bold shadow-lg shadow-blue-200">Login</a>
                    <a href="/food_website/register.php" class="py-3 bg-gray-100 text-gray-800 rounded-xl font-bold">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="h-28"></div>

    <?php if(function_exists('getFlashMessage') && $flash = getFlashMessage()): 
        $color = $flash['type'] == 'success' ? 'bg-green-500' : ($flash['type'] == 'error' ? 'bg-brand-red' : 'bg-blue-500');
    ?>
        <div class="max-w-7xl mx-auto px-4 mb-6 animate-fade-in-down">
            <div class="<?php echo $color; ?> text-white px-6 py-3 rounded-2xl shadow-lg flex items-center justify-between backdrop-blur-sm bg-opacity-90">
                <span class="font-medium"><?php echo $flash['message']; ?></span>
                <button onclick="this.parentElement.style.display='none'" class="hover:bg-white/20 rounded-full p-1 transition"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
    <?php endif; ?>

    <script src="/food_website/assets/js/theme-system.js"></script>

    <script>
        // Mobile Menu Logic
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if(mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                const icon = mobileBtn.querySelector('i');
                if(mobileMenu.classList.contains('hidden')) {
                    icon.classList.replace('bi-x-lg', 'bi-list');
                } else {
                    icon.classList.replace('bi-list', 'bi-x-lg');
                }
            });
        }
    </script>

    <main class="flex-grow">