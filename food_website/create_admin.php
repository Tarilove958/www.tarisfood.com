<?php
require_once 'includes/config.php';

// Check if admin already exists
$admin_check = $conn->query("SELECT user_id FROM users WHERE user_type = 'admin'");
$admin_exists = $admin_check->num_rows > 0;

// Handle form submission
$form_error = '';
$form_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$admin_exists) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validation
    if (empty($full_name) || empty($email) || empty($password) || empty($password_confirm)) {
        $form_error = 'All fields are required';
    } elseif (strlen($password) < 6) {
        $form_error = 'Password must be at least 6 characters';
    } elseif ($password !== $password_confirm) {
        $form_error = 'Passwords do not match';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_error = 'Invalid email address';
    } else {
        // Check if email already exists
        $check_email = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $result = $check_email->get_result();
        
        if ($result->num_rows > 0) {
            $form_error = 'Email already registered';
        } else {
            // Create admin account
            $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
            $sql = "INSERT INTO users (full_name, email, password, phone, user_type, status) VALUES (?, ?, ?, '', 'admin', 'active')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $full_name, $email, $password_hash);
            
            if ($stmt->execute()) {
                $form_success = 'Admin account created successfully! Redirecting to login...';
                // Redirect to login after 2 seconds
                header('refresh:2;url=admin/login.php');
            } else {
                $form_error = 'Error creating admin account: ' . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin - <?php echo SITE_NAME; ?></title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-orange-50 min-h-screen flex items-center justify-center p-4">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-blue-200/30 rounded-full mix-blend-multiply filter blur-3xl -z-10"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-orange-200/30 rounded-full mix-blend-multiply filter blur-3xl -z-10"></div>
    
    <div class="w-full max-w-md">
        <?php if ($admin_exists): ?>
            <!-- Admin Already Exists -->
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-8 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-check-circle text-4xl text-green-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2 font-bricolage">Admin Already Exists</h1>
                <p class="text-gray-600 mb-6">An admin account has already been created for this website.</p>
                
                <a href="admin/login.php" class="block w-full py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-xl hover:shadow-lg transition-all mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Go to Login
                </a>
                <a href="index.php" class="block w-full py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all">
                    <i class="bi bi-house me-2"></i> Back to Home
                </a>
            </div>
        <?php else: ?>
            <!-- Create Admin Form -->
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-8 md:p-10">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-shield-lock text-3xl text-white"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 font-bricolage">Create Admin Account</h1>
                    <p class="text-gray-600 text-sm">Set up your administrator account to manage the website</p>
                </div>

                <!-- Success Message -->
                <?php if ($form_success): ?>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6 flex items-start gap-3">
                        <i class="bi bi-check-circle-fill text-green-600 text-xl mt-0.5"></i>
                        <div>
                            <p class="font-bold text-green-700"><?php echo $form_success; ?></p>
                            <p class="text-sm text-green-600">Redirecting to login page...</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Error Message -->
                <?php if ($form_error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6 flex items-start gap-3">
                        <i class="bi bi-exclamation-circle-fill text-red-600 text-xl mt-0.5"></i>
                        <p class="font-bold text-red-700"><?php echo $form_error; ?></p>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form method="POST" class="space-y-5">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-person me-2 text-blue-600"></i>Full Name
                        </label>
                        <input type="text" 
                               name="full_name" 
                               required
                               placeholder="Enter your full name"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all bg-gray-50 focus:bg-white"
                               value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-envelope me-2 text-blue-600"></i>Email Address
                        </label>
                        <input type="email" 
                               name="email" 
                               required
                               placeholder="admin@example.com"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all bg-gray-50 focus:bg-white"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-lock me-2 text-blue-600"></i>Password
                        </label>
                        <input type="password" 
                               name="password" 
                               required
                               placeholder="Enter a strong password (min. 6 characters)"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all bg-gray-50 focus:bg-white">
                        <p class="text-xs text-gray-500 mt-1">Must be at least 6 characters long</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="bi bi-lock-check me-2 text-blue-600"></i>Confirm Password
                        </label>
                        <input type="password" 
                               name="password_confirm" 
                               required
                               placeholder="Confirm your password"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all bg-gray-50 focus:bg-white">
                    </div>

                    <!-- Create Button -->
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-xl hover:shadow-lg hover:-translate-y-1 transition-all duration-300 shadow-md shadow-blue-200 mt-6">
                        <i class="bi bi-shield-plus me-2"></i>Create Admin Account
                    </button>
                </form>

                <!-- Info Box -->
                <div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <p class="text-sm text-blue-800">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Secure:</strong> Your password is encrypted and stored securely. Only you will know your login credentials.
                    </p>
                </div>

                <!-- Back Link -->
                <p class="mt-6 text-center text-sm text-gray-600">
                    <a href="index.php" class="text-blue-600 hover:underline font-semibold">← Back to Home</a>
                </p>
            </div>

            <!-- Features -->
            <div class="mt-8 grid md:grid-cols-3 gap-4">
                <div class="bg-white/80 backdrop-blur rounded-2xl p-4 text-center border border-white/50">
                    <i class="bi bi-shield-check text-3xl text-green-600 mb-2 block"></i>
                    <p class="text-sm font-semibold text-gray-700">Secure Admin Panel</p>
                </div>
                <div class="bg-white/80 backdrop-blur rounded-2xl p-4 text-center border border-white/50">
                    <i class="bi bi-speedometer2 text-3xl text-blue-600 mb-2 block"></i>
                    <p class="text-sm font-semibold text-gray-700">Fast & Reliable</p>
                </div>
                <div class="bg-white/80 backdrop-blur rounded-2xl p-4 text-center border border-white/50">
                    <i class="bi bi-gear text-3xl text-orange-600 mb-2 block"></i>
                    <p class="text-sm font-semibold text-gray-700">Full Control</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
