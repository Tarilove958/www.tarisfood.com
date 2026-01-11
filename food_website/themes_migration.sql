-- ============================================================================
-- THEMES TABLE MIGRATION
-- Add this to your existing database to enable the theming system
-- ============================================================================

-- Create themes table
CREATE TABLE IF NOT EXISTS themes (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create theme settings table for additional configuration
CREATE TABLE IF NOT EXISTS theme_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    theme_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (theme_id) REFERENCES themes(theme_id) ON DELETE CASCADE,
    UNIQUE KEY unique_theme_setting (theme_id, setting_key),
    INDEX idx_theme (theme_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create user theme preferences table
CREATE TABLE IF NOT EXISTS theme_user_preferences (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default themes
INSERT INTO themes (theme_name, theme_slug, description, is_active, is_default, primary_color, secondary_color, accent_color, dark_color, light_color, font_family, header_style) VALUES
('Default Theme', 'default', 'Original modern food website theme with blue and orange colors', TRUE, TRUE, '#3b82f6', '#f97316', '#8b5cf6', '#1f2937', '#f9fafb', 'Outfit, sans-serif', 'modern'),
('Premium Sunset', 'premium-sunset', 'Elegant theme with warm sunset colors and luxury feel', FALSE, FALSE, '#E74C3C', '#F39C12', '#C0392B', '#2C3E50', '#ECF0F1', 'Poppins, sans-serif', 'elegant'),
('Ocean Breeze', 'ocean-breeze', 'Cool and refreshing theme with ocean-inspired colors', FALSE, FALSE, '#16A085', '#2980B9', '#27AE60', '#1A1A1A', '#F5F5F5', 'Lato, sans-serif', 'minimal'),
('Forest Green', 'forest-green', 'Natural and organic theme with earthy green tones', FALSE, FALSE, '#27AE60', '#8E44AD', '#1E8449', '#34495E', '#FAFAFA', 'Raleway, sans-serif', 'natural'),
('Berry Purple', 'berry-purple', 'Modern and vibrant theme with purple and pink accents', FALSE, FALSE, '#9B59B6', '#E91E63', '#3498DB', '#2C3E50', '#F8F9FA', 'Inter, sans-serif', 'modern');
