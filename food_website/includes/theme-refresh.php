<?php
/**
 * Theme Refresh Endpoint
 * Called by AJAX to get the latest theme and apply it to the page
 * Used when admin changes the global theme
 */

require_once 'config.php';

header('Content-Type: application/json');

// Get the currently active theme
$activeTheme = getActiveTheme();

if ($activeTheme) {
    // Return theme data as JSON
    echo json_encode([
        'success' => true,
        'theme' => $activeTheme,
        'css_colors' => [
            'primary' => htmlspecialchars($activeTheme['primary_color']),
            'secondary' => htmlspecialchars($activeTheme['secondary_color']),
            'accent' => htmlspecialchars($activeTheme['accent_color'] ?? '#8b5cf6'),
            'dark' => htmlspecialchars($activeTheme['dark_color']),
            'light' => htmlspecialchars($activeTheme['light_color']),
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Could not fetch theme'
    ]);
}
