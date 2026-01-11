<?php
/**
 * Theme Switcher Component
 * Include this in the user profile or settings page for users to switch themes
 */
?>

<div class="theme-switcher-container">
    <h6 class="mb-3">Choose Your Theme</h6>
    
    <div class="row g-3">
        <?php 
        $themes = getAllThemes();
        $current_theme = getActiveTheme();
        
        // Get user's preferred theme if logged in
        $user_theme = $current_theme;
        if (isLoggedIn() && isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            global $conn;
            $sql = "SELECT t.* FROM themes t 
                    INNER JOIN theme_user_preferences p ON t.theme_id = p.theme_id 
                    WHERE p.user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user_theme = $result->fetch_assoc();
            }
        }
        
        foreach ($themes as $theme):
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="theme-option card border-2 <?php echo ($user_theme['theme_id'] === $theme['theme_id'] ? 'border-primary' : 'border-light'); ?>" 
                 style="cursor: pointer; transition: all 0.3s ease;">
                
                <!-- Preview Bar -->
                <div style="height: 40px; background: linear-gradient(135deg, <?php echo htmlspecialchars($theme['primary_color']); ?>, <?php echo htmlspecialchars($theme['secondary_color']); ?>); border-radius: 2px 2px 0 0;"></div>
                
                <div class="card-body p-3">
                    <h6 class="mb-2"><?php echo htmlspecialchars($theme['theme_name']); ?></h6>
                    
                    <!-- Color Swatches -->
                    <div class="d-flex gap-2 mb-2">
                        <div style="width: 24px; height: 24px; background-color: <?php echo htmlspecialchars($theme['primary_color']); ?>; border-radius: 3px; border: 1px solid #ddd;"></div>
                        <div style="width: 24px; height: 24px; background-color: <?php echo htmlspecialchars($theme['secondary_color']); ?>; border-radius: 3px; border: 1px solid #ddd;"></div>
                        <div style="width: 24px; height: 24px; background-color: <?php echo htmlspecialchars($theme['accent_color']); ?>; border-radius: 3px; border: 1px solid #ddd;"></div>
                    </div>
                    
                    <!-- Select Button -->
                    <form method="POST" class="theme-form">
                        <input type="hidden" name="action" value="set_user_theme">
                        <input type="hidden" name="theme_id" value="<?php echo $theme['theme_id']; ?>">
                        <button type="submit" class="btn btn-sm w-100 <?php echo ($user_theme['theme_id'] === $theme['theme_id'] ? 'btn-primary' : 'btn-outline-primary'); ?>">
                            <?php echo ($user_theme['theme_id'] === $theme['theme_id'] ? 'Current Theme' : 'Select'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .theme-option:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .theme-form {
        margin: 0;
    }
</style>
