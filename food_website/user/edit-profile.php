<?php
session_start();
require_once '../includes/config.php';

// Access Control: Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login.php?redirect=user/edit-profile.php');
    exit;
}

// Access Control: Block admins from accessing user pages
if (isAdmin()) {
    header('Location: ../admin/index.php');
    exit;
}

$page_title = 'Edit Profile';
include '../includes/header.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');

    // Validate inputs
    if (empty($full_name) || empty($email)) {
        setFlashMessage('error', 'Please fill in all required fields');
    } elseif (!isValidEmail($email)) {
        setFlashMessage('error', 'Please enter a valid email address');
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $phone, $address, $user_id]);
            
            // Update session
            $_SESSION['email'] = $email;
            
            setFlashMessage('success', 'Profile updated successfully!');
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        } catch (Exception $e) {
            setFlashMessage('error', 'Error updating profile: ' . $e->getMessage());
        }
    }
}

// Get active theme for dynamic colors
$activeTheme = getActiveTheme();
$primaryColor = $activeTheme['primary_color'] ?? '#3b82f6';
$secondaryColor = $activeTheme['secondary_color'] ?? '#f97316';
$successColor = '#10b981';
$dangerColor = '#ef4444';
?>

<style>
    :root {
        --primary: <?php echo $primaryColor; ?>;
        --secondary: <?php echo $secondaryColor; ?>;
        --success: <?php echo $successColor; ?>;
        --danger: <?php echo $dangerColor; ?>;
    }
    
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
    .main-content {
        flex: 1;
    }
    
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="tel"]:focus,
    textarea:focus {
        border-color: <?php echo $primaryColor; ?> !important;
        outline: none;
        box-shadow: 0 0 0 3px <?php echo adjustBrightness($primaryColor, 80); ?> !important;
    }
    
    .page-header {
        background: linear-gradient(135deg, <?php echo $primaryColor; ?> 0%, <?php echo adjustBrightness($primaryColor, -20); ?> 100%) !important;
    }
    
    .btn-save {
        background-color: <?php echo $primaryColor; ?> !important;
    }
    
    .btn-save:hover {
        background-color: <?php echo adjustBrightness($primaryColor, -20); ?> !important;
    }
    
    .danger-zone-header {
        color: #dc2626 !important;
    }
    
    .btn-change-pass {
        border-color: <?php echo $secondaryColor; ?> !important;
        color: <?php echo $secondaryColor; ?> !important;
        background-color: <?php echo adjustBrightness($secondaryColor, 85); ?> !important;
    }
    
    .btn-change-pass:hover {
        background-color: <?php echo adjustBrightness($secondaryColor, 75); ?> !important;
    }
</style>

<!-- Page Header -->
<div class="text-white py-12 page-header">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="font-bricolage font-bold text-4xl mb-2">Edit Profile</h1>
        <p class="opacity-90">Update your personal information</p>
    </div>
</div>

<div class="main-content max-w-2xl mx-auto px-4 py-12">
    <!-- Flash Messages -->
    <?php if (hasFlashMessage()): ?>
        <?php $message = getFlashMessage(); ?>
        <div class="mb-6 p-4 rounded-lg border-l-4 text-white"
             style="<?php echo $message['type'] == 'success' ? 
                'background-color: ' . adjustBrightness($successColor, 70) . '; border-color: ' . $successColor . ';' :
                'background-color: ' . adjustBrightness($dangerColor, 70) . '; border-color: ' . $dangerColor . ';'; ?>">
            <div class="flex items-center gap-2">
                <i class="bi <?php echo $message['type'] == 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'; ?>"></i>
                <?php echo $message['message']; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <form method="POST" class="space-y-6">
            <!-- Full Name -->
            <div>
                <label class="block text-sm font-semibold text-dark mb-2">Full Name *</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2"
                       required>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-dark mb-2">Email Address *</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2"
                       required>
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-semibold text-dark mb-2">Phone Number</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2">
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-semibold text-dark mb-2">Delivery Address</label>
                <textarea name="address" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6">
                <button type="submit" class="btn-save flex-1 px-6 py-3 text-white rounded-lg font-semibold transition-colors flex items-center justify-center gap-2">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
                <a href="index.php" class="flex-1 px-6 py-3 border-2 border-gray-300 text-dark rounded-lg font-semibold hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="mt-12 bg-white rounded-2xl border border-red-200 p-8">
        <h3 class="danger-zone-header font-bricolage font-bold text-lg mb-2 flex items-center gap-2">
            <i class="bi bi-exclamation-triangle"></i> Danger Zone
        </h3>
        <p class="text-gray-600 mb-4">These actions cannot be undone.</p>
        <div class="flex gap-3">
            <a href="change-password.php" class="btn-change-pass px-6 py-3 rounded-lg font-semibold transition-colors">
                Change Password
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
