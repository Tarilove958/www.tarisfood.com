<?php
/**
 * Theme Manager Functions
 * Handles all theme-related operations and switching
 */

// Ensure config is loaded
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/config.php';
}

/* -------------------------------------------------------------------------- */
/* THEME FUNCTIONS                                                            */
/* -------------------------------------------------------------------------- */

/**
 * Get all available themes
 */
if (!function_exists('getAllThemes')) {
    function getAllThemes() {
        global $conn;
        
        $sql = "SELECT * FROM themes ORDER BY is_default DESC, theme_name ASC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}

/**
 * Get active/current theme
 */
if (!function_exists('getActiveTheme')) {
    function getActiveTheme() {
        global $conn;
        
        // First check if there's a user preference
        if (isLoggedIn()) {
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT * FROM theme_user_preferences WHERE user_id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $pref = $result->fetch_assoc();
                $theme_sql = "SELECT * FROM themes WHERE theme_id = ? LIMIT 1";
                $theme_stmt = $conn->prepare($theme_sql);
                $theme_stmt->bind_param("i", $pref['theme_id']);
                $theme_stmt->execute();
                $theme_result = $theme_stmt->get_result();
                
                if ($theme_result->num_rows > 0) {
                    return $theme_result->fetch_assoc();
                }
            }
        }
        
        // Check session for guest theme preference
        if (isset($_SESSION['theme_id'])) {
            $theme_id = $_SESSION['theme_id'];
            $sql = "SELECT * FROM themes WHERE theme_id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $theme_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
        }
        
        // Check cookie
        if (isset($_COOKIE['theme_id'])) {
            $theme_id = (int)$_COOKIE['theme_id'];
            $sql = "SELECT * FROM themes WHERE theme_id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $theme_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
        }
        
        // Fall back to active theme or default
        $sql = "SELECT * FROM themes WHERE is_active = TRUE ORDER BY is_default DESC LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        // Last resort - get any theme
        $sql = "SELECT * FROM themes LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
}

/**
 * Get theme by slug
 */
if (!function_exists('getThemeBySlug')) {
    function getThemeBySlug($slug) {
        global $conn;
        
        $sql = "SELECT * FROM themes WHERE theme_slug = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
}

/**
 * Get theme by ID
 */
if (!function_exists('getThemeByID')) {
    function getThemeByID($theme_id) {
        global $conn;
        
        $sql = "SELECT * FROM themes WHERE theme_id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $theme_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
}

/**
 * Set active theme (Admin only)
 */
if (!function_exists('setActiveTheme')) {
    function setActiveTheme($theme_id) {
        global $conn;
        
        // Verify theme exists
        $theme = getThemeByID($theme_id);
        if (!$theme) {
            return false;
        }
        
        // Deactivate all themes
        $sql = "UPDATE themes SET is_active = FALSE";
        if (!$conn->query($sql)) {
            return false;
        }
        
        // Activate selected theme
        $sql = "UPDATE themes SET is_active = TRUE WHERE theme_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $theme_id);
        
        return $stmt->execute();
    }
}

/**
 * Set user theme preference
 */
if (!function_exists('setUserThemePreference')) {
    function setUserThemePreference($theme_id) {
        global $conn;
        
        if (!isLoggedIn()) {
            // Save to session and cookie for guests
            $_SESSION['theme_id'] = $theme_id;
            setcookie('theme_id', $theme_id, time() + (365 * 24 * 60 * 60), '/');
            return true;
        }
        
        $user_id = $_SESSION['user_id'];
        
        $sql = "INSERT INTO theme_user_preferences (user_id, theme_id) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE theme_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $theme_id, $theme_id);
        
        if ($stmt->execute()) {
            $_SESSION['theme_id'] = $theme_id;
            setcookie('theme_id', $theme_id, time() + (365 * 24 * 60 * 60), '/');
            return true;
        }
        
        return false;
    }
}

/**
 * Get theme CSS colors as inline style
 */
if (!function_exists('getThemeColors')) {
    function getThemeColors($theme = null) {
        if (!$theme) {
            $theme = getActiveTheme();
        }
        
        if (!$theme) {
            return '';
        }
        
        $colors = [
            '--primary' => $theme['primary_color'],
            '--secondary' => $theme['secondary_color'],
            '--accent' => $theme['accent_color'],
            '--dark' => $theme['dark_color'],
            '--light' => $theme['light_color'],
        ];
        
        $styles = '';
        foreach ($colors as $name => $value) {
            $styles .= "$name: $value; ";
        }
        
        return $styles;
    }
}

/**
 * Get theme font family
 */
if (!function_exists('getThemeFont')) {
    function getThemeFont($theme = null) {
        if (!$theme) {
            $theme = getActiveTheme();
        }
        
        return $theme ? $theme['font_family'] : 'Outfit, sans-serif';
    }
}

/**
 * Get theme header style
 */
if (!function_exists('getThemeHeaderStyle')) {
    function getThemeHeaderStyle($theme = null) {
        if (!$theme) {
            $theme = getActiveTheme();
        }
        
        return $theme ? $theme['header_style'] : 'modern';
    }
}

/**
 * Get theme CSS file path
 */
if (!function_exists('getThemeCSSPath')) {
    function getThemeCSSPath($theme = null) {
        if (!$theme) {
            $theme = getActiveTheme();
        }
        
        if (!$theme) {
            return 'assets/themes/default.css';
        }
        
        return 'assets/themes/' . $theme['theme_slug'] . '.css';
    }
}

/**
 * Get all theme settings
 */
if (!function_exists('getThemeSettings')) {
    function getThemeSettings($theme_id) {
        global $conn;
        
        $sql = "SELECT * FROM theme_settings WHERE theme_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $theme_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $settings = [];
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    }
}

/**
 * Get specific theme setting
 */
if (!function_exists('getThemeSetting')) {
    function getThemeSetting($theme_id, $key, $default = '') {
        global $conn;
        
        $sql = "SELECT setting_value FROM theme_settings WHERE theme_id = ? AND setting_key = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $theme_id, $key);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['setting_value'];
        }
        
        return $default;
    }
}

/**
 * Update theme setting
 */
if (!function_exists('updateThemeSetting')) {
    function updateThemeSetting($theme_id, $key, $value) {
        global $conn;
        
        $sql = "INSERT INTO theme_settings (theme_id, setting_key, setting_value) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE setting_value = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $theme_id, $key, $value, $value);
        
        return $stmt->execute();
    }
}

/**
 * Create new theme
 */
if (!function_exists('createTheme')) {
    function createTheme($data) {
        global $conn;
        
        $sql = "INSERT INTO themes (theme_name, theme_slug, description, primary_color, secondary_color, accent_color, dark_color, light_color, font_family, header_style) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssss",
            $data['theme_name'],
            $data['theme_slug'],
            $data['description'],
            $data['primary_color'],
            $data['secondary_color'],
            $data['accent_color'],
            $data['dark_color'],
            $data['light_color'],
            $data['font_family'],
            $data['header_style']
        );
        
        if ($stmt->execute()) {
            return $conn->insert_id;
        }
        
        return false;
    }
}

/**
 * Update theme
 */
if (!function_exists('updateTheme')) {
    function updateTheme($theme_id, $data) {
        global $conn;
        
        $sql = "UPDATE themes SET 
                theme_name = ?, 
                description = ?, 
                primary_color = ?, 
                secondary_color = ?, 
                accent_color = ?, 
                dark_color = ?, 
                light_color = ?, 
                font_family = ?, 
                header_style = ?
                WHERE theme_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssssssi",
            $data['theme_name'],
            $data['description'],
            $data['primary_color'],
            $data['secondary_color'],
            $data['accent_color'],
            $data['dark_color'],
            $data['light_color'],
            $data['font_family'],
            $data['header_style'],
            $theme_id
        );
        
        return $stmt->execute();
    }
}

/**
 * Delete theme
 */
if (!function_exists('deleteTheme')) {
    function deleteTheme($theme_id) {
        global $conn;
        
        // Don't allow deleting if it's the only active theme
        $sql = "SELECT COUNT(*) as count FROM themes WHERE is_active = TRUE";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        
        if ($row['count'] <= 1) {
            return false;
        }
        
        $sql = "DELETE FROM themes WHERE theme_id = ? AND is_default = FALSE";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $theme_id);
        
        return $stmt->execute();
    }
}
?>
