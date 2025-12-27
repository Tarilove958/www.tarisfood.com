/**
 * Main JavaScript File
 * Core UI Functions and Utilities
 */

// ===== TOAST NOTIFICATION SYSTEM =====
function showToast(type, message) {
    console.log('📢 showToast called:', { type, message });
    
    // Get or create toast container
    let toastContainer = document.getElementById('toast-container');
    
    if (!toastContainer) {
        console.log('Creating toast container...');
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-24 right-5 z-[9999] flex flex-col gap-2';
        toastContainer.style.cssText = 'pointer-events: none;';
        document.body.appendChild(toastContainer);
    }
    
    // Determine colors and icons based on type
    const config = {
        success: {
            bg: 'bg-green-500',
            icon: 'bi-check-circle-fill',
            text: 'text-white'
        },
        error: {
            bg: 'bg-red-500',
            icon: 'bi-exclamation-circle-fill',
            text: 'text-white'
        },
        warning: {
            bg: 'bg-yellow-500',
            icon: 'bi-exclamation-triangle-fill',
            text: 'text-white'
        },
        info: {
            bg: 'bg-blue-500',
            icon: 'bi-info-circle-fill',
            text: 'text-white'
        }
    };
    
    const typeConfig = config[type] || config.info;
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `${typeConfig.bg} ${typeConfig.text} px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3 min-w-[300px] max-w-md pointer-events-auto animate-slide-in`;
    toast.style.cssText = 'animation: slideInRight 0.3s ease-out;';
    
    toast.innerHTML = `
        <i class="bi ${typeConfig.icon} text-xl flex-shrink-0"></i>
        <span class="font-medium flex-1">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-2 hover:opacity-75 transition-opacity">
            <i class="bi bi-x-lg"></i>
        </button>
    `;
    
    // Add to container
    toastContainer.appendChild(toast);
    
    console.log('✅ Toast added to DOM');
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-in';
        toast.style.opacity = '0';
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }, 4000);
}

// Make showToast globally available
window.showToast = showToast;

// Alternative name for compatibility
window.showNotification = showToast;

// ===== ADD ANIMATIONS TO PAGE =====
function addAnimationStyles() {
    if (document.getElementById('toast-animations')) return;
    
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .animate-slide-in {
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .animate-bounce-once {
            animation: bounce 0.5s ease;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    `;
    document.head.appendChild(style);
}

// ===== FORMAT CURRENCY =====
function formatCurrency(amount) {
    return '₦' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

window.formatCurrency = formatCurrency;

// ===== MOBILE MENU TOGGLE =====
function initMobileMenu() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            const icon = this.querySelector('i');
            
            if (mobileMenu.classList.contains('hidden')) {
                icon.classList.remove('bi-x-lg');
                icon.classList.add('bi-list');
            } else {
                icon.classList.remove('bi-list');
                icon.classList.add('bi-x-lg');
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenuBtn.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.remove('bi-x-lg');
                icon.classList.add('bi-list');
            }
        });
    }
}

// ===== SCROLL REVEAL ANIMATION =====
function initScrollReveal() {
    const reveals = document.querySelectorAll('.reveal');
    
    if (reveals.length === 0) return;
    
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    reveals.forEach(reveal => {
        revealObserver.observe(reveal);
    });
}

// ===== SMOOTH SCROLL =====
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '#!') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// ===== BACK TO TOP BUTTON =====
function initBackToTop() {
    const backToTopBtn = document.getElementById('back-to-top');
    
    if (backToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTopBtn.classList.remove('hidden');
            } else {
                backToTopBtn.classList.add('hidden');
            }
        });
        
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// ===== COUNTER ANIMATION =====
function initCounterAnimation() {
    const counters = document.querySelectorAll('.counter');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;
        
        const updateCounter = () => {
            current += increment;
            if (current < target) {
                counter.textContent = Math.ceil(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };
        
        // Start animation when element is in view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(counter);
    });
}

// ===== LOADING OVERLAY =====
function showLoading() {
    let overlay = document.getElementById('loading-overlay');
    
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]';
        overlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 flex flex-col items-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4"></div>
                <p class="text-gray-700 font-medium">Loading...</p>
            </div>
        `;
        document.body.appendChild(overlay);
    }
    
    overlay.style.display = 'flex';
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

window.showLoading = showLoading;
window.hideLoading = hideLoading;

// ===== COPY TO CLIPBOARD =====
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text)
            .then(() => showToast('success', 'Copied to clipboard!'))
            .catch(() => fallbackCopyToClipboard(text));
    } else {
        fallbackCopyToClipboard(text);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';
    document.body.appendChild(textArea);
    textArea.select();
    
    try {
        document.execCommand('copy');
        showToast('success', 'Copied to clipboard!');
    } catch (err) {
        showToast('error', 'Failed to copy');
    }
    
    document.body.removeChild(textArea);
}

window.copyToClipboard = copyToClipboard;

// ===== CONFIRM DELETE =====
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

window.confirmDelete = confirmDelete;

// ===== IMAGE PREVIEW =====
function initImagePreview() {
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                // Look for preview element near the input
                let preview = input.parentElement.querySelector('.image-preview');
                
                if (!preview) {
                    preview = document.getElementById('image-preview');
                }
                
                if (preview) {
                    const img = preview.querySelector('img');
                    if (img) {
                        img.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                }
            };
            reader.readAsDataURL(file);
        });
    });
}

// ===== FORM VALIDATION =====
function validateForm(form) {
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('border-red-500');
        } else {
            input.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}

window.validateForm = validateForm;

// ===== LAZY LOADING IMAGES =====
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// ===== INITIALIZE ALL =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Main.js initializing...');
    
    // Add animation styles
    addAnimationStyles();
    
    // Initialize features
    initMobileMenu();
    initScrollReveal();
    initSmoothScroll();
    initBackToTop();
    initCounterAnimation();
    initImagePreview();
    initLazyLoading();
    
    console.log('✅ Main.js initialized successfully!');
    
    // Test notification
    console.log('🧪 Testing notification system...');
});

// ===== GLOBAL ERROR HANDLER =====
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
});

// ===== EXPORT FOR USE IN OTHER SCRIPTS =====
window.MainJS = {
    showToast,
    showNotification: showToast,
    formatCurrency,
    showLoading,
    hideLoading,
    copyToClipboard,
    confirmDelete,
    validateForm
};

console.log('✅ main.js loaded successfully');