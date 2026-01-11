<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/theme-manager.php';

$active_theme = getActiveTheme();
$all_themes = getAllThemes();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Theme System Test</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    
    <!-- Load theme CSS -->
    <?php 
        if ($active_theme && isset($active_theme['theme_slug'])) {
            $theme_css_path = ASSETS_URL . '/themes/' . htmlspecialchars($active_theme['theme_slug']) . '.css?v=' . time();
        } else {
            $theme_css_path = ASSETS_URL . '/themes/default.css?v=' . time();
        }
    ?>
    <link rel="stylesheet" href="<?php echo $theme_css_path; ?>" id="theme-stylesheet">
    
    <!-- Set CSS variables -->
    <style>
        :root {
            <?php 
            if ($active_theme) {
                echo "--primary: " . htmlspecialchars($active_theme['primary_color']) . " !important;\n";
                echo "--secondary: " . htmlspecialchars($active_theme['secondary_color']) . " !important;\n";
                echo "--accent: " . htmlspecialchars($active_theme['accent_color']) . " !important;\n";
                echo "--dark: " . htmlspecialchars($active_theme['dark_color']) . " !important;\n";
                echo "--light: " . htmlspecialchars($active_theme['light_color']) . " !important;\n";
            }
            ?>
        }
        
        <?php if ($active_theme): ?>
        body {
            font-family: <?php echo htmlspecialchars($active_theme['font_family']); ?> !important;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: <?php echo htmlspecialchars($active_theme['font_family']); ?> !important;
        }
        <?php endif; ?>
    </style>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .test-box {
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .color-box {
            padding: 30px;
            border-radius: 8px;
            color: white;
            margin: 10px 0;
            text-align: center;
            font-weight: bold;
        }
        .theme-info {
            background: #f0f9ff;
            padding: 15px;
            border-left: 4px solid #3b82f6;
            border-radius: 5px;
            margin: 20px 0;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 0.9em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .test-element {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-test-primary {
            background-color: var(--primary);
            color: white;
        }
        .btn-test-secondary {
            background-color: var(--secondary);
            color: white;
        }
        .btn-test-accent {
            background-color: var(--accent);
            color: white;
        }
        .text-test-primary {
            color: var(--primary);
        }
        .text-test-secondary {
            color: var(--secondary);
        }
    </style>
</head>
<body>
    <h1>🎨 Complete Theme System Test</h1>
    
    <div class="theme-info">
        <h3>Active Theme Information</h3>
        <?php if ($active_theme): ?>
            <table>
                <tr>
                    <th>Property</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Theme Name</td>
                    <td><strong><?php echo htmlspecialchars($active_theme['name']); ?></strong></td>
                </tr>
                <tr>
                    <td>Slug</td>
                    <td><code><?php echo htmlspecialchars($active_theme['theme_slug']); ?></code></td>
                </tr>
                <tr>
                    <td>Primary Color</td>
                    <td><code><?php echo htmlspecialchars($active_theme['primary_color']); ?></code></td>
                </tr>
                <tr>
                    <td>Secondary Color</td>
                    <td><code><?php echo htmlspecialchars($active_theme['secondary_color']); ?></code></td>
                </tr>
                <tr>
                    <td>Accent Color</td>
                    <td><code><?php echo htmlspecialchars($active_theme['accent_color']); ?></code></td>
                </tr>
                <tr>
                    <td>Dark Color</td>
                    <td><code><?php echo htmlspecialchars($active_theme['dark_color']); ?></code></td>
                </tr>
                <tr>
                    <td>Light Color</td>
                    <td><code><?php echo htmlspecialchars($active_theme['light_color']); ?></code></td>
                </tr>
                <tr>
                    <td>Font Family</td>
                    <td><code><?php echo htmlspecialchars($active_theme['font_family']); ?></code></td>
                </tr>
            </table>
        <?php else: ?>
            <p class="error"><strong>ERROR: No active theme found!</strong></p>
        <?php endif; ?>
    </div>
    
    <h2>Test 1: CSS Variables Direct Test</h2>
    <p>These boxes use CSS variables directly via <code>background-color: var(--primary)</code></p>
    
    <div class="color-box" style="background-color: var(--primary);">
        Primary Color (--primary)
    </div>
    
    <div class="color-box" style="background-color: var(--secondary);">
        Secondary Color (--secondary)
    </div>
    
    <div class="color-box" style="background-color: var(--accent);">
        Accent Color (--accent)
    </div>
    
    <h2>Test 2: CSS Classes Test</h2>
    <p>These elements use CSS classes that reference variables</p>
    
    <p>
        <span class="test-element btn-test-primary">Primary Button</span>
        <span class="test-element btn-test-secondary">Secondary Button</span>
        <span class="test-element btn-test-accent">Accent Button</span>
    </p>
    
    <p>
        <span class="test-element text-test-primary">Primary Text</span>
        <span class="test-element text-test-secondary">Secondary Text</span>
    </p>
    
    <h2>Test 3: Tailwind Classes Test</h2>
    <p>These elements use Tailwind classes with custom color configuration</p>
    
    <div class="test-grid">
        <div class="bg-primary text-white p-6 rounded" style="background-color: var(--primary);">
            Background Primary
        </div>
        <div class="bg-secondary text-white p-6 rounded" style="background-color: var(--secondary);">
            Background Secondary
        </div>
        <div class="bg-accent text-white p-6 rounded" style="background-color: var(--accent);">
            Background Accent
        </div>
    </div>
    
    <h2>Test 4: Font Test</h2>
    <p>This text should show the theme's font family</p>
    
    <div style="padding: 20px; border: 2px solid var(--primary); border-radius: 8px; margin: 20px 0;">
        <h1 style="margin: 10px 0;">Heading Font Test (H1)</h1>
        <p style="margin: 10px 0; font-size: 1.1em;">Body text with theme font.</p>
        <p style="margin: 10px 0; font-size: 0.95em;">This paragraph uses the theme's font family.</p>
    </div>
    
    <h2>Test 5: All Available Themes</h2>
    <p>Click on a theme to activate it and see the colors change:</p>
    
    <div class="test-grid">
        <?php foreach ($all_themes as $theme): ?>
            <div class="test-box" style="border: 3px solid <?php echo htmlspecialchars($theme['primary_color']); ?>;">
                <h3 style="color: <?php echo htmlspecialchars($theme['primary_color']); ?>;">
                    <?php echo htmlspecialchars($theme['name']); ?>
                    <?php if ($active_theme && $active_theme['id'] === $theme['id']): ?>
                        <span style="color: green;">✓ ACTIVE</span>
                    <?php endif; ?>
                </h3>
                
                <div style="display: flex; gap: 5px; margin: 10px 0;">
                    <div style="flex: 1; height: 30px; background-color: <?php echo htmlspecialchars($theme['primary_color']); ?>; border-radius: 3px; title='Primary: <?php echo htmlspecialchars($theme['primary_color']); ?>'"></div>
                    <div style="flex: 1; height: 30px; background-color: <?php echo htmlspecialchars($theme['secondary_color']); ?>; border-radius: 3px; title='Secondary: <?php echo htmlspecialchars($theme['secondary_color']); ?>'"></div>
                    <div style="flex: 1; height: 30px; background-color: <?php echo htmlspecialchars($theme['accent_color']); ?>; border-radius: 3px; title='Accent: <?php echo htmlspecialchars($theme['accent_color']); ?>'"></div>
                </div>
                
                <p style="margin: 10px 0; font-size: 0.85em;">
                    <strong>Font:</strong> <?php echo htmlspecialchars($theme['font_family']); ?><br>
                    <strong>Primary:</strong> <code><?php echo htmlspecialchars($theme['primary_color']); ?></code>
                </p>
                
                <form method="POST" action="quick-theme-switcher.php" style="margin-top: 10px;">
                    <input type="hidden" name="theme_id" value="<?php echo intval($theme['id']); ?>">
                    <button type="submit" style="width: 100%; padding: 8px; background-color: <?php echo htmlspecialchars($theme['primary_color']); ?>; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
                        <?php echo ($active_theme && $active_theme['id'] === $theme['id']) ? '✓ Active' : 'Activate'; ?>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    
    <hr style="margin-top: 30px;">
    <p><a href="index.php">← Back to Home</a> | <a href="admin/manage-themes.php">Go to Admin Themes →</a></p>
</body>
</html>
