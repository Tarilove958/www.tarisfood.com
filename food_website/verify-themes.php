<?php
/**
 * Theme System Verification Script
 * Run this to verify theme system is properly installed
 */

require_once 'includes/config.php';

echo "=================================\n";
echo "THEME SYSTEM VERIFICATION\n";
echo "=================================\n\n";

$all_good = true;

// 1. Check Database Connection
echo "[1] Checking Database Connection... ";
if ($conn && $conn->connect_error === null) {
    echo "✓ OK\n";
} else {
    echo "✗ FAILED\n";
    $all_good = false;
}

// 2. Check if Themes Table Exists
echo "[2] Checking Themes Table... ";
$result = $conn->query("SHOW TABLES LIKE 'themes'");
if ($result && $result->num_rows > 0) {
    echo "✓ EXISTS\n";
} else {
    echo "✗ MISSING - Run: mysql -u root food_website < themes_migration.sql\n";
    $all_good = false;
}

// 3. Check Theme Count
echo "[3] Checking Installed Themes... ";
$result = $conn->query("SELECT COUNT(*) as count FROM themes");
if ($result) {
    $row = $result->fetch_assoc();
    $count = $row['count'];
    if ($count >= 5) {
        echo "✓ OK ($count themes found)\n";
    } else {
        echo "✗ INCOMPLETE ($count/5 themes)\n";
        $all_good = false;
    }
} else {
    echo "✗ ERROR\n";
    $all_good = false;
}

// 4. Check Active Theme
echo "[4] Checking Active Theme... ";
$result = $conn->query("SELECT theme_name FROM themes WHERE is_active = TRUE");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ OK (Active: {$row['theme_name']})\n";
} else {
    echo "✗ NO ACTIVE THEME\n";
    $all_good = false;
}

// 5. Check Theme CSS Files
echo "[5] Checking Theme CSS Files...\n";
$themes_dir = 'assets/themes/';
$result = $conn->query("SELECT theme_slug FROM themes");
if ($result && $result->num_rows > 0) {
    $missing = [];
    while ($row = $result->fetch_assoc()) {
        $css_file = $themes_dir . $row['theme_slug'] . '.css';
        if (file_exists($css_file)) {
            echo "   ✓ {$row['theme_slug']}.css\n";
        } else {
            echo "   ✗ {$row['theme_slug']}.css (MISSING)\n";
            $missing[] = $row['theme_slug'];
            $all_good = false;
        }
    }
}

// 6. Check Theme Manager Function
echo "[6] Checking Theme Manager Functions... ";
if (function_exists('getActiveTheme')) {
    echo "✓ OK\n";
} else {
    echo "✗ FAILED\n";
    $all_good = false;
}

// 7. Check Admin Theme Page
echo "[7] Checking Admin Theme Management... ";
if (file_exists('admin/manage-themes.php')) {
    echo "✓ EXISTS\n";
} else {
    echo "✗ MISSING\n";
    $all_good = false;
}

// 8. Check Theme Sidebar Link
echo "[8] Checking Admin Sidebar Theme Link... ";
$sidebar_content = file_get_contents('admin/includes/admin-sidebar.php');
if (strpos($sidebar_content, 'manage-themes.php') !== false) {
    echo "✓ OK\n";
} else {
    echo "✗ MISSING\n";
    $all_good = false;
}

// 9. Check Header Theme Loading
echo "[9] Checking Header Theme Integration... ";
$header_content = file_get_contents('includes/header.php');
if (strpos($header_content, 'getThemeCSSPath') !== false && strpos($header_content, 'getActiveTheme') !== false) {
    echo "✓ OK\n";
} else {
    echo "✗ INCOMPLETE\n";
    $all_good = false;
}

// 10. List All Themes
echo "\n[10] Available Themes:\n";
$result = $conn->query("SELECT theme_id, theme_name, theme_slug, is_active, is_default, primary_color, secondary_color FROM themes ORDER BY is_active DESC, is_default DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $status = [];
        if ($row['is_active']) $status[] = "ACTIVE";
        if ($row['is_default']) $status[] = "DEFAULT";
        $status_str = !empty($status) ? '[' . implode(', ', $status) . ']' : '';
        
        printf("   • %-20s %-15s %s %s\n", 
            $row['theme_name'], 
            '(' . $row['theme_slug'] . ')',
            $row['primary_color'] . ' / ' . $row['secondary_color'],
            $status_str
        );
    }
}

echo "\n=================================\n";
if ($all_good) {
    echo "✓ ALL CHECKS PASSED!\n";
    echo "Theme system is ready to use.\n";
} else {
    echo "✗ SOME CHECKS FAILED\n";
    echo "Please see errors above.\n";
}
echo "=================================\n";
?>
