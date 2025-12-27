<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login.php?redirect=user/change-password.php');
    exit;
}

$page_title = 'Change Password';
include '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        setFlashMessage('error', 'Please fill in all password fields');
    } elseif (strlen($new_password) < 6) {
        setFlashMessage('error', 'Password must be at least 6 characters long');
    } elseif ($new_password !== $confirm_password) {
        setFlashMessage('error', 'New passwords do not match');
    } else {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!password_verify($current_password, $user['password'])) {
            setFlashMessage('error', 'Current password is incorrect');
        } else {
            try {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $stmt->execute([$hashed_password, $user_id]);

                setFlashMessage('success', 'Password changed successfully!');
            } catch (Exception $e) {
                setFlashMessage('error', 'Error changing password: ' . $e->getMessage());
            }
        }
    }
}
?>

<!-- Page Header -->
<div class="bg-gradient-to-r from-primary to-blue-600 text-white py-12">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="font-bricolage font-bold text-4xl mb-2">Change Password</h1>
        <p class="text-blue-100">Keep your account secure</p>
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
            <!-- Current Password -->
            <div>
                <label class="block text-sm font-semibold text-dark mb-2">Current Password *</label>
                <input type="password" name="current_password"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100"
                       required>
                <p class="text-xs text-gray-500 mt-1">Enter your current password to verify</p>
            </div>

            <!-- New Password -->
            <div>
                <label class="block text-sm font-semibold text-dark mb-2">New Password *</label>
                <input type="password" name="new_password" id="new_password"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100"
                       required>
                <p class="text-xs text-gray-500 mt-1">Must be at least 6 characters</p>
                <div class="mt-2 flex gap-1">
                    <div id="strength-meter" class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full w-0 bg-red-500 transition-all" id="strength-bar"></div>
                    </div>
                    <span class="text-xs font-semibold text-gray-600" id="strength-text">Weak</span>
                </div>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-semibold text-dark mb-2">Confirm Password *</label>
                <input type="password" name="confirm_password" id="confirm_password"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100"
                       required>
                <p class="text-xs text-gray-500 mt-1" id="match-message"></p>
            </div>

            <!-- Password Requirements -->
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <h4 class="font-semibold text-dark text-sm mb-2 flex items-center gap-2">
                    <i class="bi bi-info-circle text-primary"></i> Password Requirements
                </h4>
                <ul class="text-xs text-gray-700 space-y-1">
                    <li class="flex items-center gap-2">
                        <span id="req-length" class="text-gray-400"><i class="bi bi-circle-fill text-[4px]"></i></span>
                        At least 6 characters
                    </li>
                    <li class="flex items-center gap-2">
                        <span id="req-match" class="text-gray-400"><i class="bi bi-circle-fill text-[4px]"></i></span>
                        Passwords must match
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6">
                <button type="submit" class="flex-1 px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
                        id="submit-btn" disabled>
                    <i class="bi bi-shield-check"></i> Change Password
                </button>
                <a href="index.php" class="flex-1 px-6 py-3 border-2 border-gray-300 text-dark rounded-lg font-semibold hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPass = document.getElementById('new_password');
    const confirmPass = document.getElementById('confirm_password');
    const submitBtn = document.getElementById('submit-btn');
    const matchMessage = document.getElementById('match-message');
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');

    // Check password strength
    function checkStrength() {
        const password = newPass.value;
        let strength = 0;

        if (password.length >= 6) strength++;
        if (password.length >= 10) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[!@#$%^&*]/.test(password)) strength++;

        const strengthLevels = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
        const strengthColors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500', 'bg-green-600'];

        strengthBar.className = 'h-full transition-all ' + strengthColors[strength - 1];
        strengthBar.style.width = (strength * 20) + '%';
        strengthText.textContent = strengthLevels[strength - 1];

        document.getElementById('req-length').className = password.length >= 6 ? 'text-green-500' : 'text-gray-400';
        updateValidation();
    }

    // Check password match
    function updateValidation() {
        const matches = newPass.value === confirmPass.value && newPass.value.length >= 6;
        
        if (confirmPass.value) {
            if (matches) {
                matchMessage.textContent = '✓ Passwords match';
                matchMessage.className = 'text-xs text-green-600 mt-1';
            } else if (newPass.value !== confirmPass.value) {
                matchMessage.textContent = '✗ Passwords do not match';
                matchMessage.className = 'text-xs text-red-600 mt-1';
            } else {
                matchMessage.textContent = '';
            }
        }

        document.getElementById('req-match').className = matches ? 'text-green-500' : 'text-gray-400';
        submitBtn.disabled = !matches;
    }

    newPass.addEventListener('input', checkStrength);
    confirmPass.addEventListener('input', updateValidation);
});
</script>

<?php include '../includes/footer.php'; ?>
