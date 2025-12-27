<?php
/**
 * Admin Panel Verification Script
 * Run this to verify all components are properly installed
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Food Website Admin Panel Verification ===\n\n";

// 1. Check PHP Version
echo "1. PHP Version: " . PHP_VERSION . " ✓\n";

// 2. Check Required Extensions
$required_extensions = ['mysqli', 'pdo', 'gd', 'json'];
echo "\n2. Required Extensions:\n";
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? "✓" : "✗";
    echo "   - $ext: $status\n";
}

// 3. Check File Structure
echo "\n3. Admin Files Status:\n";
$admin_files = [
    'admin/index.php',
    'admin/login.php',
    'admin/logout.php',
    'admin/manage-products.php',
    'admin/add-product.php',
    'admin/edit-product.php',
    'admin/delete-product.php',
    'admin/manage-orders.php',
    'admin/view-order.php',
    'admin/manage-users.php',
    'admin/manage-categories.php',
    'admin/edit-category.php',
    'admin/manage-testimonials.php',
    'admin/settings.php',
    'admin/includes/admin-header.php',
    'admin/includes/admin-footer.php',
    'admin/includes/admin-sidebar.php',
    'admin/includes/admin-functions.php',
];

$root_dir = __DIR__;
$missing_files = [];

foreach ($admin_files as $file) {
    $full_path = $root_dir . '/' . $file;
    if (file_exists($full_path)) {
        echo "   ✓ " . basename($file) . "\n";
    } else {
        echo "   ✗ " . basename($file) . " (MISSING)\n";
        $missing_files[] = $file;
    }
}

// 4. Check Directories
echo "\n4. Upload Directories Status:\n";
$required_dirs = [
    'assets/images/products',
    'uploads/testimonials',
    'uploads/products',
];

foreach ($required_dirs as $dir) {
    $full_path = $root_dir . '/' . $dir;
    $exists = is_dir($full_path);
    $writable = is_writable($full_path);
    
    if ($exists && $writable) {
        echo "   ✓ " . $dir . " (readable & writable)\n";
    } elseif ($exists) {
        echo "   ⚠ " . $dir . " (exists but not writable)\n";
    } else {
        echo "   ✗ " . $dir . " (MISSING)\n";
    }
}

// 5. Check Configuration
echo "\n5. Configuration Check:\n";
if (file_exists($root_dir . '/includes/config.php')) {
    require_once $root_dir . '/includes/config.php';
    
    if (defined('DB_HOST') && defined('DB_NAME')) {
        echo "   ✓ Database constants defined\n";
        
        // Try connection
        try {
            $test_conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (!$test_conn->connect_error) {
                echo "   ✓ Database connection successful\n";
                
                // Check tables
                $tables = ['users', 'products', 'categories', 'orders', 'order_items', 'testimonials', 'site_settings'];
                echo "\n6. Database Tables Status:\n";
                foreach ($tables as $table) {
                    $result = $test_conn->query("SHOW TABLES LIKE '$table'");
                    if ($result && $result->num_rows > 0) {
                        echo "   ✓ " . $table . "\n";
                    } else {
                        echo "   ✗ " . $table . " (MISSING)\n";
                    }
                }
                
                $test_conn->close();
            } else {
                echo "   ✗ Database connection failed: " . $test_conn->connect_error . "\n";
            }
        } catch (Exception $e) {
            echo "   ✗ Database connection error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ✗ Database constants not defined\n";
    }
} else {
    echo "   ✗ config.php not found\n";
}

// 7. Summary
echo "\n=== Summary ===\n";
if (empty($missing_files)) {
    echo "✓ All admin files are in place!\n";
} else {
    echo "✗ Missing files:\n";
    foreach ($missing_files as $file) {
        echo "  - " . $file . "\n";
    }
}

echo "\n=== Next Steps ===\n";
echo "1. Ensure database is created and populated with database.sql\n";
echo "2. Update database credentials in includes/config.php\n";
echo "3. Visit: http://localhost/food_website/admin/login.php\n";
echo "4. Login with: admin@foodwebsite.com / admin123\n";
echo "5. Change the default password immediately\n";
echo "\n=== Verification Complete ===\n";
?>
