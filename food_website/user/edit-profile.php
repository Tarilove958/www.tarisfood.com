<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login.php?redirect=user/edit-profile.php');
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
?>

<!-- Page Header -->
<div class="bg-gradient-to-r from-primary to-blue-600 text-white py-12">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="font-bricolage font-bold text-4xl mb-2">Edit Profile</h1>
        <p class="text-blue-100">Update your personal information</p>
    </div>
</div>

<div class="max-w-2xl mx-auto px-4 py-12">
    <!-- Flash Messages -->
    <?php if (hasFlashMessage()): ?>
        <?php $message = getFlashMessage(); ?>
        <div class="mb-6 p-4 rounded-lg border-l-4 
            <?php echo $message['type'] == 'success' ? 'bg-green-50 border-green-500 text-green-700' : 'bg-red-50 border-red-500 text-red-700'; ?>">
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
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100"
                       required>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-dark mb-2">Email Address *</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100"
                       required>
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-semibold text-dark mb-2">Phone Number</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100">
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-semibold text-dark mb-2">Delivery Address</label>
                <textarea name="address" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6">
                <button type="submit" class="flex-1 px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
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
        <h3 class="font-bricolage font-bold text-lg text-red-600 mb-2 flex items-center gap-2">
            <i class="bi bi-exclamation-triangle"></i> Danger Zone
        </h3>
        <p class="text-gray-600 mb-4">These actions cannot be undone.</p>
        <div class="flex gap-3">
            <a href="change-password.php" class="px-6 py-3 border-2 border-orange-500 text-orange-600 rounded-lg font-semibold hover:bg-orange-50 transition-colors">
                Change Password
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
