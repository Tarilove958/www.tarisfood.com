<?php
// admin/login.php - ADMIN ONLY LOGIN
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/theme-manager.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// If already logged in as admin, go to dashboard
if (isLoggedIn() && isAdmin()) {
    redirect('index.php');
}

// If logged in as regular user, redirect to user dashboard
if (isLoggedIn() && !isAdmin()) {
    redirect('../user/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    // Only check for admin users
    $sql = "SELECT * FROM users WHERE email = ? AND user_type = 'admin' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Admin login successful - Use proper session function
            if (function_exists('loginUser')) {
                loginUser($user['user_id'], $user['email'], 'admin');
            } else {
                // Fallback if function doesn't exist
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['last_activity'] = time();
                $_SESSION['created'] = time();
                $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            }
            redirect('index.php');
        } else {
            $error = "Invalid password.";
        }
    } else {
        // No admin found with this email
        $error = "Admin account not found. This portal is for administrators only.";
    }
}

// Get active theme for styling
$activeTheme = getActiveTheme();
$primaryColor = $activeTheme['primary_color'] ?? '#0066CC';
$secondaryColor = $activeTheme['secondary_color'] ?? '#DC3545';
$darkColor = $activeTheme['dark_color'] ?? '#212529';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    
    <!-- Favicon - Multiple formats for best browser compatibility -->
    <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <link rel="apple-touch-icon" href="../assets/images/favicon.svg">
    <meta name="theme-color" content="#FF6B35">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700&family=Outfit:wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 
                        primary: '<?php echo $primaryColor; ?>', 
                        secondary: '<?php echo $secondaryColor; ?>', 
                        dark: '<?php echo $darkColor; ?>' 
                    },
                    fontFamily: { heading: ['"Bricolage Grotesque"', 'sans-serif'], body: ['"Outfit"', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        :root {
            --primary: <?php echo $primaryColor; ?>;
            --secondary: <?php echo $secondaryColor; ?>;
            --dark: <?php echo $darkColor; ?>;
        }
    </style>
</head>
<body class="bg-dark flex items-center justify-center min-h-screen font-body p-4">
    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="font-heading text-3xl font-bold text-primary mb-2">Admin Portal</h1>
            <p class="text-gray-500">Secure Access Only</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-center text-sm font-bold border border-red-100">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Email Address</label>
                <input type="email" name="email" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" required>
            </div>
            <button type="submit" class="w-full bg-dark text-white font-bold py-3 rounded-xl hover:bg-black transition-colors">
                Enter Dashboard
            </button>
        </form>
        <div class="mt-6 text-center">
            <a href="../index.php" class="text-sm text-gray-400 hover:text-primary">← Back to Website</a>
        </div>
    </div>
</body>
</html>