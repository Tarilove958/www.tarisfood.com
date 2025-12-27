/* ===========================
   CART FUNCTIONALITY
   Modern Cart Management
   =========================== */

// ===== CART INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
    initCartFunctionality();
    updateCartUI();
    initCheckoutFlow();
    initAjaxAddToCart();  // Add AJAX form handling
    initAjaxClearCart();  // Add AJAX clear cart handling
    initAjaxRemoveCart();  // Add AJAX remove item handling
    initQuantityChangeHandlers();  // Add real-time quantity update handlers
    
    // Log initialization completion
    console.log('✅ Cart system initialized successfully');
});

// ===== UNIFIED FORM SUBMISSION HANDLER =====
let cartOperationInProgress = {
    addToCart: false,
    removeFromCart: false,
    clearCart: false
};

// ===== AJAX ADD TO CART FORM HANDLER =====
function initAjaxAddToCart() {
    // Use event delegation with single listener
    document.addEventListener('submit', function handleAddToCart(e) {
        const form = e.target;
        
        // Check if this is an add to cart form (multiple ways to detect it)
        const isAddToCartForm = form.action.includes('addToCart.php') || 
                               form.classList.contains('ajax-add-cart') ||
                               form.querySelector('input[name="product_id"]');
        
        if (!isAddToCartForm || form.method.toLowerCase() !== 'post') {
            return; // Let other handlers process this form
        }
        
        // Prevent duplicate submissions
        if (cartOperationInProgress.addToCart) {
            console.warn('⚠️ Add to cart operation already in progress, ignoring duplicate click');
            e.preventDefault();
            return;
        }
        
        e.preventDefault();
        cartOperationInProgress.addToCart = true;
        
        const button = form.querySelector('button[type="submit"]');
        const originalButtonText = button?.textContent;
        
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Adding...';
        }
        
        // Get form data
        const formData = new FormData(form);
        const productId = formData.get('product_id');
        
        if (!productId) {
            console.warn('❌ No product ID found in form');
            cartOperationInProgress.addToCart = false;
            if (button) {
                button.disabled = false;
                button.textContent = originalButtonText;
            }
            return;
        }
        
        // Get product name from the form or DOM
        const productCard = form.closest('.product-card, [class*="product"], .bg-white, .rounded-2xl');
        let productName = 'Product';
        
        if (productCard) {
            const nameEl = productCard.querySelector('h3, h2, [class*="font-bold"]');
            if (nameEl) {
                productName = nameEl.textContent.trim();
            }
        }
        
        // Get the form action
        const action = form.action || 'includes/addToCart.php';
        
        console.log('🛒 Adding to cart:', { productId, productName, action });
        
        // Submit via AJAX
        fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('✅ Add to cart response:', data);
            
            if (data.success) {
                const displayName = data.product_name || productName;
                showNotification(`✓ ${displayName} successfully added to cart!`, 'success');
                
                // Update cart count if provided
                if (data.cart_count) {
                    updateCartCount(data.cart_count);
                    // Ensure badge is visible
                    const badge = document.querySelector('[data-cart-count]');
                    if (badge && data.cart_count > 0) {
                        badge.parentElement.style.display = '';
                        badge.textContent = data.cart_count;
                    }
                }
                
                // Animate the cart icon
                if (typeof animateCartButton === 'function') {
                    animateCartButton();
                }
            } else {
                showNotification(`✗ ${data.message || 'Error adding to cart'}`, 'error');
            }
        })
        .catch(error => {
            console.error('❌ Add to cart error:', error);
            showNotification('✗ Error adding to cart. Please try again.', 'error');
        })
        .finally(() => {
            cartOperationInProgress.addToCart = false;
            if (button) {
                button.disabled = false;
                button.textContent = originalButtonText;
            }
        });
    }, true); // Use capture phase
}

// ===== UPDATE CART COUNT IN HEADER =====
function updateCartCount(count) {
    const cartCountEl = document.querySelector('[data-cart-count]');
    if (cartCountEl) {
        cartCountEl.textContent = count;
    }
}

// ===== AJAX CLEAR CART HANDLER =====
function initAjaxClearCart() {
    document.addEventListener('submit', function handleClearCart(e) {
        const form = e.target;
        
        // Check if this is a clear cart form
        const isClearCartForm = form.classList.contains('ajax-clear-cart') ||
                               form.action.includes('clearCart.php');
        
        if (!isClearCartForm || form.method.toLowerCase() !== 'post') {
            return; // Let other handlers process this form
        }
        
        e.preventDefault();
        
        // Prevent duplicate submissions
        if (cartOperationInProgress.clearCart) {
            console.warn('⚠️ Clear cart operation already in progress, ignoring duplicate click');
            return;
        }
        
        // Confirm action
        if (!confirm('Are you sure you want to clear your entire cart? This action cannot be undone.')) {
            return;
        }
        
        cartOperationInProgress.clearCart = true;
        const button = form.querySelector('button[type="submit"]');
        const originalButtonText = button?.textContent;
        
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Clearing...';
        }
        
        const formData = new FormData(form);
        const action = form.action || 'includes/clearCart.php';
        
        console.log('🗑️ Clearing entire cart...');
        
        // Submit via AJAX
        fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('✅ Clear cart response:', data);
            
            if (data.success) {
                showNotification('✓ Cart cleared successfully!', 'success');
                updateCartCount(0);
                
                // Refresh the cart display after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showNotification(`✗ ${data.message || 'Error clearing cart'}`, 'error');
            }
        })
        .catch(error => {
            console.error('❌ Clear cart error:', error);
            showNotification('✗ Error clearing cart. Please try again.', 'error');
        })
        .finally(() => {
            cartOperationInProgress.clearCart = false;
            if (button) {
                button.disabled = false;
                button.textContent = originalButtonText;
            }
        });
    }, true); // Use capture phase
}

// ===== AJAX REMOVE FROM CART HANDLER =====
function initAjaxRemoveCart() {
    // Use event delegation on document level with single listener
    document.addEventListener('submit', function handleRemoveCart(e) {
        const form = e.target;
        
        // Check if this is a remove cart form
        const isRemoveCartForm = form.classList.contains('ajax-remove-cart') ||
                                form.action.includes('removeCart.php');
        
        if (!isRemoveCartForm || form.method.toLowerCase() !== 'post') {
            return; // Let other handlers process this form
        }
        
        // Prevent duplicate processing
        if (cartOperationInProgress.removeFromCart) {
            console.warn('⚠️ Remove operation already in progress, ignoring duplicate click');
            e.preventDefault();
            return;
        }
        
        e.preventDefault();
        cartOperationInProgress.removeFromCart = true;
        
        const button = form.querySelector('button[type="submit"]');
        const originalButtonText = button?.textContent;
        
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> Removing...';
        }
        
        const cartId = form.querySelector('input[name="cart_id"]')?.value;
        const productId = form.querySelector('input[name="product_id"]')?.value;
        
        if (!cartId && !productId) {
            console.warn('❌ No cart ID or product ID found');
            cartOperationInProgress.removeFromCart = false;
            if (button) {
                button.disabled = false;
                button.textContent = originalButtonText;
            }
            showNotification('Error: Invalid item identifier', 'error');
            return;
        }
        
        // Get the product name from the DOM
        const cartItem = form.closest('div[class*="flex"]');
        let productName = 'Item';
        
        if (cartItem) {
            const nameEl = cartItem.querySelector('h3');
            if (nameEl) {
                productName = nameEl.textContent.trim();
            }
        }
        
        const formData = new FormData(form);
        const action = form.action || 'includes/removeCart.php';
        
        console.log('🗑️ Removing item from cart:', { cartId, productId, productName });
        
        // Submit via AJAX
        fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('✅ Remove cart response:', data);
            
            if (data.success) {
                // Only show ONE success message
                showNotification(`✓ ${productName} removed from cart`, 'success');
                
                // Animate out the cart item
                if (cartItem) {
                    cartItem.style.animation = 'fadeOut 0.3s ease-out forwards';
                    setTimeout(() => {
                        cartItem.remove();
                    }, 300);
                }
                
                // Update cart UI
                updateCartCount(data.cart_count || 0);
                updateCartSummary();
                
                // If cart is now empty, refresh the page to show empty state
                if (data.cart_count === 0) {
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            } else {
                // Only show ONE error message
                showNotification(`✗ Failed to remove item: ${data.message || 'Unknown error'}`, 'error');
            }
        })
        .catch(error => {
            console.error('❌ Remove cart error:', error);
            showNotification(`✗ Error removing item. Please try again.`, 'error');
        })
        .finally(() => {
            cartOperationInProgress.removeFromCart = false;
            if (button) {
                button.disabled = false;
                button.textContent = originalButtonText;
            }
        });
    }, true); // Use capture phase to ensure we catch it first
}
function initQuantityChangeHandlers() {
    // Attach event listeners to all quantity input fields
    document.querySelectorAll('.cart-qty-input').forEach(input => {
        input.addEventListener('change', function() {
            handleQuantityChange(this);
        });
        
        input.addEventListener('input', function() {
            // Real-time total update as user types
            const row = this.closest('tr, div[class*="flex"]');
            if (row) {
                const quantity = parseInt(this.value) || 1;
                if (quantity < 1) {
                    this.value = 1;
                    return;
                }
                updateItemRowTotal(row, quantity);
                updateCartSummary();
            }
        });
    });
}

function handleQuantityChange(input) {
    const quantity = parseInt(input.value) || 1;
    
    // Validate quantity
    if (quantity < 1) {
        input.value = 1;
        return;
    }
    
    // Get the cart item ID from data attribute
    const row = input.closest('tr, div[class*="flex"]');
    if (!row) return;
    
    const cartId = input.dataset.cartId || row.dataset.cartId;
    
    if (!cartId) {
        console.warn('No cart ID found for quantity input');
        return;
    }
    
    // Update item row total
    updateItemRowTotal(row, quantity);
    
    // Update cart summary totals
    updateCartSummary();
    
    // Optional: Send to server to persist (uncomment if you have an updateCart.php endpoint)
    // updateCartQuantityOnServer(cartId, quantity);
    
    showNotification('Cart updated', 'info');
}

function updateItemRowTotal(row, quantity) {
    // Find the price element in this row
    const priceElement = row.querySelector('[data-item-price]');
    if (!priceElement) return;
    
    const price = parseFloat(priceElement.dataset.itemPrice) || 0;
    const total = price * quantity;
    
    // Find and update the subtotal element
    const subtotalElement = row.querySelector('[data-item-subtotal]');
    if (subtotalElement) {
        subtotalElement.textContent = formatCurrency(total);
        subtotalElement.dataset.itemTotal = total;
    }
}

function updateCartSummary() {
    // Get all cart rows
    const cartRows = document.querySelectorAll('[data-item-subtotal]');
    if (cartRows.length === 0) return;
    
    // Calculate new subtotal
    let newSubtotal = 0;
    cartRows.forEach(element => {
        const itemTotal = parseFloat(element.dataset.itemTotal) || 0;
        newSubtotal += itemTotal;
    });
    
    // Update subtotal display
    const subtotalDisplay = document.querySelector('[data-cart-subtotal]');
    if (subtotalDisplay) {
        subtotalDisplay.textContent = formatCurrency(newSubtotal);
    }
    
    // Update total display (same as subtotal, delivery fee calculated at checkout)
    const totalDisplay = document.querySelector('[data-cart-total]');
    if (totalDisplay) {
        totalDisplay.textContent = formatCurrency(newSubtotal);
    }
}

function updateCartQuantityOnServer(cartId, quantity) {
    // Optional: If you have a dedicated updateCart.php endpoint
    const formData = new FormData();
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);
    formData.append('csrf_token', document.querySelector('[name="csrf_token"]')?.value);
    
    fetch('includes/updateCart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.warn('Server error updating cart:', data.message);
            showNotification('Error updating cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating cart on server:', error);
    });
}

// ===== ADD TO CART =====
window.addToCart = function(productId, productName, price, image) {
    const quantity = parseInt(document.querySelector(`#qty-${productId}`)?.value) || 1;

    const cartItem = {
        id: productId,
        name: productName,
        price: parseFloat(price),
        image: image,
        quantity: quantity,
        timestamp: Date.now()
    };

    // Get existing cart
    let cart = getCart();

    // Check if item exists
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += quantity;
        showNotification(`${productName} quantity updated!`, 'info');
    } else {
        cart.push(cartItem);
        showNotification(`${productName} added to cart!`, 'success');
    }

    // Save cart
    saveCart(cart);
    updateCartUI();

    // Animate cart button
    animateCartButton();
};

// ===== REMOVE FROM CART =====
window.removeFromCart = function(productId) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== productId);
    saveCart(cart);
    updateCartUI();
    showNotification('Item removed from cart', 'info');
};

// ===== UPDATE CART QUANTITY =====
window.updateCartQuantity = function(productId, quantity) {
    let cart = getCart();
    const item = cart.find(i => i.id === productId);

    if (item) {
        if (quantity <= 0) {
            removeFromCart(productId);
        } else {
            item.quantity = parseInt(quantity);
            saveCart(cart);
            updateCartUI();
        }
    }
};

// ===== GET CART =====
function getCart() {
    const cart = localStorage.getItem('cart');
    return cart ? JSON.parse(cart) : [];
}

// ===== SAVE CART =====
function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// ===== CLEAR CART =====
window.clearCart = function() {
    if (confirm('Are you sure you want to clear your cart?')) {
        localStorage.removeItem('cart');
        updateCartUI();
        showNotification('Cart cleared', 'info');
    }
};

// ===== CALCULATE TOTALS =====
function calculateCartTotals() {
    const cart = getCart();
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const tax = subtotal * 0.1; // 10% tax
    const shipping = subtotal > 50 ? 0 : 5; // Free shipping over $50
    const total = subtotal + tax + shipping;

    return {
        subtotal: subtotal,
        tax: tax,
        shipping: shipping,
        total: total,
        itemCount: cart.length,
        itemQuantity: cart.reduce((sum, item) => sum + item.quantity, 0)
    };
}

// ===== UPDATE CART UI =====
function updateCartUI() {
    const cart = getCart();
    const totals = calculateCartTotals();

    // Update cart count in header
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(el => {
        el.textContent = totals.itemQuantity;
        if (totals.itemQuantity > 0) {
            el.style.display = 'flex';
        } else {
            el.style.display = 'none';
        }
    });

    // Update cart items display
    const cartItemsContainer = document.querySelector('.cart-items-container');
    const cartEmptyState = document.querySelector('.cart-empty-state');
    const cartSummary = document.querySelector('.cart-summary');

    if (cart.length === 0) {
        if (cartItemsContainer) {
            cartItemsContainer.innerHTML = '';
        }
        if (cartEmptyState) {
            cartEmptyState.style.display = 'block';
        }
        if (cartSummary) {
            cartSummary.style.display = 'none';
        }
        return;
    }

    if (cartEmptyState) {
        cartEmptyState.style.display = 'none';
    }
    if (cartSummary) {
        cartSummary.style.display = 'block';
    }

    // Render cart items
    if (cartItemsContainer) {
        cartItemsContainer.innerHTML = cart.map(item => `
            <div class="cart-item" data-product-id="${item.id}">
                <div class="cart-item-image">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="cart-item-details">
                    <h3 class="cart-item-name">${item.name}</h3>
                    <p class="cart-item-price">${formatCurrency(item.price)}</p>
                </div>
                <div class="cart-item-quantity">
                    <button type="button" onclick="updateCartQuantity(${item.id}, ${item.quantity - 1})" class="qty-btn">
                        <i class="bi bi-dash"></i>
                    </button>
                    <input type="number" min="1" value="${item.quantity}" onchange="updateCartQuantity(${item.id}, this.value)" class="qty-input">
                    <button type="button" onclick="updateCartQuantity(${item.id}, ${item.quantity + 1})" class="qty-btn">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <div class="cart-item-subtotal">
                    <p>${formatCurrency(item.price * item.quantity)}</p>
                </div>
                <button type="button" onclick="removeFromCart(${item.id})" class="cart-item-remove" title="Remove item">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `).join('');
    }

    // Update totals
    updateCartTotals(totals);
}

// ===== UPDATE TOTALS DISPLAY =====
function updateCartTotals(totals) {
    const subtotalEl = document.querySelector('[data-total="subtotal"]');
    const taxEl = document.querySelector('[data-total="tax"]');
    const shippingEl = document.querySelector('[data-total="shipping"]');
    const totalEl = document.querySelector('[data-total="total"]');

    if (subtotalEl) subtotalEl.textContent = formatCurrency(totals.subtotal);
    if (taxEl) taxEl.textContent = formatCurrency(totals.tax);
    if (shippingEl) {
        shippingEl.textContent = totals.shipping === 0 ? 'FREE' : formatCurrency(totals.shipping);
    }
    if (totalEl) totalEl.textContent = formatCurrency(totals.total);
}

// ===== INIT CART FUNCTIONALITY =====
function initCartFunctionality() {
    // Add to cart buttons
    const addToCartBtns = document.querySelectorAll('[data-add-to-cart]');
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const price = this.getAttribute('data-price');
            const image = this.getAttribute('data-image');

            addToCart(productId, productName, price, image);
        });
    });

    // Quantity input handlers
    const qtyInputs = document.querySelectorAll('.qty-input');
    qtyInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.closest('[data-product-id]').getAttribute('data-product-id');
            updateCartQuantity(productId, this.value);
        });
    });
}

// ===== CHECKOUT FLOW =====
function initCheckoutFlow() {
    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            const cart = getCart();
            if (cart.length === 0) {
                showNotification('Your cart is empty', 'warning');
                return;
            }
            proceedToCheckout();
        });
    }

    // Coupon code
    const applyCouponBtn = document.querySelector('.apply-coupon-btn');
    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', function() {
            applyCoupon();
        });
    }
}

// ===== APPLY COUPON =====
window.applyCoupon = function() {
    const couponInput = document.querySelector('.coupon-input');
    const couponCode = couponInput?.value.toUpperCase();

    if (!couponCode) {
        showNotification('Please enter a coupon code', 'warning');
        return;
    }

    // Sample coupon codes
    const coupons = {
        'WELCOME10': 10,
        'SAVE20': 20,
        'FOODLOVER15': 15,
        'FRIEND50': 50
    };

    if (coupons[couponCode]) {
        const discount = coupons[couponCode];
        localStorage.setItem('coupon', JSON.stringify({
            code: couponCode,
            discount: discount
        }));
        showNotification(`Coupon applied! ${discount}% discount`, 'success');
        couponInput.value = '';
        updateCartUI();
    } else {
        showNotification('Invalid coupon code', 'error');
    }
};

// ===== PROCEED TO CHECKOUT =====
function proceedToCheckout() {
    const cart = getCart();
    if (cart.length > 0) {
        window.location.href = 'checkout.php';
    }
}

// ===== ANIMATE CART BUTTON =====
function animateCartButton() {
    const cartBtn = document.querySelector('.cart-icon');
    if (cartBtn) {
        cartBtn.style.animation = 'bounce 0.5s ease';
        setTimeout(() => {
            cartBtn.style.animation = '';
        }, 500);
    }
}

// ===== CART PERSISTENCE =====
window.addEventListener('beforeunload', function() {
    const cart = getCart();
    if (cart.length > 0) {
        localStorage.setItem('cartBackup', JSON.stringify(cart));
    }
});

// ===== MINI CART PREVIEW =====
window.showMiniCart = function() {
    const cart = getCart();
    const totals = calculateCartTotals();

    let html = '<div class="mini-cart">';
    
    if (cart.length === 0) {
        html += '<p class="mini-cart-empty">Your cart is empty</p>';
    } else {
        html += '<div class="mini-cart-items">';
        cart.slice(0, 3).forEach(item => {
            html += `
                <div class="mini-cart-item">
                    <span>${item.name} x${item.quantity}</span>
                    <span>${formatCurrency(item.price * item.quantity)}</span>
                </div>
            `;
        });
        if (cart.length > 3) {
            html += `<p class="mini-cart-more">+${cart.length - 3} more items</p>`;
        }
        html += '</div>';
        html += `<div class="mini-cart-total">Total: ${formatCurrency(totals.total)}</div>`;
        html += '<button class="btn btn-primary" onclick="window.location.href=\'cart.php\'">View Cart</button>';
    }

    html += '</div>';

    const miniCart = document.querySelector('.mini-cart-container');
    if (miniCart) {
        miniCart.innerHTML = html;
    }
};

// ===== SAVE FOR LATER =====
window.saveForLater = function(productId) {
    let saved = JSON.parse(localStorage.getItem('savedItems')) || [];
    if (!saved.includes(productId)) {
        saved.push(productId);
        localStorage.setItem('savedItems', JSON.stringify(saved));
        showNotification('Item saved for later', 'success');
    }
};

// ===== MOVE TO CART FROM SAVED =====
window.moveToCart = function(productId, name, price, image) {
    addToCart(productId, name, price, image);
    removeSavedItem(productId);
};

// ===== REMOVE SAVED ITEM =====
window.removeSavedItem = function(productId) {
    let saved = JSON.parse(localStorage.getItem('savedItems')) || [];
    saved = saved.filter(id => id !== productId);
    localStorage.setItem('savedItems', JSON.stringify(saved));
};

// ===== EXPORT CART SUMMARY =====
window.exportCartSummary = function() {
    const cart = getCart();
    const totals = calculateCartTotals();

    let summary = 'CART SUMMARY\n\n';
    summary += '======================\n';
    
    cart.forEach(item => {
        summary += `${item.name} x${item.quantity}: ${formatCurrency(item.price * item.quantity)}\n`;
    });

    summary += '======================\n';
    summary += `Subtotal: ${formatCurrency(totals.subtotal)}\n`;
    summary += `Tax: ${formatCurrency(totals.tax)}\n`;
    summary += `Shipping: ${totals.shipping === 0 ? 'FREE' : formatCurrency(totals.shipping)}\n`;
    summary += `TOTAL: ${formatCurrency(totals.total)}\n`;

    copyToClipboard(summary);
};

// ===== HELPER: FORMAT CURRENCY =====
function formatCurrency(amount) {
    return '₦' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// ===== HELPER: SHOW NOTIFICATION =====
function showNotification(message, type = 'info') {
    // Check if a notification function exists in main.js
    if (typeof window.showToast === 'function') {
        window.showToast(type, message);
        return;
    }
    
    // Fallback: Create simple notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#10B981' : type === 'error' ? '#DC3545' : '#0066CC'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ===== HELPER: COPY TO CLIPBOARD =====
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Copied to clipboard!', 'success');
        }).catch(() => {
            fallbackCopyToClipboard(text);
        });
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
        showNotification('Copied to clipboard!', 'success');
    } catch (err) {
        showNotification('Failed to copy', 'error');
    }
    
    document.body.removeChild(textArea);
}