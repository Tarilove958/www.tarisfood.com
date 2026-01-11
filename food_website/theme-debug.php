<?php
/**
 * Theme Debug Script
 * Check if themes are properly set up and loading
 */

require_once 'includes/config.php';
require_once 'includes/theme-manager.php';

echo "<h1>Theme System Debug</h1>";
echo "<hr>";

// 1. Check database connection
echo "<h2>1. Database Connection</h2>";
if ($conn && !$conn->connect_error) {
    echo "<p style='color: green;'>✓ Connected to database</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    exit;
}

// 2. Check themes table
echo "<h2>2. Themes Table</h2>";
$result = $conn->query("SELECT COUNT(*) as count FROM themes");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<p style='color: green;'>✓ Themes table exists - " . $row['count'] . " themes found</p>";
} else {
    echo "<p style='color: red;'>✗ Themes table not found</p>";
}

// 3. Get active theme
echo "<h2>3. Active Theme</h2>";
$active_theme = getActiveTheme();
if ($active_theme) {
    echo "<p style='color: green;'>✓ Active theme: <strong>" . htmlspecialchars($active_theme['theme_name']) . "</strong></p>";
    echo "<p>Theme slug: " . htmlspecialchars($active_theme['theme_slug']) . "</p>";
    echo "<p>Primary color: <span style='display: inline-block; width: 30px; height: 30px; background-color: " . htmlspecialchars($active_theme['primary_color']) . "; border: 1px solid #ddd;'></span> " . htmlspecialchars($active_theme['primary_color']) . "</p>";
    echo "<p>Secondary color: <span style='display: inline-block; width: 30px; height: 30px; background-color: " . htmlspecialchars($active_theme['secondary_color']) . "; border: 1px solid #ddd;'></span> " . htmlspecialchars($active_theme['secondary_color']) . "</p>";
    echo "<p>Font family: " . htmlspecialchars($active_theme['font_family']) . "</p>";
} else {
    echo "<p style='color: red;'>✗ No active theme found</p>";
}

// 4. Check CSS file
echo "<h2>4. Theme CSS File</h2>";
if ($active_theme) {
    $css_file = 'assets/themes/' . htmlspecialchars($active_theme['theme_slug']) . '.css';
    if (file_exists($css_file)) {
        echo "<p style='color: green;'>✓ CSS file exists: <code>" . htmlspecialchars($css_file) . "</code></p>";
        $filesize = filesize($css_file);
        echo "<p>File size: " . $filesize . " bytes</p>";
    } else {
        echo "<p style='color: red;'>✗ CSS file not found: <code>" . htmlspecialchars($css_file) . "</code></p>";
    }
}

// 5. List all themes
echo "<h2>5. All Available Themes</h2>";
$all_themes = getAllThemes();
if (!empty($all_themes)) {
    echo "<ul>";
    foreach ($all_themes as $theme) {
        $status = $theme['is_active'] ? ' (ACTIVE)' : '';
        $default = $theme['is_default'] ? ' [DEFAULT]' : '';
        echo "<li>" . htmlspecialchars($theme['theme_name']) . " - " . htmlspecialchars($theme['theme_slug']) . $status . $default . "</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='index.php'>Back to home</a> | <a href='admin/manage-themes.php'>Go to theme manager</a></p>";
?>
