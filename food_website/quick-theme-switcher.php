<?php
// Quick theme switcher for testing
session_start();
require_once 'includes/config.php';
require_once 'includes/theme-manager.php';

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    die("Error: Not logged in as admin");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme_id'])) {
    $theme_id = intval($_POST['theme_id']);
    setActiveTheme($theme_id);
    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
    exit;
}

$all_themes = getAllThemes();
$active_theme = getActiveTheme();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Theme Switcher</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .theme-list {
            list-style: none;
            padding: 0;
        }
        .theme-item {
            padding: 15px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .theme-item.active {
            background: #e0e7ff;
            border-color: #3b82f6;
        }
        .color-swatch {
            width: 50px;
            height: 50px;
            border-radius: 5px;
        }
        form {
            display: inline;
        }
        button {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            background: #3b82f6;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #2563eb;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>🎨 Quick Theme Switcher</h1>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="success">✓ Theme activated! Refresh the page to see changes.</div>
    <?php endif; ?>
    
    <p><strong>Currently Active Theme:</strong> <?php echo htmlspecialchars($active_theme['name'] ?? 'None'); ?></p>
    
    <h2>Available Themes:</h2>
    
    <ul class="theme-list">
        <?php foreach ($all_themes as $theme): ?>
            <li class="theme-item <?php echo ($active_theme && $active_theme['id'] === $theme['id']) ? 'active' : ''; ?>">
                <div class="color-swatch" style="background: <?php echo htmlspecialchars($theme['primary_color']); ?>; border: 2px solid <?php echo htmlspecialchars($theme['secondary_color']); ?>;"></div>
                <div>
                    <strong><?php echo htmlspecialchars($theme['name']); ?></strong><br>
                    <small>Primary: <?php echo htmlspecialchars($theme['primary_color']); ?> | Secondary: <?php echo htmlspecialchars($theme['secondary_color']); ?></small>
                </div>
                <?php if ($active_theme && $active_theme['id'] === $theme['id']): ?>
                    <span style="margin-left: auto; color: #3b82f6; font-weight: bold;">✓ ACTIVE</span>
                <?php else: ?>
                    <form method="POST" style="margin-left: auto;">
                        <input type="hidden" name="theme_id" value="<?php echo intval($theme['id']); ?>">
                        <button type="submit">Activate</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <hr style="margin-top: 30px;">
    <p><a href="index.php">← Back to Home</a> | <a href="test-theme-css.php">Test Theme CSS →</a></p>
</body>
</html>
