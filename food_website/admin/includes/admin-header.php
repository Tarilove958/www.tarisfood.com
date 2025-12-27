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
$adminFuncPath = __DIR__ . '/admin-functions.php';

if (file_exists($configPath)) require_once $configPath;
if (file_exists($funcPath)) require_once $funcPath;
if (file_exists($adminFuncPath)) require_once $adminFuncPath;

// 4. SECURITY: Check Admin Access
// If user is not logged in OR is not an admin, kick them out
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    // We assume login.php is in the 'admin' folder (one level up from here)
    header("Location: ../login.php"); 
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
                        dark: '#212529', // Black
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
        body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <?php include 'admin-sidebar.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-6 z-20">
            <button id="sidebar-toggle" class="md:hidden text-2xl text-dark">
                <i class="bi bi-list"></i>
            </button>

            <h2 class="hidden md:block font-heading font-bold text-xl text-gray-700">
                Admin Portal
            </h2>

            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-800">
                        <?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin User'; ?>
                    </p>
                    <p class="text-xs text-green-600 font-bold uppercase">Online</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg shadow-md border-2 border-blue-100">
                    <i class="bi bi-person-fill"></i>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
            
            <?php 
            if(function_exists('getFlashMessage')):
                $flash = getFlashMessage();
                if($flash): 
                    $bgClass = $flash['type'] == 'success' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';
            ?>
            <div class="mb-6 p-4 rounded-xl border <?php echo $bgClass; ?> flex items-center gap-3 shadow-sm animate-bounce-in">
                <i class="bi bi-info-circle-fill text-xl"></i>
                <span class="font-medium"><?php echo $flash['message']; ?></span>
            </div>
            <?php 
                endif; 
            endif; 
            ?>