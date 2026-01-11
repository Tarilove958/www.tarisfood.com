<?php
require 'includes/config.php';

echo "=== Current Users in Database ===\n";
echo str_repeat("=", 60) . "\n\n";

$stmt = $pdo->query("SELECT user_id, email, user_type FROM users ORDER BY user_id DESC LIMIT 20");
$users = $stmt->fetchAll();

if (empty($users)) {
    echo "No users found in database.\n";
} else {
    foreach ($users as $u) {
        $type = $u['user_type'] ?: 'NULL/EMPTY';
        echo sprintf("ID: %3d | Email: %-30s | Type: %s\n", 
            $u['user_id'], 
            $u['email'], 
            $type
        );
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Note: Admin users should have Type = 'admin'\n";
echo "Regular users should have Type = 'user'\n";
?>
