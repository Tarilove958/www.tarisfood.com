<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    
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
                        primary: '#0066CC', // Primary Blue
                        secondary: '#DC3545', // Primary Red
                        dark: '#212529', // Accent Black
                        light: '#F8F9FA', // Light BG
                    },
                    fontFamily: {
                        bricolage: ['"Bricolage Grotesque"', 'sans-serif'],
                        outfit: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #F8F9FA; }
        .font-bricolage { font-family: 'Bricolage Grotesque', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #0066CC 0%, #DC3545 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-blob {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 102, 204, 0.1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }
        .hover-scale:hover { transform: scale(1.05); transition: transform 0.2s; }
    </style>
</head>
<body class="text-gray-800 antialiased flex flex-col min-h-screen">

    <nav class="fixed top-0 left-0 right-0 z-50">
        <div class="nav-blob px-4 py-3">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <a href="index.php" class="flex items-center gap-2">
                    <img src="assets/images/logo.png" alt="<?php echo SITE_NAME; ?>" class="h-10 w-auto object-contain">
                </a>

                <div class="hidden md:flex items-center gap-8 font-medium text-sm text-gray-600">
                    <a href="index.php" class="hover:text-primary transition-colors">Home</a>
                    <a href="menu.php" class="hover:text-primary transition-colors">Menu</a>
                    <a href="about.php" class="hover:text-primary transition-colors">About</a>
                    <a href="testimonial.php" class="hover:text-primary transition-colors">Testimonials</a>
                    <a href="contact.php" class="hover:text-primary transition-colors">Contact</a>
                </div>

                <div class="flex items-center gap-4">
                    <a href="cart.php" class="relative p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <i class="bi bi-cart3 text-xl text-primary"></i>
                        <span class="absolute top-0 right-0 w-4 h-4 bg-secondary text-white text-[10px] font-bold flex items-center justify-center rounded-full">
                            <?php echo getCartCount(); ?>
                        </span>
                    </a>
                    
                    <?php if(isLoggedIn()): ?>
                        <div class="hidden sm:flex items-center gap-3">
                            <a href="user/dashboard.php" class="text-sm font-semibold hover:text-primary">Dashboard</a>
                            <a href="logout.php" class="text-sm font-semibold text-secondary hover:text-red-700">Logout</a>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="hidden sm:flex items-center gap-2 px-5 py-2 rounded-full bg-primary text-white text-sm font-semibold hover:bg-blue-700 transition-all hover-scale shadow-lg shadow-blue-200">
                            <span>Login</span>
                        </a>
                    <?php endif; ?>

                    <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-600">
                        <i class="bi bi-list text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-gray-100 absolute w-full top-[64px]">
            <div class="p-4 flex flex-col gap-4 text-center">
                <a href="index.php" class="text-gray-700 hover:text-primary">Home</a>
                <a href="menu.php" class="text-gray-700 hover:text-primary">Menu</a>
                <a href="about.php" class="text-gray-700 hover:text-primary">About</a>
                <a href="contact.php" class="text-gray-700 hover:text-primary">Contact</a>
                <?php if(!isLoggedIn()): ?>
                    <a href="login.php" class="bg-primary text-white py-2 rounded-lg">Login</a>
                <?php else: ?>
                    <a href="user/dashboard.php" class="text-primary">My Dashboard</a>
                    <a href="logout.php" class="text-secondary">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="h-20"></div>
    
    <?php 
    $flash = getFlashMessage();
    if($flash): 
        $bgClass = $flash['type'] == 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    ?>
    <div class="max-w-6xl mx-auto px-4 mt-4">
        <div class="p-4 rounded-lg <?php echo $bgClass; ?> flex items-center gap-2">
            <i class="bi bi-info-circle-fill"></i>
            <?php echo $flash['message']; ?>
        </div>
    </div>
    <?php endif; ?>