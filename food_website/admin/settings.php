<?php
// admin/settings.php
require_once '../includes/config.php';
require_once 'includes/admin-functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => sanitize($_POST['site_name'] ?? ''),
        'site_email' => sanitize($_POST['site_email'] ?? ''),
        'site_phone' => sanitize($_POST['site_phone'] ?? ''),
        'site_address' => sanitize($_POST['site_address'] ?? ''),
        'currency' => sanitize($_POST['currency'] ?? 'NGN'),
        'delivery_fee' => sanitize($_POST['delivery_fee'] ?? '0'),
        'paystack_public_key' => sanitize($_POST['paystack_public_key'] ?? ''),
        'paystack_secret_key' => sanitize($_POST['paystack_secret_key'] ?? ''),
        'flutterwave_public_key' => sanitize($_POST['flutterwave_public_key'] ?? ''),
        'flutterwave_secret_key' => sanitize($_POST['flutterwave_secret_key'] ?? ''),
    ];

    foreach ($settings as $key => $value) {
        $sql = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $key, $value, $value);
        
        if (!$stmt->execute()) {
            $error = 'Error saving settings: ' . $conn->error;
            break;
        }
    }

    if (empty($error)) {
        setFlashMessage('success', 'Settings updated successfully!');
        redirect('settings.php');
    }
}

// Include header AFTER handling POST requests
require_once 'includes/admin-header.php';

// Get current settings
function getSetting($key, $default = '') {
    global $conn;
    $sql = "SELECT setting_value FROM site_settings WHERE setting_key = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['setting_value'] : $default;
}
?>

<style>
    /* Make all input box text black in settings page */
    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="password"],
    input[type="number"],
    select,
    textarea {
        color: #000000 !important;
    }
    
    /* Ensure placeholder text is visible but lighter */
    input::placeholder,
    textarea::placeholder {
        color: #999999 !important;
    }
    
    /* Ensure selected options are visible */
    select option {
        color: #000000 !important;
    }
    
    /* Make currency, phone number, and address inputs white background */
    select[name="currency"],
    input[name="site_phone"],
    textarea[name="site_address"] {
        background-color: #ffffff !important;
        color: #000000 !important;
    }
</style>

<div class="mb-8">
    <h1 class="font-heading text-3xl font-bold text-gray-800">Settings</h1>
    <p class="text-gray-500">Manage your site configuration and payment gateways</p>
</div>

<?php if ($error): ?>
<div class="mb-6 bg-red-50 text-red-800 p-4 rounded-xl border border-red-200">
    <p class="font-bold"><i class="bi bi-exclamation-circle-fill"></i> Error</p>
    <p><?php echo htmlspecialchars($error); ?></p>
</div>
<?php endif; ?>

<form method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <!-- Site Information -->
    <h2 class="font-heading text-xl font-bold text-gray-800 mb-4 pb-4 border-b border-gray-100">Site Information</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <label class="block text-sm font-bold mb-2">Site Name</label>
            <input type="text" name="site_name" value="<?php echo htmlspecialchars(getSetting('site_name', SITE_NAME)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" required>
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">Currency</label>
            <select name="currency" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="NGN" <?php echo getSetting('currency', 'NGN') == 'NGN' ? 'selected' : ''; ?>>Nigerian Naira (₦)</option>
                <option value="USD" <?php echo getSetting('currency') == 'USD' ? 'selected' : ''; ?>>US Dollar ($)</option>
                <option value="GBP" <?php echo getSetting('currency') == 'GBP' ? 'selected' : ''; ?>>British Pound (£)</option>
                <option value="EUR" <?php echo getSetting('currency') == 'EUR' ? 'selected' : ''; ?>>Euro (€)</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">Email Address</label>
            <input type="email" name="site_email" value="<?php echo htmlspecialchars(getSetting('site_email', SITE_EMAIL)); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" required>
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">Phone Number</label>
            <input type="tel" name="site_phone" value="<?php echo htmlspecialchars(getSetting('site_phone')); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none">
        </div>
    </div>

    <div class="mb-8">
        <label class="block text-sm font-bold mb-2">Address</label>
        <textarea name="site_address" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" rows="3"><?php echo htmlspecialchars(getSetting('site_address')); ?></textarea>
    </div>

    <!-- Delivery Settings -->
    <h2 class="font-heading text-xl font-bold text-gray-800 mb-4 pb-4 border-b border-gray-100">Delivery Settings</h2>

    <div class="mb-8">
        <label class="block text-sm font-bold mb-2">Delivery Fee (₦)</label>
        <input type="number" name="delivery_fee" step="0.01" min="0" value="<?php echo htmlspecialchars(getSetting('delivery_fee', '0')); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none">
        <p class="text-xs text-gray-500 mt-1">Amount charged for delivery</p>
    </div>

    <!-- Payment Gateway Settings -->
    <h2 class="font-heading text-xl font-bold text-gray-800 mb-4 pb-4 border-b border-gray-100">Payment Gateway Settings</h2>

    <!-- Paystack -->
    <div class="bg-gray-50 rounded-xl p-6 mb-8">
        <h3 class="font-bold text-gray-800 mb-4"><i class="bi bi-credit-card"></i> Paystack</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold mb-2">Public Key</label>
                <input type="password" name="paystack_public_key" value="<?php echo htmlspecialchars(getSetting('paystack_public_key')); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" placeholder="pk_test_...">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Secret Key</label>
                <input type="password" name="paystack_secret_key" value="<?php echo htmlspecialchars(getSetting('paystack_secret_key')); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" placeholder="sk_test_...">
            </div>
        </div>
    </div>

    <!-- Flutterwave -->
    <div class="bg-gray-50 rounded-xl p-6 mb-8">
        <h3 class="font-bold text-gray-800 mb-4"><i class="bi bi-credit-card"></i> Flutterwave</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold mb-2">Public Key</label>
                <input type="password" name="flutterwave_public_key" value="<?php echo htmlspecialchars(getSetting('flutterwave_public_key')); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" placeholder="FLWPUBK_TEST...">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Secret Key</label>
                <input type="password" name="flutterwave_secret_key" value="<?php echo htmlspecialchars(getSetting('flutterwave_secret_key')); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" placeholder="FLWSECK_TEST...">
            </div>
        </div>
    </div>

    <div class="flex gap-4">
        <button type="submit" class="bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors">
            <i class="bi bi-check-lg"></i> Save Settings
        </button>
        <a href="index.php" class="px-6 py-3 rounded-xl font-bold border border-gray-300 hover:bg-gray-50 transition-colors">
            Cancel
        </a>
    </div>
</form>

<?php require_once 'includes/admin-footer.php'; ?>
