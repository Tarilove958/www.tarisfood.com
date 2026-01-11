<?php
/**
 * Fix User Types in Database
 * Ensures all users have either 'admin' or 'customer' user_type
 */
require 'includes/config.php';

echo "=== User Type Migration ===\n\n";

// Step 1: Check how many users have NULL user_type
$checkNull = $pdo->query("SELECT COUNT(*) as count FROM users WHERE user_type IS NULL OR user_type = ''");
$nullCount = $checkNull->fetch()['count'];

if ($nullCount > 0) {
    echo "Found $nullCount users with NULL/empty user_type\n";
    echo "Setting them to 'customer'...\n";
    
    $updateNull = $pdo->prepare("UPDATE users SET user_type = 'customer' WHERE user_type IS NULL OR user_type = ''");
    if ($updateNull->execute()) {
        echo "✓ Updated $nullCount users\n\n";
    }
}

// Step 2: Show all users and their types
echo "Current Users in Database:\n";
echo str_repeat("-", 80) . "\n";
echo sprintf("%-5s | %-35s | %-15s\n", "ID", "Email", "Type");
echo str_repeat("-", 80) . "\n";

$users = $pdo->query("SELECT user_id, email, user_type FROM users ORDER BY user_id ASC");
foreach ($users as $u) {
    $type = $u['user_type'] ?: 'NULL';
    printf("%-5d | %-35s | %-15s\n", $u['user_id'], $u['email'], $type);
}

echo str_repeat("-", 80) . "\n";
echo "\nMigration complete!\n";
echo "Admin accounts: Type = 'admin'\n";
echo "Customer accounts: Type = 'customer'\n";
?>
