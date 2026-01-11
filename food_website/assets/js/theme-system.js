/**
 * Theme System - Real-time Theme Switching
 * Monitors for theme changes and applies them dynamically
 */

(function() {
    'use strict';

    // Store the initial theme
    let currentThemeId = getCookie('current_theme_id') || null;
    
    /**
     * Get a cookie value by name
     */
    function getCookie(name) {
        const nameEQ = name + "=";
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i].trim();
            if (cookie.indexOf(nameEQ) === 0) {
                return cookie.substring(nameEQ.length);
            }
        }
        return null;
    }

    /**
     * Set a cookie
     */
    function setCookie(name, value, days = 30) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
    }

    /**
     * Apply theme colors to the page dynamically
     */
    function applyThemeColors(colors) {
        const root = document.documentElement;
        
        if (colors.primary) root.style.setProperty('--primary', colors.primary, 'important');
        if (colors.secondary) root.style.setProperty('--secondary', colors.secondary, 'important');
        if (colors.accent) root.style.setProperty('--accent', colors.accent, 'important');
        if (colors.dark) root.style.setProperty('--dark', colors.dark, 'important');
        if (colors.light) root.style.setProperty('--light', colors.light, 'important');
    }

    /**
     * Check for theme updates from server
     */
    function checkThemeUpdate() {
        fetch('/food_website/includes/theme-refresh.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.theme) {
                    const newThemeId = data.theme.theme_id;
                    
                    // If theme has changed, apply it
                    if (currentThemeId && currentThemeId !== newThemeId.toString()) {
                        // Theme has changed, apply new colors
                        applyThemeColors(data.css_colors);
                        
                        // Update the theme CSS file
                        const themeLink = document.getElementById('theme-stylesheet');
                        if (themeLink) {
                            // Add cache buster
                            const cacheBuster = newThemeId + '_' + new Date().getTime();
                            themeLink.href = themeLink.href.split('?')[0] + '?v=' + cacheBuster;
                        }
                        
                        // Update stored theme ID
                        currentThemeId = newThemeId.toString();
                        setCookie('current_theme_id', currentThemeId);
                        
                        // Show toast notification
                        showThemeUpdateNotification(data.theme.theme_name);
                    }
                }
            })
            .catch(error => console.log('Theme check error:', error));
    }

    /**
     * Show a notification when theme changes
     */
    function showThemeUpdateNotification(themeName) {
        const notification = document.createElement('div');
        notification.className = 'theme-update-notification';
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            font-size: 14px;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        `;
        notification.textContent = '✓ Theme updated: ' + themeName;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    /**
     * Add CSS animations for notification
     */
    if (!document.getElementById('theme-notifications-css')) {
        const style = document.createElement('style');
        style.id = 'theme-notifications-css';
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Check for theme updates every 5 seconds
    // Reduce frequency to every 30 seconds in production
    setInterval(checkThemeUpdate, 30000);
    
    // Also check when page becomes visible (tab switch)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            checkThemeUpdate();
        }
    });
})();
