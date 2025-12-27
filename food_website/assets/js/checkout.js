/**
 * Checkout Form Handler
 */

document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkoutForm');
    
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            
            // Validate form
            if (!this.checkValidity()) {
                e.preventDefault();
                showNotification('✗ Please fill in all required fields', 'error');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Processing Payment...';
        });
    }
});

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    // Using the same function from cart.js
    if (typeof showNotification !== 'function' || message) {
        console.log(`[${type.toUpperCase()}] ${message}`);
    }
}
