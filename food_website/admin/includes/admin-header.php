<?php
// 1. FAIL-SAFE: Force Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Load Main Configuration (Go up 2 levels: admin/includes -> admin -> root)
$configPath = dirname(dirname(__DIR__)) . '/includes/config.php';
$funcPath = dirname(dirname(__DIR__)) . '/includes/functions.php';
$adminFuncPath = dirname(__DIR__) . '/includes/admin-functions.php';
$themeManagerPath = dirname(dirname(__DIR__)) . '/includes/theme-manager.php';

if (file_exists($configPath)) require_once $configPath;
if (file_exists($funcPath)) require_once $funcPath;
if (file_exists($themeManagerPath)) require_once $themeManagerPath;
if (file_exists($adminFuncPath)) require_once $adminFuncPath;

// 4. SECURITY: Check Admin Access - Enhanced Validation
// Only admins can access admin pages

// Debug: Check if functions are loaded
if (!function_exists('isLoggedIn') || !function_exists('isAdmin')) {
    header("Location: ../login.php?error=session");
    exit("Session functions not loaded. Please contact administrator.");
}

if (!isLoggedIn()) {
    // Not logged in - redirect to main login page
    header("Location: ../login.php");
    exit();
}

// Validate session data is present
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    // Session corrupted, force re-login
    session_destroy();
    header("Location: ../login.php?error=session");
    exit();
}

if (!isAdmin()) {
    // Logged in but not an admin - redirect to user dashboard
    header("Location: ../user/index.php");
    exit();
}

// 5. DEFAULTS (Prevent crashes if config fails)
if (!defined('SITE_NAME')) define('SITE_NAME', 'FoodHub');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    
    <!-- Favicon - Multiple formats for best browser compatibility -->
    <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <link rel="apple-touch-icon" href="../assets/images/favicon.svg">
    <meta name="theme-color" content="#FF6B35">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Admin Dynamic Theme Styles -->
    <link rel="stylesheet" href="../includes/admin-theme.php">
    
    <!-- Admin Modern Styles -->
    <link rel="stylesheet" href="../assets/css/admin-modern.css">
    
    <?php
    // Get active theme colors
    $activeTheme = getActiveTheme();
    $primaryColor = $activeTheme['primary_color'] ?? '#3b82f6';
    $secondaryColor = $activeTheme['secondary_color'] ?? '#f97316';
    $darkColor = $activeTheme['dark_color'] ?? '#1f2937';
    $lightColor = $activeTheme['light_color'] ?? '#f9fafb';
    ?>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo $primaryColor; ?>',
                        secondary: '<?php echo $secondaryColor; ?>',
                        dark: '<?php echo $darkColor; ?>',
                        light: '<?php echo $lightColor; ?>',
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        #app-container {
            display: flex;
            width: 100%;
            height: 100%;
            background: #f9fafb;
        }
        
        @media (max-width: 768px) {
            #sidebar.hidden {
                display: none !important;
            }
            
            #sidebar:not(.hidden) {
                display: flex !important;
            }
        }
        
        .content-wrapper {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        header {
            flex-shrink: 0;
            height: 80px;
        }
        
        main {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        footer {
            flex-shrink: 0;
        }
        
        :root {
            --primary: <?php echo $primaryColor; ?>;
            --secondary: <?php echo $secondaryColor; ?>;
            --dark: <?php echo $darkColor; ?>;
            --light: <?php echo $lightColor; ?>;
        }
        
        body { 
            font-family: 'Outfit', sans-serif; 
            --primary: <?php echo $primaryColor; ?> !important;
            --secondary: <?php echo $secondaryColor; ?> !important;
        }
        
        /* Override all primary color references */
        .bg-primary {
            background-color: <?php echo $primaryColor; ?> !important;
        }
        
        .text-primary {
            color: <?php echo $primaryColor; ?> !important;
        }
        
        .border-primary {
            border-color: <?php echo $primaryColor; ?> !important;
        }
        
        .hover\:bg-primary:hover {
            background-color: <?php echo $primaryColor; ?> !important;
        }
        
        .focus\:border-primary:focus {
            border-color: <?php echo $primaryColor; ?> !important;
        }
        
        /* Secondary colors */
        .bg-secondary {
            background-color: <?php echo $secondaryColor; ?> !important;
        }
        
        .text-secondary {
            color: <?php echo $secondaryColor; ?> !important;
        }
        
        /* Dark colors */
        .bg-dark {
            background-color: <?php echo $darkColor; ?> !important;
        }
        
        .text-dark {
            color: <?php echo $darkColor; ?> !important;
        }
        
        /* Light colors */
        .bg-light {
            background-color: <?php echo $lightColor; ?> !important;
        }
        
        .text-light {
            color: <?php echo $lightColor; ?> !important;
        }
        
        /* Modern Card Styling */
        .admin-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .admin-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        /* Modern Stat Card */
        .stat-card {
            position: relative;
            overflow: hidden;
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #E5E7EB;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .stat-card:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            transform: translateY(-4px);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 600;
        }
        
        /* Sidebar Active Item */
        .sidebar-active {
            background-color: <?php echo $primaryColor; ?> !important;
            color: white !important;
        }
        
        /* Header styling */
        header {
            background: white;
            border-bottom: 1px solid #E5E7EB;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        /* Button Styling */
        button, .btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            border-radius: 8px;
        }
        
        button:hover, .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        button:active, .btn:active {
            transform: translateY(0);
        }
        
        /* Input Styling */
        input, textarea, select {
            border-radius: 8px;
            border: 1.5px solid #E5E7EB;
            transition: all 0.3s ease;
        }
        
        input:focus, textarea:focus, select:focus {
            border-color: <?php echo $primaryColor; ?>;
            box-shadow: 0 0 0 3px rgba(<?php 
                list($r, $g, $b) = sscanf($primaryColor, "#%02x%02x%02x");
                echo "$r, $g, $b, 0.1)";
            ?>;
        }
        
        /* Table Modern Styling */
        table tbody tr {
            border-bottom: 1px solid #F3F4F6;
        }
        
        table tbody tr:hover {
            background-color: #F9FAFB;
        }
        
        /* Status Badges */
        .badge-success {
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
            color: #047857;
        }
        
        .badge-pending {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            color: #D97706;
        }
        
        .badge-cancelled {
            background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);
            color: #DC2626;
        }
        
        .badge-processing {
            background: linear-gradient(135deg, #E0E7FF 0%, #C7D2FE 100%);
            color: #3730A3;
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Flash Message Animation */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .flash-message {
            animation: slideDown 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Smooth Transitions */
        .transition-smooth {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Scroll Bar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #F3F4F6;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #D1D5DB;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #9CA3AF;
        }
    </style>
</head>
<body>
    <div id="app-container">
        <?php include 'admin-sidebar.php'; ?>

        <div class="content-wrapper">
            
            <header class="bg-white border-b border-gray-100 flex items-center justify-between px-6 z-20 transition-smooth shadow-sm">
            <div class="flex items-center gap-4 md:gap-6">
                <button id="sidebar-toggle" class="md:hidden text-2xl text-dark hover:text-primary transition-smooth">
                    <i class="bi bi-list"></i>
                </button>

                <h2 class="hidden md:block font-heading font-bold text-xl text-gray-800">
                    Admin Portal
                </h2>
            </div>

            <!-- User Profile Section -->
            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Search Bar (Hidden on Mobile) -->
                <div class="hidden lg:flex items-center gap-2 bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 hover:border-primary transition-smooth">
                    <i class="bi bi-search text-gray-400 text-sm"></i>
                    <input type="text" placeholder="Search..." class="bg-transparent outline-none text-sm w-32 text-gray-700 placeholder:text-gray-400" />
                </div>

                <!-- Notification Icon -->
                <button class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-smooth group" title="Notifications">
                    <i class="bi bi-bell text-lg"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                </button>

                <!-- Divider -->
                <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>

                <!-- User Info -->
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-800">
                            <?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin User'; ?>
                        </p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                    <div class="relative group">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-secondary text-white flex items-center justify-center font-bold text-sm shadow-md hover:shadow-lg transition-smooth cursor-pointer border-2 border-primary/20">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        
                        <!-- Profile Dropdown -->
                        <div class="hidden group-hover:block absolute right-0 mt-0 w-48 bg-white border border-gray-200 rounded-lg shadow-xl z-50">
                            <a href="../user/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 first:rounded-t-lg flex items-center gap-2 transition-smooth">
                                <i class="bi bi-person"></i> My Profile
                            </a>
                            <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 transition-smooth">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                            <hr class="my-2">
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 last:rounded-b-lg flex items-center gap-2 transition-smooth font-medium">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto overflow-x-hidden bg-gray-50 p-6 w-full">
            
            <?php 
            if(function_exists('getFlashMessage')):
                $flash = getFlashMessage();
                if($flash): 
                    $bgClass = $flash['type'] == 'success' ? 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-800 border-green-200' : 'bg-gradient-to-r from-red-50 to-rose-50 text-red-800 border-red-200';
                    $icon = $flash['type'] == 'success' ? 'bi-check-circle-fill text-green-600' : 'bi-exclamation-circle-fill text-red-600';
            ?>
            <div class="mb-6 p-4 rounded-lg border <?php echo $bgClass; ?> flex items-center gap-3 shadow-sm flash-message">
                <i class="bi <?php echo $icon; ?> text-lg flex-shrink-0"></i>
                <span class="font-medium text-sm"><?php echo $flash['message']; ?></span>
            </div>
            <?php 
                endif; 
            endif; 
            ?>