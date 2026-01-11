<?php
/**
 * Theme System Setup Script
 * Run this once to initialize the theme tables and default themes
 * Access via: http://localhost/food_website/setup-themes.php
 */

// Security check - only allow from localhost
$allowed_ips = ['127.0.0.1', 'localhost', '::1'];
$user_ip = $_SERVER['REMOTE_ADDR'];

if (!in_array($user_ip, $allowed_ips) && !isset($_GET['force'])) {
    die('Access denied. This script can only be run locally.');
}

// Load configuration
require_once 'includes/config.php';

// Check if already setup
$setup_check = $conn->query("SHOW TABLES LIKE 'themes'");
$already_setup = $setup_check && $setup_check->num_rows > 0;

$status = [
    'success' => [],
    'error' => [],
    'info' => []
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
    
    // Step 1: Create themes table
    $themes_table = "CREATE TABLE IF NOT EXISTS themes (
        theme_id INT PRIMARY KEY AUTO_INCREMENT,
        theme_name VARCHAR(100) NOT NULL UNIQUE,
        theme_slug VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        is_active BOOLEAN DEFAULT FALSE,
        is_default BOOLEAN DEFAULT FALSE,
        primary_color VARCHAR(7) NOT NULL,
        secondary_color VARCHAR(7) NOT NULL,
        accent_color VARCHAR(7) NOT NULL,
        dark_color VARCHAR(7) NOT NULL,
        light_color VARCHAR(7) NOT NULL,
        font_family VARCHAR(100) NOT NULL,
        header_style VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_active (is_active),
        INDEX idx_default (is_default)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($themes_table)) {
        $status['success'][] = 'Themes table created successfully.';
    } else {
        $status['error'][] = 'Error creating themes table: ' . $conn->error;
    }
    
    // Step 2: Create theme_settings table
    $settings_table = "CREATE TABLE IF NOT EXISTS theme_settings (
        setting_id INT PRIMARY KEY AUTO_INCREMENT,
        theme_id INT NOT NULL,
        setting_key VARCHAR(100) NOT NULL,
        setting_value LONGTEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (theme_id) REFERENCES themes(theme_id) ON DELETE CASCADE,
        UNIQUE KEY unique_theme_setting (theme_id, setting_key),
        INDEX idx_theme (theme_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($settings_table)) {
        $status['success'][] = 'Theme settings table created successfully.';
    } else {
        $status['error'][] = 'Error creating theme_settings table: ' . $conn->error;
    }
    
    // Step 3: Create theme_user_preferences table
    $prefs_table = "CREATE TABLE IF NOT EXISTS theme_user_preferences (
        preference_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        theme_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (theme_id) REFERENCES themes(theme_id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_theme (user_id),
        INDEX idx_user (user_id),
        INDEX idx_theme (theme_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($prefs_table)) {
        $status['success'][] = 'Theme user preferences table created successfully.';
    } else {
        $status['error'][] = 'Error creating theme_user_preferences table: ' . $conn->error;
    }
    
    // Step 4: Insert default themes
    $themes_data = [
        [
            'theme_name' => 'Default Theme',
            'theme_slug' => 'default',
            'description' => 'Original modern food website theme with blue and orange colors',
            'is_active' => 1,
            'is_default' => 1,
            'primary_color' => '#3b82f6',
            'secondary_color' => '#f97316',
            'accent_color' => '#8b5cf6',
            'dark_color' => '#1f2937',
            'light_color' => '#f9fafb',
            'font_family' => 'Outfit, sans-serif',
            'header_style' => 'modern'
        ],
        [
            'theme_name' => 'Premium Sunset',
            'theme_slug' => 'premium-sunset',
            'description' => 'Elegant theme with warm sunset colors and luxury feel',
            'is_active' => 0,
            'is_default' => 0,
            'primary_color' => '#E74C3C',
            'secondary_color' => '#F39C12',
            'accent_color' => '#C0392B',
            'dark_color' => '#2C3E50',
            'light_color' => '#ECF0F1',
            'font_family' => 'Poppins, sans-serif',
            'header_style' => 'elegant'
        ],
        [
            'theme_name' => 'Ocean Breeze',
            'theme_slug' => 'ocean-breeze',
            'description' => 'Cool and refreshing theme with ocean-inspired colors',
            'is_active' => 0,
            'is_default' => 0,
            'primary_color' => '#16A085',
            'secondary_color' => '#2980B9',
            'accent_color' => '#27AE60',
            'dark_color' => '#1A1A1A',
            'light_color' => '#F5F5F5',
            'font_family' => 'Lato, sans-serif',
            'header_style' => 'minimal'
        ],
        [
            'theme_name' => 'Forest Green',
            'theme_slug' => 'forest-green',
            'description' => 'Natural and organic theme with earthy green tones',
            'is_active' => 0,
            'is_default' => 0,
            'primary_color' => '#27AE60',
            'secondary_color' => '#8E44AD',
            'accent_color' => '#1E8449',
            'dark_color' => '#34495E',
            'light_color' => '#FAFAFA',
            'font_family' => 'Raleway, sans-serif',
            'header_style' => 'natural'
        ],
        [
            'theme_name' => 'Berry Purple',
            'theme_slug' => 'berry-purple',
            'description' => 'Modern and vibrant theme with purple and pink accents',
            'is_active' => 0,
            'is_default' => 0,
            'primary_color' => '#9B59B6',
            'secondary_color' => '#E91E63',
            'accent_color' => '#3498DB',
            'dark_color' => '#2C3E50',
            'light_color' => '#F8F9FA',
            'font_family' => 'Inter, sans-serif',
            'header_style' => 'modern'
        ]
    ];
    
    foreach ($themes_data as $theme) {
        // Check if theme already exists
        $check_sql = "SELECT theme_id FROM themes WHERE theme_slug = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $theme['theme_slug']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            $insert_sql = "INSERT INTO themes (theme_name, theme_slug, description, is_active, is_default, primary_color, secondary_color, accent_color, dark_color, light_color, font_family, header_style) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param(
                "sssiisissss",
                $theme['theme_name'],
                $theme['theme_slug'],
                $theme['description'],
                $theme['is_active'],
                $theme['is_default'],
                $theme['primary_color'],
                $theme['secondary_color'],
                $theme['accent_color'],
                $theme['dark_color'],
                $theme['light_color'],
                $theme['font_family'],
                $theme['header_style']
            );
            
            if ($insert_stmt->execute()) {
                $status['success'][] = "Theme '{$theme['theme_name']}' inserted successfully.";
            } else {
                $status['error'][] = "Error inserting theme '{$theme['theme_name']}': " . $conn->error;
            }
        } else {
            $status['info'][] = "Theme '{$theme['theme_name']}' already exists.";
        }
    }
    
    $status['success'][] = 'Theme setup completed!';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme System Setup - Food Website</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .setup-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        .status-message {
            margin-bottom: 15px;
            padding: 12px 15px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .status-message.success {
            background-color: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .status-message.error {
            background-color: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        .status-message.info {
            background-color: #d1ecf1;
            border-left-color: #17a2b8;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1 class="mb-4 text-center">
            <i class="bi bi-palette"></i> Theme System Setup
        </h1>
        
        <p class="text-muted text-center mb-4">
            Initialize the theme system by creating necessary database tables and default themes.
        </p>

        <?php if (!empty($status['success']) || !empty($status['error']) || !empty($status['info'])): ?>
            <div class="status-section mb-4">
                <?php foreach ($status['success'] as $msg): ?>
                    <div class="status-message success">
                        <strong>✓</strong> <?php echo htmlspecialchars($msg); ?>
                    </div>
                <?php endforeach; ?>
                
                <?php foreach ($status['error'] as $msg): ?>
                    <div class="status-message error">
                        <strong>✗</strong> <?php echo htmlspecialchars($msg); ?>
                    </div>
                <?php endforeach; ?>
                
                <?php foreach ($status['info'] as $msg): ?>
                    <div class="status-message info">
                        <strong>ℹ</strong> <?php echo htmlspecialchars($msg); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!$_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alert alert-info mb-4">
                <strong>Note:</strong> This script will create three new database tables:
                <ul class="mb-0 mt-2">
                    <li><code>themes</code> - Stores theme definitions</li>
                    <li><code>theme_settings</code> - Stores theme-specific settings</li>
                    <li><code>theme_user_preferences</code> - Stores user theme preferences</li>
                </ul>
            </div>
            
            <form method="POST" class="text-center">
                <button type="submit" name="setup" value="1" class="btn btn-primary btn-lg">
                    <i class="bi bi-gear"></i> Initialize Theme System
                </button>
            </form>
        <?php else: ?>
            <div class="alert alert-success mb-4">
                <h5>Setup Complete!</h5>
                <p class="mb-2">The theme system has been initialized successfully. You can now:</p>
                <ul class="mb-0">
                    <li>Access the theme manager at: <code>/admin/manage-themes.php</code></li>
                    <li>Switch themes from the admin dashboard</li>
                    <li>Users can choose their preferred theme from their profile</li>
                </ul>
            </div>
            
            <a href="/food_website/admin/manage-themes.php" class="btn btn-success w-100 mb-2">
                Go to Theme Manager
            </a>
            <a href="/food_website/index.php" class="btn btn-outline-primary w-100">
                Back to Website
            </a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
