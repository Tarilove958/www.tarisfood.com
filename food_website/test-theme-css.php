<?php
// Test if theme CSS is loading and applying
session_start();
require_once 'includes/config.php';
require_once 'includes/theme-manager.php';

$active_theme = getActiveTheme();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme CSS Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .box {
            padding: 20px;
            margin: 20px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        .primary {
            background-color: var(--primary);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .secondary {
            background-color: var(--secondary);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .accent {
            background-color: var(--accent);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 10px 0;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
    
    <!-- Load main CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    
    <!-- Load active theme CSS -->
    <?php 
        if ($active_theme && isset($active_theme['theme_slug'])) {
            $theme_css_path = ASSETS_URL . '/themes/' . htmlspecialchars($active_theme['theme_slug']) . '.css?v=' . time();
        } else {
            $theme_css_path = ASSETS_URL . '/themes/default.css?v=' . time();
        }
    ?>
    <link rel="stylesheet" href="<?php echo $theme_css_path; ?>" id="theme-stylesheet">
    
    <!-- Set CSS variables inline -->
    <style>
        :root {
            <?php 
            $active_theme = getActiveTheme();
            if ($active_theme) {
                echo "--primary: " . htmlspecialchars($active_theme['primary_color']) . " !important;\n";
                echo "--secondary: " . htmlspecialchars($active_theme['secondary_color']) . " !important;\n";
                echo "--accent: " . htmlspecialchars($active_theme['accent_color']) . " !important;\n";
                echo "--dark: " . htmlspecialchars($active_theme['dark_color']) . " !important;\n";
                echo "--light: " . htmlspecialchars($active_theme['light_color']) . " !important;\n";
            }
            ?>
        }
    </style>
</head>
<body>
    <h1>Theme CSS Test</h1>
    
    <div class="box">
        <h2>Active Theme Information</h2>
        <?php if ($active_theme): ?>
            <p><strong>Theme Name:</strong> <?php echo htmlspecialchars($active_theme['name']); ?></p>
            <p><strong>Theme Slug:</strong> <code><?php echo htmlspecialchars($active_theme['theme_slug']); ?></code></p>
            <p><strong>Primary Color:</strong> <code><?php echo htmlspecialchars($active_theme['primary_color']); ?></code></p>
            <p><strong>Secondary Color:</strong> <code><?php echo htmlspecialchars($active_theme['secondary_color']); ?></code></p>
            <p><strong>Accent Color:</strong> <code><?php echo htmlspecialchars($active_theme['accent_color']); ?></code></p>
            <p><strong>Dark Color:</strong> <code><?php echo htmlspecialchars($active_theme['dark_color']); ?></code></p>
            <p><strong>Light Color:</strong> <code><?php echo htmlspecialchars($active_theme['light_color']); ?></code></p>
            <p><strong>Font Family:</strong> <code><?php echo htmlspecialchars($active_theme['font_family']); ?></code></p>
            <p><strong>CSS File Path:</strong> <code><?php echo htmlspecialchars($theme_css_path); ?></code></p>
        <?php else: ?>
            <p style="color: red;"><strong>ERROR:</strong> No active theme found!</p>
        <?php endif; ?>
    </div>
    
    <div class="box">
        <h2>CSS Variable Test</h2>
        <p>The boxes below should show your theme's colors using CSS variables:</p>
        
        <div class="primary">
            <strong>Primary Color (--primary)</strong><br>
            This box uses: background-color: var(--primary)
        </div>
        
        <div class="secondary">
            <strong>Secondary Color (--secondary)</strong><br>
            This box uses: background-color: var(--secondary)
        </div>
        
        <div class="accent">
            <strong>Accent Color (--accent)</strong><br>
            This box uses: background-color: var(--accent)
        </div>
    </div>
    
    <div class="box">
        <h2>Theme CSS File Check</h2>
        <?php 
            if ($active_theme && isset($active_theme['theme_slug'])) {
                $css_file = 'assets/themes/' . $active_theme['theme_slug'] . '.css';
                if (file_exists($css_file)) {
                    $file_size = filesize($css_file);
                    echo "<p style='color: green;'><strong>✓ CSS File Found:</strong> <code>$css_file</code> ($file_size bytes)</p>";
                    
                    // Show first few lines of the file
                    $first_lines = array_slice(file($css_file), 0, 20);
                    echo "<p><strong>First 20 lines of CSS file:</strong></p>";
                    echo "<pre>" . htmlspecialchars(implode('', $first_lines)) . "</pre>";
                } else {
                    echo "<p style='color: red;'><strong>✗ CSS File NOT Found:</strong> <code>$css_file</code></p>";
                }
            }
        ?>
    </div>
    
    <div class="box">
        <h2>Debugging Tips</h2>
        <ul>
            <li>Open your browser's Developer Tools (F12)</li>
            <li>Go to "Inspector" or "Elements" tab</li>
            <li>Right-click on the colored boxes and select "Inspect Element"</li>
            <li>Look for "Computed Styles" and check what <code>background-color</code> is set to</li>
            <li>Check the "Styles" panel to see where colors are coming from</li>
            <li>Go to "Network" tab and check if the theme CSS file is loading (should see 200 status)</li>
        </ul>
    </div>
    
    <hr>
    
    <p><a href="index.php">← Back to Home</a> | <a href="admin/manage-themes.php">Manage Themes →</a></p>
</body>
</html>
