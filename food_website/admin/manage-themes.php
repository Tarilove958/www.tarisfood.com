<?php
/**
 * Admin Theme Manager Page
 * Manage website themes and appearance settings
 */

// Load required files
require_once '../includes/config.php';
require_once '../includes/theme-manager.php';
require_once 'includes/admin-functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Handle theme activation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = sanitize($_POST['action']);
    
    if ($action === 'activate_theme') {
        $theme_id = (int)$_POST['theme_id'];
        if (applyGlobalTheme($theme_id)) {
            setFlashMessage('success', 'Theme activated successfully! All users will now see the new theme.');
            // Clear any cached theme info
            if (isset($_SESSION['current_theme'])) {
                unset($_SESSION['current_theme']);
            }
            // Clear theme cookies
            setcookie('theme_id', '', time() - 3600, '/');
        } else {
            setFlashMessage('error', 'Failed to activate theme.');
        }
        redirect('manage-themes.php');
    }
    
    if ($action === 'set_user_theme') {
        $theme_id = (int)$_POST['theme_id'];
        if (setUserThemePreference($theme_id)) {
            setFlashMessage('success', 'Your theme preference has been saved!');
        } else {
            setFlashMessage('error', 'Failed to save theme preference.');
        }
        redirect('manage-themes.php');
    }
}

// Include header
require_once 'includes/admin-header.php';

// Get all themes
$themes = getAllThemes();
$active_theme = getActiveTheme();
$flash = getFlashMessage();
?>

<div class="container-fluid p-4 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen">
    <!-- Flash Messages -->
    <?php if ($flash): ?>
        <div class="mb-4">
            <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show d-flex align-items-center" role="alert" style="border-left: 5px solid; border-radius: 8px;">
                <i class="bi <?php echo $flash['type'] === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'; ?> me-3" style="font-size: 1.5rem;"></i>
                <span><?php echo htmlspecialchars($flash['message']); ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Page Header - Premium Design -->
    <div class="mb-5">
        <div class="text-white">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl">
                    <i class="bi bi-palette2 text-white" style="font-size: 2rem;"></i>
                </div>
                <div>
                    <h1 class="mb-1 fw-bold" style="font-size: 2.5rem;">Website Themes</h1>
                    <p class="text-gray-300 mb-0">Manage and customize your website appearance with professional theme designs</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Theme Spotlight - Premium Card -->
    <?php if ($active_theme): ?>
    <div class="mb-5">
        <div class="card border-0 overflow-hidden" style="background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.9) 100%); backdrop-filter: blur(10px); box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <div class="card-body p-0">
                <div style="background: linear-gradient(135deg, <?php echo htmlspecialchars($active_theme['primary_color']); ?>, <?php echo htmlspecialchars($active_theme['secondary_color']); ?>); height: 120px; position: relative; overflow: hidden;">
                    <!-- Decorative elements -->
                    <div style="position: absolute; top: -50%; right: -10%; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -30%; left: 10%; width: 200px; height: 200px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                </div>
                
                <div class="p-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="p-2 bg-success bg-opacity-10 rounded-lg">
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 1.8rem;"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-bold">Currently Active Theme</h4>
                                    <p class="text-gray-600 mb-0">This theme is being used on your website</p>
                                </div>
                            </div>
                            <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($active_theme['theme_name']); ?></h5>
                            <p class="text-gray-600 mb-0"><?php echo htmlspecialchars($active_theme['description']); ?></p>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 bg-light rounded-xl">
                                <div class="mb-4">
                                    <p class="text-gray-600 text-sm mb-2"><strong>Color Palette</strong></p>
                                    <div class="d-flex gap-2">
                                        <div class="rounded-lg" style="width: 40px; height: 40px; background-color: <?php echo htmlspecialchars($active_theme['primary_color']); ?>; border: 2px solid #ddd; cursor: pointer;" title="Primary: <?php echo htmlspecialchars($active_theme['primary_color']); ?>"></div>
                                        <div class="rounded-lg" style="width: 40px; height: 40px; background-color: <?php echo htmlspecialchars($active_theme['secondary_color']); ?>; border: 2px solid #ddd; cursor: pointer;" title="Secondary: <?php echo htmlspecialchars($active_theme['secondary_color']); ?>"></div>
                                        <div class="rounded-lg" style="width: 40px; height: 40px; background-color: <?php echo htmlspecialchars($active_theme['accent_color']); ?>; border: 2px solid #ddd; cursor: pointer;" title="Accent: <?php echo htmlspecialchars($active_theme['accent_color']); ?>"></div>
                                        <div class="rounded-lg" style="width: 40px; height: 40px; background-color: <?php echo htmlspecialchars($active_theme['dark_color']); ?>; border: 2px solid #ddd; cursor: pointer;" title="Dark: <?php echo htmlspecialchars($active_theme['dark_color']); ?>"></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm mb-2"><strong>Typography</strong></p>
                                    <p class="mb-0 text-sm" style="font-family: <?php echo htmlspecialchars($active_theme['font_family']); ?>;">
                                        <span class="badge" style="background-color: <?php echo htmlspecialchars($active_theme['primary_color']); ?>;"><?php echo str_replace(', sans-serif', '', htmlspecialchars($active_theme['font_family'])); ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Themes Grid -->
    <div class="mb-4">
        <h5 class="text-white fw-bold mb-4">
            <i class="bi bi-grid-3x2-gap me-2"></i>Available Themes
        </h5>
    </div>

    <div class="row g-4">
        <?php if (!empty($themes)): ?>
            <?php foreach ($themes as $theme): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 theme-card-premium" style="transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); overflow: hidden;">
                    
                    <!-- Theme Preview Header - Animated Gradient -->
                    <div class="theme-preview-header" style="height: 100px; background: linear-gradient(135deg, <?php echo htmlspecialchars($theme['primary_color']); ?>, <?php echo htmlspecialchars($theme['secondary_color']); ?>); position: relative; overflow: hidden;">
                        <!-- Animated background elements -->
                        <div style="position: absolute; top: -50%; right: -15%; width: 250px; height: 250px; background: rgba(255,255,255,0.2); border-radius: 50%; animation: float 6s ease-in-out infinite;"></div>
                        <div style="position: absolute; bottom: -40%; left: 5%; width: 180px; height: 180px; background: rgba(255,255,255,0.1); border-radius: 50%; animation: float 8s ease-in-out infinite 1s;"></div>
                        
                        <!-- Active Badge -->
                        <?php if ($theme['theme_id'] === $active_theme['theme_id']): ?>
                        <div style="position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.2); backdrop-filter: blur(10px); padding: 6px 12px; border-radius: 20px; color: white; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                            <i class="bi bi-check-circle-fill"></i> Active
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <h6 class="card-title mb-1 fw-bold" style="font-size: 1.1rem;"><?php echo htmlspecialchars($theme['theme_name']); ?></h6>
                                    <p class="text-gray-600 text-sm mb-0"><?php echo htmlspecialchars($theme['description']); ?></p>
                                </div>
                            </div>

                            <!-- Status Badges -->
                            <div class="d-flex gap-2 mb-3">
                                <?php if ($theme['is_default']): ?>
                                    <span class="badge bg-secondary bg-opacity-80 text-white">
                                        <i class="bi bi-star-fill me-1"></i> Default
                                    </span>
                                <?php endif; ?>
                                <?php if ($theme['theme_id'] === $active_theme['theme_id']): ?>
                                    <span class="badge" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($theme['primary_color']); ?>, <?php echo htmlspecialchars($theme['secondary_color']); ?>); color: white;">
                                        <i class="bi bi-check-circle me-1"></i> Active Now
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Color Swatches - Enhanced -->
                        <div class="mb-4 p-3 bg-light rounded-lg">
                            <p class="text-gray-700 text-sm mb-2 fw-600"><i class="bi bi-palette me-2"></i>Color Palette</p>
                            <div class="d-flex gap-2">
                                <div class="color-swatch-premium" style="width: 35px; height: 35px; background-color: <?php echo htmlspecialchars($theme['primary_color']); ?>; border-radius: 8px; border: 2px solid rgba(0,0,0,0.1); cursor: pointer; transition: all 0.3s;" title="Primary: <?php echo htmlspecialchars($theme['primary_color']); ?>"></div>
                                <div class="color-swatch-premium" style="width: 35px; height: 35px; background-color: <?php echo htmlspecialchars($theme['secondary_color']); ?>; border-radius: 8px; border: 2px solid rgba(0,0,0,0.1); cursor: pointer; transition: all 0.3s;" title="Secondary: <?php echo htmlspecialchars($theme['secondary_color']); ?>"></div>
                                <div class="color-swatch-premium" style="width: 35px; height: 35px; background-color: <?php echo htmlspecialchars($theme['accent_color']); ?>; border-radius: 8px; border: 2px solid rgba(0,0,0,0.1); cursor: pointer; transition: all 0.3s;" title="Accent: <?php echo htmlspecialchars($theme['accent_color']); ?>"></div>
                                <div class="color-swatch-premium" style="width: 35px; height: 35px; background-color: <?php echo htmlspecialchars($theme['dark_color']); ?>; border-radius: 8px; border: 2px solid rgba(0,0,0,0.1); cursor: pointer; transition: all 0.3s;" title="Dark: <?php echo htmlspecialchars($theme['dark_color']); ?>"></div>
                                <div class="color-swatch-premium" style="width: 35px; height: 35px; background-color: <?php echo htmlspecialchars($theme['light_color']); ?>; border-radius: 8px; border: 2px solid rgba(0,0,0,0.2); cursor: pointer; transition: all 0.3s;" title="Light: <?php echo htmlspecialchars($theme['light_color']); ?>"></div>
                            </div>
                        </div>

                        <!-- Theme Details Grid -->
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <div class="p-2 bg-light rounded-lg text-center">
                                    <p class="text-gray-600 text-xs mb-1">Font</p>
                                    <p class="mb-0 text-sm fw-600" style="font-size: 0.85rem; color: #333;">
                                        <?php echo str_replace(', sans-serif', '', htmlspecialchars($theme['font_family'])); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 bg-light rounded-lg text-center">
                                    <p class="text-gray-600 text-xs mb-1">Style</p>
                                    <p class="mb-0 text-sm fw-600" style="font-size: 0.85rem; color: #333;">
                                        <?php echo htmlspecialchars(ucfirst($theme['header_style'])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Color Code Reference -->
                        <div class="p-3 bg-dark bg-opacity-5 rounded-lg mb-4" style="border-left: 3px solid <?php echo htmlspecialchars($theme['primary_color']); ?>;">
                            <p class="text-gray-700 text-xs mb-2 fw-600">Color Codes</p>
                            <div class="row g-2" style="font-size: 0.75rem;">
                                <div class="col-6"><code><?php echo htmlspecialchars($theme['primary_color']); ?></code></div>
                                <div class="col-6"><code><?php echo htmlspecialchars($theme['secondary_color']); ?></code></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer - Button -->
                    <div class="card-footer bg-white border-top-0 p-3">
                        <?php if ($theme['theme_id'] !== $active_theme['theme_id']): ?>
                            <form method="POST" class="d-grid">
                                <input type="hidden" name="action" value="activate_theme">
                                <input type="hidden" name="theme_id" value="<?php echo $theme['theme_id']; ?>">
                                <button type="submit" class="btn btn-primary btn-sm fw-600 rounded-lg" style="padding: 10px; background: linear-gradient(135deg, <?php echo htmlspecialchars($theme['primary_color']); ?>, <?php echo htmlspecialchars($theme['secondary_color']); ?>); border: none; transition: all 0.3s;">
                                    <i class="bi bi-arrow-right-circle me-2"></i>Activate This Theme
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-sm w-100 fw-600 rounded-lg" style="padding: 10px; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none;" disabled>
                                <i class="bi bi-check-circle me-2"></i>Currently Active
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>No themes found!</strong> Please run the migration script to create default themes.
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Theme Information Section -->
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card border-0" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="card-header bg-gradient-to-r border-0 p-4" style="background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(168,85,247,0.1));">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle me-2" style="color: #6366f1;"></i>
                        <span style="color: #333;">Theme Information & Features</span>
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-collection me-2" style="color: #6366f1;"></i>Available Themes
                            </h6>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item border-0 px-0 py-2">
                                    <strong class="text-dark">Default Theme</strong>
                                    <p class="text-gray-600 text-sm mb-0">Modern clean design with blue and orange colors</p>
                                </div>
                                <div class="list-group-item border-0 px-0 py-2">
                                    <strong class="text-dark">Premium Sunset</strong>
                                    <p class="text-gray-600 text-sm mb-0">Elegant design with warm sunset colors and luxury feel</p>
                                </div>
                                <div class="list-group-item border-0 px-0 py-2">
                                    <strong class="text-dark">Ocean Breeze</strong>
                                    <p class="text-gray-600 text-sm mb-0">Cool and refreshing design with ocean-inspired colors</p>
                                </div>
                                <div class="list-group-item border-0 px-0 py-2">
                                    <strong class="text-dark">Forest Green</strong>
                                    <p class="text-gray-600 text-sm mb-0">Natural and organic design with earthy green tones</p>
                                </div>
                                <div class="list-group-item border-0 px-0 py-2">
                                    <strong class="text-dark">Berry Purple</strong>
                                    <p class="text-gray-600 text-sm mb-0">Modern and vibrant design with purple and pink accents</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="bi bi-palette-fill me-2" style="color: #ec4899;"></i>What Changes With Themes
                            </h6>
                            <ul class="list-unstyled text-sm">
                                <li class="mb-2 text-gray-700">
                                    <i class="bi bi-check-circle-fill me-2" style="color: #10b981;"></i>Primary and secondary color schemes
                                </li>
                                <li class="mb-2 text-gray-700">
                                    <i class="bi bi-check-circle-fill me-2" style="color: #10b981;"></i>Font families and typography
                                </li>
                                <li class="mb-2 text-gray-700">
                                    <i class="bi bi-check-circle-fill me-2" style="color: #10b981;"></i>Header and navigation styling
                                </li>
                                <li class="mb-2 text-gray-700">
                                    <i class="bi bi-check-circle-fill me-2" style="color: #10b981;"></i>Button styles and effects
                                </li>
                                <li class="mb-2 text-gray-700">
                                    <i class="bi bi-check-circle-fill me-2" style="color: #10b981;"></i>Card and component designs
                                </li>
                                <li class="mb-2 text-gray-700">
                                    <i class="bi bi-check-circle-fill me-2" style="color: #10b981;"></i>Background colors and gradients
                                </li>
                                <li class="text-gray-700">
                                    <i class="bi bi-check-circle-fill me-2" style="color: #10b981;"></i>Accent colors and highlights
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0 mt-4 border-0 rounded-lg" style="background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(168,85,247,0.1)); border-left: 4px solid #6366f1;">
                        <i class="bi bi-lightbulb me-2" style="color: #6366f1;"></i>
                        <strong style="color: #333;">Pro Tip:</strong>
                        <span style="color: #555;">Theme changes apply instantly to all users. This is the perfect way to refresh your website appearance without affecting any content or data.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Theme Cards */
    .theme-card-premium {
        border-radius: 12px;
        position: relative;
        overflow: hidden;
    }

    .theme-card-premium:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25) !important;
    }

    .theme-preview-header {
        position: relative;
        background-size: 400% 400%;
        animation: gradient 8s ease infinite;
    }

    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .color-swatch-premium {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .color-swatch-premium:hover {
        transform: scale(1.15) !important;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25) !important;
    }

    /* Floating Animation */
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        33% { transform: translateY(-20px) rotate(5deg); }
        66% { transform: translateY(10px) rotate(-5deg); }
    }

    /* Background Gradient Container */
    .bg-gradient-to-br {
        background: linear-gradient(135deg, #111827 0%, #1f2937 50%, #111827 100%);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Button Hover Effects */
    .theme-card-premium .card-footer button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2) !important;
    }

    /* Badge Styling */
    .badge {
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    /* Information Cards */
    .alert {
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }

    .list-group-item {
        background: transparent;
        border: none;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    /* Smooth Transitions */
    * {
        transition-property: background-color, border-color, color, box-shadow, transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }

    /* Code Block Styling */
    code {
        background-color: rgba(0, 0, 0, 0.05);
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
        color: #666;
        font-family: 'Courier New', monospace;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .theme-card-premium:hover {
            transform: translateY(-4px);
        }

        h1 {
            font-size: 1.75rem !important;
        }
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Selection styling */
    ::selection {
        background-color: rgba(99, 102, 241, 0.3);
        color: inherit;
    }
</style>

<?php require_once 'includes/admin-footer.php'; ?>
