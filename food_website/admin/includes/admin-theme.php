<?php
/**
 * Admin Theme CSS Generator
 * Dynamically generates CSS with active theme colors for admin pages
 */

// Make sure config is loaded
if (!function_exists('getActiveTheme')) {
    require_once dirname(dirname(__DIR__)) . '/includes/config.php';
}

// Get active theme
$activeTheme = getActiveTheme();
$primaryColor = $activeTheme['primary_color'] ?? '#3b82f6';
$secondaryColor = $activeTheme['secondary_color'] ?? '#f97316';
$darkColor = $activeTheme['dark_color'] ?? '#1f2937';
$lightColor = $activeTheme['light_color'] ?? '#f9fafb';
$accentColor = $activeTheme['accent_color'] ?? '#8b5cf6';

// Set content type and prevent caching
header('Content-Type: text/css');
header('Cache-Control: no-cache, no-store, must-revalidate');
?>

:root {
    --primary: <?php echo $primaryColor; ?>;
    --secondary: <?php echo $secondaryColor; ?>;
    --dark: <?php echo $darkColor; ?>;
    --light: <?php echo $lightColor; ?>;
    --accent: <?php echo $accentColor; ?>;
}

/* Apply primary color to all primary elements */
.bg-primary, [class*="bg-primary"] {
    background-color: <?php echo $primaryColor; ?> !important;
}

.text-primary, [class*="text-primary"] {
    color: <?php echo $primaryColor; ?> !important;
}

.border-primary, [class*="border-primary"] {
    border-color: <?php echo $primaryColor; ?> !important;
}

/* Hover effects */
.hover\:bg-primary:hover, [class*="hover:bg-primary"]:hover {
    background-color: <?php echo adjustBrightness($primaryColor, -10); ?> !important;
}

/* Focus effects */
.focus\:border-primary:focus, [class*="focus:border-primary"]:focus {
    border-color: <?php echo $primaryColor; ?> !important;
    outline-color: <?php echo $primaryColor; ?> !important;
}

/* Secondary colors */
.bg-secondary {
    background-color: <?php echo $secondaryColor; ?> !important;
}

.text-secondary {
    color: <?php echo $secondaryColor; ?> !important;
}

/* Dark colors */
.bg-dark {
    background-color: <?php echo $darkColor; ?> !important;
}

.text-dark {
    color: <?php echo $darkColor; ?> !important;
}

/* Light colors */
.bg-light {
    background-color: <?php echo $lightColor; ?> !important;
}

.text-light {
    color: <?php echo $lightColor; ?> !important;
}

/* Accent colors */
.bg-accent {
    background-color: <?php echo $accentColor; ?> !important;
}

.text-accent {
    color: <?php echo $accentColor; ?> !important;
}

/* Buttons */
button[class*="primary"],
input[type="button"][class*="primary"],
input[type="submit"][class*="primary"],
a[class*="primary"][class*="btn"] {
    background-color: <?php echo $primaryColor; ?> !important;
    color: white !important;
}

button[class*="primary"]:hover,
input[type="button"][class*="primary"]:hover,
input[type="submit"][class*="primary"]:hover,
a[class*="primary"][class*="btn"]:hover {
    background-color: <?php echo adjustBrightness($primaryColor, -15); ?> !important;
}

/* Links */
a[class*="text-primary"], 
a:not([class]) {
    color: <?php echo $primaryColor; ?>;
}

a[class*="text-primary"]:hover {
    color: <?php echo adjustBrightness($primaryColor, -15); ?>;
}

/* Sidebar active states */
.sidebar-active,
[class*="sidebar"] [class*="active"] {
    background-color: <?php echo $primaryColor; ?> !important;
    color: white !important;
}

/* Form elements */
input:focus,
textarea:focus,
select:focus {
    border-color: <?php echo $primaryColor; ?> !important;
    outline-color: <?php echo $primaryColor; ?> !important;
    box-shadow: 0 0 0 3px <?php echo $primaryColor; ?>20 !important;
}

/* Progress bars */
[class*="progress"] {
    background-color: <?php echo adjustBrightness($primaryColor, 60); ?> !important;
}

[class*="progress"] [class*="bar"] {
    background-color: <?php echo $primaryColor; ?> !important;
}

/* Badges */
[class*="badge"] {
    background-color: <?php echo adjustBrightness($primaryColor, 50); ?> !important;
    color: <?php echo $primaryColor; ?> !important;
}

/* Tables */
table [class*="active"] {
    background-color: <?php echo adjustBrightness($primaryColor, 80); ?> !important;
}

table a {
    color: <?php echo $primaryColor; ?>;
}

/* Icons with primary color */
[class*="icon"][class*="primary"],
i[class*="primary"] {
    color: <?php echo $primaryColor; ?> !important;
}

/* Shadows with primary color influence */
[class*="shadow"][class*="primary"] {
    box-shadow: 0 10px 30px <?php echo $primaryColor; ?>30 !important;
}

/* Gradients */
[class*="gradient"] {
    background: linear-gradient(135deg, <?php echo $primaryColor; ?>, <?php echo adjustBrightness($primaryColor, -20); ?>) !important;
}
