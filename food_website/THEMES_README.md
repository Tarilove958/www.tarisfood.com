# Theme System Documentation

## Overview

The Food Website now features a comprehensive theming system that allows administrators to switch between multiple pre-designed themes and users to customize their preferred appearance. The system includes 5 professionally designed themes with unique color schemes, typography, and design styles.

## Features

✅ **5 Pre-Designed Themes**
- Default Theme (Modern Blue & Orange)
- Premium Sunset (Elegant Warmth)
- Ocean Breeze (Cool & Fresh)
- Forest Green (Natural & Organic)
- Berry Purple (Vibrant & Modern)

✅ **Dynamic Color Switching**
- Primary and secondary colors
- Accent colors
- Dark and light background colors
- All colors changeable per theme

✅ **Typography Customization**
- Different font families per theme
- Heading fonts
- Body fonts

✅ **User Preferences**
- Logged-in users can save their preferred theme
- Guest users' theme preferences saved via cookies/sessions
- Admin can set site-wide active theme

✅ **Professional Admin Dashboard**
- Theme management interface
- Visual theme previews
- Color swatch display
- One-click theme activation

## Themes Included

### 1. Default Theme
- **Primary:** #3b82f6 (Blue)
- **Secondary:** #f97316 (Orange)
- **Font:** Outfit, sans-serif
- **Style:** Modern, clean, professional
- **Accent:** Purple (#8b5cf6)

### 2. Premium Sunset
- **Primary:** #E74C3C (Red)
- **Secondary:** #F39C12 (Gold)
- **Font:** Poppins, sans-serif
- **Style:** Elegant, luxury, warm
- **Accent:** Dark Red (#C0392B)

### 3. Ocean Breeze
- **Primary:** #16A085 (Teal)
- **Secondary:** #2980B9 (Blue)
- **Font:** Lato, sans-serif
- **Style:** Minimal, cool, refreshing
- **Accent:** Green (#27AE60)

### 4. Forest Green
- **Primary:** #27AE60 (Green)
- **Secondary:** #8E44AD (Purple)
- **Font:** Raleway, sans-serif
- **Style:** Natural, organic, earthy
- **Accent:** Dark Green (#1E8449)

### 5. Berry Purple
- **Primary:** #9B59B6 (Purple)
- **Secondary:** #E91E63 (Pink)
- **Font:** Inter, sans-serif
- **Style:** Modern, vibrant, energetic
- **Accent:** Blue (#3498DB)

## Installation & Setup

### Step 1: Run the Setup Script

1. Navigate to your website root directory
2. Open your browser and go to: `http://localhost/food_website/setup-themes.php`
3. Click the "Initialize Theme System" button

This will:
- Create the `themes` table
- Create the `theme_settings` table
- Create the `theme_user_preferences` table
- Insert all 5 default themes

### Step 2: Access Theme Manager

1. Log in to the admin dashboard
2. Go to: **Admin Dashboard > Website Themes**
3. You'll see all 5 available themes with visual previews

## How It Works

### Database Structure

**themes table:**
```sql
- theme_id (INT, Primary Key)
- theme_name (VARCHAR)
- theme_slug (VARCHAR, Unique)
- description (TEXT)
- is_active (BOOLEAN)
- is_default (BOOLEAN)
- primary_color (VARCHAR - Hex color)
- secondary_color (VARCHAR - Hex color)
- accent_color (VARCHAR - Hex color)
- dark_color (VARCHAR - Hex color)
- light_color (VARCHAR - Hex color)
- font_family (VARCHAR)
- header_style (VARCHAR)
```

**theme_user_preferences table:**
```sql
- preference_id (INT, Primary Key)
- user_id (INT, Foreign Key)
- theme_id (INT, Foreign Key)
- Stores individual user theme preferences
```

### PHP Integration

#### Functions Available (in `includes/theme-manager.php`)

**Get all themes:**
```php
$themes = getAllThemes();
```

**Get currently active theme:**
```php
$theme = getActiveTheme();
```

**Get theme by slug:**
```php
$theme = getThemeBySlug('premium-sunset');
```

**Get theme by ID:**
```php
$theme = getThemeByID(2);
```

**Set active theme (Admin only):**
```php
setActiveTheme($theme_id);
```

**Set user theme preference:**
```php
setUserThemePreference($theme_id);
```

**Get theme CSS path:**
```php
$css_path = getThemeCSSPath();
// Returns: assets/themes/default.css
```

**Get theme colors:**
```php
$colors = getThemeColors();
// Returns CSS color variables
```

**Get theme font:**
```php
$font = getThemeFont();
// Returns: Outfit, sans-serif
```

### CSS Files Location

All theme CSS files are located in: `assets/themes/`

- `assets/themes/default.css`
- `assets/themes/premium-sunset.css`
- `assets/themes/ocean-breeze.css`
- `assets/themes/forest-green.css`
- `assets/themes/berry-purple.css`

Each CSS file defines:
- `:root` variables with theme colors
- Font families
- Button styles
- Card styles
- Component styling
- Animations specific to the theme

### Frontend Implementation

The header automatically loads the active theme:

```php
<?php 
    $active_theme = getActiveTheme();
    $theme_css_path = getThemeCSSPath($active_theme);
?>
<link rel="stylesheet" href="<?php echo $theme_css_path; ?>">

<!-- Inline Theme Colors -->
<style>
    :root {
        --primary: <?php echo $theme['primary_color']; ?>;
        --secondary: <?php echo $theme['secondary_color']; ?>;
        --accent: <?php echo $theme['accent_color']; ?>;
        --dark: <?php echo $theme['dark_color']; ?>;
        --light: <?php echo $theme['light_color']; ?>;
    }
    body { 
        font-family: <?php echo $theme['font_family']; ?>; 
    }
</style>
```

## Admin Usage

### To Change the Site-Wide Theme

1. Log in as Admin
2. Navigate to: **Manage Themes** (Admin Dashboard sidebar)
3. Click the **"Activate Theme"** button on any theme card
4. The website will instantly switch to that theme for all users

### To View Theme Details

The theme manager displays:
- Theme name and description
- Color swatches (visual color preview)
- Font family information
- Header style
- Hex color codes
- Color values for each component

## User Theme Preferences

### For Logged-In Users

Users can set their personal theme preference from their profile:

```php
// Include in user settings page
<?php require_once '../includes/theme-switcher.php'; ?>
```

This displays a theme selector where users can choose their preferred theme. Their preference is saved and persists across sessions.

### For Guest Users

Guest users' theme preferences are saved in:
- Browser session: `$_SESSION['theme_id']`
- Browser cookies: `theme_id` (365 days expiration)

## Customizing Themes

### To Create a New Custom Theme

1. Create a new CSS file in `assets/themes/`
2. Define the theme colors and styles
3. Add the theme to the database using the admin interface or SQL:

```sql
INSERT INTO themes (theme_name, theme_slug, description, is_active, is_default, 
                   primary_color, secondary_color, accent_color, dark_color, 
                   light_color, font_family, header_style) 
VALUES (
    'My Custom Theme',
    'my-custom-theme',
    'Description of my theme',
    FALSE,
    FALSE,
    '#FF0000',
    '#00FF00',
    '#0000FF',
    '#333333',
    '#FFFFFF',
    'Arial, sans-serif',
    'modern'
);
```

### To Modify Existing Theme Colors

Edit the CSS file directly:
```css
:root {
    --primary: #3b82f6;
    --secondary: #f97316;
    /* ... other colors */
}
```

Then update the database record:
```sql
UPDATE themes 
SET primary_color = '#NEWCOLOR'
WHERE theme_slug = 'theme-name';
```

## What Changes With Themes

When you switch themes, these elements are affected:

- ✓ Primary button colors
- ✓ Secondary button colors
- ✓ Header background and styling
- ✓ Link colors
- ✓ Card and component styling
- ✓ Badge colors
- ✓ Section background colors
- ✓ Border colors
- ✓ Font families
- ✓ Animation effects
- ✓ Hover states
- ✓ Gradients and transitions

## Color Variables Used

Each theme defines these CSS variables:

```css
:root {
    --primary: #3b82f6;       /* Main color for CTAs and primary elements */
    --secondary: #f97316;     /* Secondary actions and highlights */
    --dark: #1f2937;          /* Text and dark elements */
    --light: #f9fafb;         /* Light backgrounds */
    --accent: #8b5cf6;        /* Accent details */
    --success: #10b981;       /* Success states */
    --danger: #ef4444;        /* Error/danger states */
}
```

## File Structure

```
food_website/
├── includes/
│   ├── theme-manager.php       (Theme functions)
│   ├── theme-switcher.php      (User theme selector)
│   └── header.php              (Modified to load themes)
├── admin/
│   └── manage-themes.php       (Admin theme manager)
├── assets/
│   └── themes/
│       ├── default.css
│       ├── premium-sunset.css
│       ├── ocean-breeze.css
│       ├── forest-green.css
│       └── berry-purple.css
├── setup-themes.php            (Setup script)
├── themes_migration.sql        (Database migration)
└── THEMES_README.md            (This file)
```

## Troubleshooting

### Theme Not Changing

1. **Clear browser cache** (Ctrl+Shift+Del)
2. **Check if setup-themes.php was run**
3. **Verify database tables exist:**
   ```sql
   SHOW TABLES LIKE 'theme%';
   ```
4. **Check if theme CSS file exists** in `assets/themes/`

### Colors Not Applying

1. **Ensure theme-manager.php is loaded** in header.php
2. **Check for CSS conflicts** with other stylesheets
3. **Verify database color values** are valid hex codes
4. **Clear CSS cache** if using a CDN

### User Preferences Not Saving

1. **Check if user is logged in** (verify session)
2. **Check database** for theme_user_preferences table
3. **Verify foreign key constraints** are not violated

## API Reference

### Functions in `includes/theme-manager.php`

```php
// Get all themes
getAllThemes() : array

// Get active theme
getActiveTheme() : array|null

// Get theme by slug
getThemeBySlug($slug) : array|null

// Get theme by ID
getThemeByID($id) : array|null

// Set active theme (admin only)
setActiveTheme($theme_id) : bool

// Set user preference
setUserThemePreference($theme_id) : bool

// Get CSS path
getThemeCSSPath($theme = null) : string

// Get colors
getThemeColors($theme = null) : string

// Get font
getThemeFont($theme = null) : string

// Get header style
getThemeHeaderStyle($theme = null) : string

// Get all theme settings
getThemeSettings($theme_id) : array

// Get specific theme setting
getThemeSetting($theme_id, $key, $default = '') : string

// Update theme setting
updateThemeSetting($theme_id, $key, $value) : bool

// Create new theme
createTheme($data) : int|bool

// Update theme
updateTheme($theme_id, $data) : bool

// Delete theme
deleteTheme($theme_id) : bool
```

## Best Practices

1. **Always test after switching themes** - Check all pages
2. **Keep theme names descriptive** - Easy identification
3. **Use consistent color palettes** - Professional appearance
4. **Test with different screen sizes** - Responsive design
5. **Backup database** before major changes
6. **Document custom themes** - For future reference

## Support

For issues or customizations, refer to:
- Theme CSS files in `assets/themes/`
- Admin theme manager at `/admin/manage-themes.php`
- Database tables: `themes`, `theme_settings`, `theme_user_preferences`

---

**Version:** 1.0.0  
**Created:** 2025  
**Last Updated:** December 2025
