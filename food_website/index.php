<?php
require_once 'includes/config.php';
$page_title = 'Home';
include 'includes/header.php';
?>

<!-- Hero Section with Modern Gradient Background -->
<section class="relative py-20 md:py-32 overflow-hidden bg-gradient-to-br from-blue-50 via-white to-orange-50">
    <!-- Animated Background Elements -->
    <div class="absolute top-10 left-10 w-72 h-72 bg-primary/10 rounded-full mix-blend-multiply filter blur-3xl animate-blob"></div>
    <div class="absolute top-40 right-10 w-72 h-72 bg-secondary/10 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-20 left-1/2 w-72 h-72 bg-accent/10 rounded-full mix-blend-multiply filter blur-3xl animate-blob animation-delay-4000"></div>
    
    <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center relative z-10">
        <div class="relative z-10 animate-fade-in">
            <span class="inline-block px-4 py-2 rounded-full bg-gradient-to-r from-blue-100 to-blue-50 text-primary text-xs font-bold uppercase tracking-wider mb-6 border border-blue-200">
                <i class="bi bi-bicycle me-2"></i> Fast Delivery in 30 Mins
            </span>
            <h1 class="font-bricolage font-extrabold text-5xl md:text-6xl lg:text-7xl leading-tight mb-6 text-dark">
                Delicious <span class="gradient-text bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Food</span><br>
                <span class="text-primary">Delivered</span> Hot.
            </h1>
            <p class="font-outfit text-lg text-gray-600 mb-8 max-w-xl leading-relaxed">
                Experience the best flavors from FoodHub. Fresh ingredients, masterful chefs, and lightning-fast delivery that keeps your food piping hot.
            </p>
            <div class="flex flex-col sm:flex-row flex-wrap gap-4">
                <a href="menu.php" class="px-8 py-4 bg-gradient-to-r from-primary to-blue-600 text-white rounded-full font-bold hover:shadow-xl hover:-translate-y-1 transition-all duration-300 shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                    Order Now <i class="bi bi-arrow-right"></i>
                </a>
                <a href="about.php" class="px-8 py-4 border-2 border-primary text-primary rounded-full font-bold hover:bg-primary hover:text-white transition-all duration-300 flex items-center justify-center gap-2">
                    Learn More <i class="bi bi-info-circle"></i>
                </a>
            </div>
            
            <!-- Trust Badges -->
            <div class="flex flex-wrap gap-8 mt-12 pt-8 border-t border-gray-200">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="bi bi-check2 text-green-600 font-bold text-lg"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">100% Fresh</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="bi bi-lightning text-orange-600 font-bold text-lg"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Quick Delivery</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="bi bi-award text-purple-600 font-bold text-lg"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">Quality Assured</span>
                </div>
            </div>
        </div>
        
        <!-- Hero Image with Modern Styling -->
        <div class="relative h-96 md:h-[500px] animate-fade-in animation-delay-200">
            <!-- Decorative floating elements -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-3xl transform rotate-45"></div>
            <div class="absolute -bottom-5 -left-5 w-40 h-40 bg-gradient-to-tr from-accent/20 to-transparent rounded-full"></div>
            
            <!-- Image Container with shadow -->
            <div class="relative h-full rounded-3xl overflow-hidden shadow-2xl hover:shadow-3xl transition-shadow duration-500 transform hover:-translate-y-2">
                <img src="https://i.pinimg.com/736x/6c/bf/b0/6cbfb01ec22b9ba66bff84315a5bafbd.jpg" 
                     alt="Delicious Food" 
                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                     loading="lazy">
                
                <!-- Gradient overlay for better text contrast if needed -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                
                <!-- Floating badge -->
                <div class="absolute bottom-6 left-6 bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-lg transform hover:-translate-y-1 transition-transform">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="bi bi-star-fill text-yellow-400"></i>
                        <span class="font-bold text-gray-900">4.8/5</span>
                    </div>
                    <p class="text-xs text-gray-600">2,000+ Reviews</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-gradient-to-b from-white to-light">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-2 rounded-full bg-blue-100 text-primary text-xs font-bold uppercase tracking-wider mb-4">
                <i class="bi bi-boxes me-2"></i> Shop by Category
            </span>
            <h2 class="font-bricolage font-bold text-3xl md:text-4xl text-dark">Browse Our Categories</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php
            // Fetch categories (Limit 4)
            $stmt = $pdo->query("SELECT * FROM categories WHERE status='active' LIMIT 4");
            while($cat = $stmt->fetch()):
            ?>
            <a href="menu.php?category=<?php echo $cat['category_id']; ?>" class="group bg-white p-8 rounded-2xl shadow-md border border-gray-100 text-center hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-blue-100 to-blue-50 rounded-2xl flex items-center justify-center mb-4 group-hover:from-primary group-hover:to-blue-600 transition-all duration-300 shadow-lg group-hover:shadow-xl">
                    <i class="bi bi-basket text-4xl text-primary group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="font-bold text-lg text-dark group-hover:text-primary transition-colors"><?php echo $cat['category_name']; ?></h3>
                <p class="text-xs text-gray-500 mt-2">Browse all</p>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="font-bricolage font-bold text-3xl md:text-4xl text-dark mb-2">Popular Dishes</h2>
                <p class="text-gray-500">Our customer favorites this week</p>
            </div>
            <a href="menu.php" class="text-primary font-semibold hover:underline flex items-center gap-2">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <?php
            // Fetch Featured Products
            $stmt = $pdo->query("SELECT * FROM products WHERE is_featured=1 AND status='available' LIMIT 3");
            while($product = $stmt->fetch()):
            ?>
            <div class="bg-white border border-gray-100 rounded-3xl overflow-hidden hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group popular-dish-card" data-product-id="<?php echo $product['product_id']; ?>" data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>" data-product-price="<?php echo $product['discount_price'] ?? $product['price']; ?>">
                <div class="relative h-56 bg-gradient-to-br from-gray-100 to-gray-50 overflow-hidden">
                    <?php if($product['image']): ?>
                        <?php 
                        $imageUrl = (!empty($product['image']) && strpos($product['image'], 'http') === 0) 
                            ? $product['image'] 
                            : 'assets/images/products/' . $product['image'];
                        ?>
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <i class="bi bi-image text-6xl"></i>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Badge -->
                    <span class="absolute top-4 right-4 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg flex items-center gap-1">
                        <i class="bi bi-star-fill"></i> 4.5
                    </span>
                    
                    <!-- Hover overlay with action buttons -->
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-3">
                        <!-- Quick View Button -->
                        <button class="popular-quick-view-btn bg-white text-primary px-6 py-2 rounded-full font-bold hover:bg-primary hover:text-white transition-all transform hover:scale-110 shadow-lg" title="Quick View">
                            <i class="bi bi-eye me-2"></i>Quick View
                        </button>
                        
                        <!-- Like Button -->
                        <button class="popular-like-btn w-12 h-12 rounded-full bg-white text-gray-600 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all transform hover:scale-110 shadow-lg" title="Like">
                            <i class="bi bi-heart text-lg"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="font-bold text-xl text-dark group-hover:text-primary transition-colors"><?php echo $product['product_name']; ?></h3>
                        <span class="font-bricolage font-bold text-lg text-secondary whitespace-nowrap ml-2"><?php echo formatPrice($product['price']); ?></span>
                    </div>
                    <p class="text-gray-500 text-sm mb-4 line-clamp-2 h-10"><?php echo $product['description']; ?></p>
                    
                    <!-- Rating and Stats -->
                    <div class="flex items-center gap-2 mb-4 text-xs text-gray-500">
                        <span><i class="bi bi-bag-check text-green-500"></i> 150+ Sold</span>
                        <span class="text-gray-300">|</span>
                        <span><i class="bi bi-lightning text-orange-500"></i> 30 min</span>
                    </div>
                    
                    <form action="includes/addToCart.php" method="POST" class="flex gap-2">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <?php echo getCSRFInput(); ?>
                        
                        <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-dark to-gray-800 text-white font-bold hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2 group/btn">
                            <i class="bi bi-bag-plus group-hover/btn:scale-125 transition-transform"></i> Add Cart
                        </button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Floating Contact Icons with Enhanced Design -->
<div id="floating-icons" class="fixed bottom-8 right-8 z-40 flex flex-col gap-4">
    <!-- WhatsApp -->
    <a href="https://wa.me/1234567890?text=Hello%20FoodHub" 
       target="_blank" 
       rel="noopener noreferrer"
       class="floating-icon w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 hover:shadow-3xl transition-all duration-300 group"
       title="Chat on WhatsApp">
        <i class="bi bi-whatsapp text-2xl group-hover:animate-bounce"></i>
        <span class="absolute right-20 bg-dark text-white px-3 py-1 rounded-lg text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">WhatsApp Us</span>
    </a>
    
    <!-- Phone Call -->
    <a href="tel:+1234567890"
       class="floating-icon w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 hover:shadow-3xl transition-all duration-300 group"
       title="Call us">
        <i class="bi bi-telephone-fill text-2xl group-hover:animate-bounce"></i>
        <span class="absolute right-20 bg-dark text-white px-3 py-1 rounded-lg text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Call Us</span>
    </a>
    
    <!-- Email -->
    <a href="mailto:hello@foodhub.com"
       class="floating-icon w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 hover:shadow-3xl transition-all duration-300 group"
       title="Email us">
        <i class="bi bi-envelope text-2xl group-hover:animate-bounce"></i>
        <span class="absolute right-20 bg-dark text-white px-3 py-1 rounded-lg text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Email Us</span>
    </a>
    
    <!-- Chat/Support -->
    <a href="<?php echo isLoggedIn() ? 'user/index.php' : 'login.php'; ?>"
       class="floating-icon w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-700 text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 hover:shadow-3xl transition-all duration-300 group"
       title="Customer Support">
        <i class="bi bi-chat-dots text-2xl group-hover:animate-bounce"></i>
        <span class="absolute right-20 bg-dark text-white px-3 py-1 rounded-lg text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Support</span>
    </a>
</div>

<!-- Call-to-Action Banner Section -->
<section class="py-20 bg-gradient-to-r from-primary via-blue-600 to-secondary relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-40 h-40 bg-white rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
        <div class="absolute top-0 right-0 w-40 h-40 bg-white rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
    </div>
    
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <h2 class="font-bricolage font-bold text-4xl md:text-5xl text-white mb-6">
            <i class="bi bi-lightning-fill text-yellow-300 animate-pulse"></i> Ready to Order?
        </h2>
        <p class="text-xl text-blue-50 mb-8 max-w-2xl mx-auto">
            Join thousands of happy customers enjoying delicious food delivered fresh to your doorstep in minutes!
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="menu.php" class="px-10 py-4 bg-white text-primary rounded-full font-bold hover:bg-gray-100 transition-all hover:-translate-y-1 shadow-xl flex items-center justify-center gap-2">
                <i class="bi bi-bag-plus-fill"></i> Order Now
            </a>
            <a href="contact.php" class="px-10 py-4 border-2 border-white text-white rounded-full font-bold hover:bg-white hover:text-primary transition-all hover:-translate-y-1 flex items-center justify-center gap-2">
                <i class="bi bi-chat-left-dots"></i> Contact Us
            </a>
        </div>
    </div>
</section>

<script>
// Popular Dishes Interactive Features
document.querySelectorAll('.popular-dish-card').forEach(card => {
    const quickViewBtn = card.querySelector('.popular-quick-view-btn');
    const likeBtn = card.querySelector('.popular-like-btn');
    
    // Quick View Button - Works on desktop and mobile
    if (quickViewBtn) {
        quickViewBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            e.preventDefault();
            openPopularDishModal(card);
        });
    }
    
    // Like/Love Button - Works on desktop and mobile
    if (likeBtn) {
        likeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            e.preventDefault();
            toggleDishLike(likeBtn, card.dataset.productId, card.dataset.productName);
        });
    }
});

// Popular Dish Quick View Modal
function openPopularDishModal(card) {
    const productId = card.dataset.productId;
    const productName = card.dataset.productName;
    const productPrice = parseFloat(card.dataset.productPrice);
    
    // Get image from card
    const img = card.querySelector('img');
    const imageSrc = img ? img.src : '';
    
    // Get description
    const description = card.querySelector('p').textContent;
    
    // Check if modal exists, if not create it
    let modal = document.getElementById('popularDishModal');
    if (!modal) {
        modal = createPopularDishModal();
        document.body.appendChild(modal);
    }
    
    // Set modal data
    document.getElementById('popularModalProductName').textContent = productName;
    document.getElementById('popularModalProductImage').src = imageSrc;
    document.getElementById('popularModalProductImage').alt = productName;
    document.getElementById('popularModalProductPrice').textContent = formatPrice(productPrice);
    document.getElementById('popularModalProductDescription').textContent = description;
    
    // Store current product
    window.currentPopularProduct = {
        id: productId,
        name: productName,
        price: productPrice
    };
    
    // Show modal
    modal.classList.remove('hidden');
}

// Create Popular Dish Modal
function createPopularDishModal() {
    const modal = document.createElement('div');
    modal.id = 'popularDishModal';
    modal.className = 'hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm';
    modal.innerHTML = `
        <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex items-center justify-between z-10">
                <h2 id="popularModalProductName" class="font-bricolage font-bold text-2xl text-dark">Product</h2>
                <button id="closePopularModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="bi bi-x-lg text-2xl"></i>
                </button>
            </div>
            
            <div class="p-6">
                <div class="mb-6 rounded-2xl overflow-hidden bg-gray-100 h-80">
                    <img id="popularModalProductImage" src="" alt="" class="w-full h-full object-cover">
                </div>
                
                <div class="space-y-4 mb-6">
                    <p id="popularModalProductPrice" class="text-3xl font-bold text-secondary font-bricolage">₦0</p>
                    <p id="popularModalProductDescription" class="text-gray-600 leading-relaxed"></p>
                </div>
                
                <div class="mb-6 bg-gray-50 rounded-2xl p-6">
                    <label class="block text-sm font-semibold text-dark mb-4">Select Quantity</label>
                    <div class="flex items-center gap-6 justify-center">
                        <button id="popularQuantityMinus" class="w-12 h-12 rounded-full border-2 border-gray-300 flex items-center justify-center text-xl font-bold text-gray-700 hover:border-primary hover:text-primary transition-all hover:scale-110">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                        <input type="number" id="popularQuantityInput" min="1" value="1" class="w-20 text-center text-2xl font-bold text-dark border-2 border-gray-300 rounded-lg focus:border-primary focus:outline-none">
                        <button id="popularQuantityPlus" class="w-12 h-12 rounded-full border-2 border-gray-300 flex items-center justify-center text-xl font-bold text-gray-700 hover:border-primary hover:text-primary transition-all hover:scale-110">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-6 bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Total:</span>
                        <p id="popularTotalPrice" class="text-3xl font-bold text-secondary">₦0</p>
                    </div>
                </div>
                
                <button id="popularConfirmAddToCart" class="w-full py-4 rounded-xl bg-gradient-to-r from-primary to-blue-600 text-white font-bold hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2 text-lg">
                    <i class="bi bi-bag-plus-fill"></i> Add to Cart
                </button>
            </div>
        </div>
    `;
    
    // Add event listeners
    document.getElementById('closePopularModal').addEventListener('click', closePopularDishModal);
    
    const quantityInput = document.getElementById('popularQuantityInput');
    document.getElementById('popularQuantityMinus').addEventListener('click', () => {
        const val = Math.max(1, parseInt(quantityInput.value) - 1);
        quantityInput.value = val;
        updatePopularTotalPrice(window.currentPopularProduct.price);
    });
    
    document.getElementById('popularQuantityPlus').addEventListener('click', () => {
        quantityInput.value = parseInt(quantityInput.value) + 1;
        updatePopularTotalPrice(window.currentPopularProduct.price);
    });
    
    quantityInput.addEventListener('change', () => {
        if (quantityInput.value < 1) quantityInput.value = 1;
        updatePopularTotalPrice(window.currentPopularProduct.price);
    });
    
    document.getElementById('popularConfirmAddToCart').addEventListener('click', () => {
        const product = window.currentPopularProduct;
        const quantity = parseInt(document.getElementById('popularQuantityInput').value) || 1;
        
        fetch('/food_website/includes/addToCart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'product_id': product.id,
                'quantity': quantity,
                'csrf_token': document.querySelector('[name="csrf_token"]')?.value || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPopularNotification(`✓ Added ${quantity}x ${product.name} to cart!`, 'success');
                closePopularDishModal();
            } else {
                showPopularNotification(`✗ ${data.message || 'Error adding to cart'}`, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showPopularNotification('Error adding to cart', 'error');
        });
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target.id === 'popularDishModal') {
            closePopularDishModal();
        }
    });
    
    return modal;
}

function closePopularDishModal() {
    const modal = document.getElementById('popularDishModal');
    if (modal) modal.classList.add('hidden');
}

function updatePopularTotalPrice(price) {
    const quantity = parseInt(document.getElementById('popularQuantityInput').value) || 1;
    const total = price * quantity;
    document.getElementById('popularTotalPrice').textContent = formatPrice(total);
}

// Like/Favorite functionality for popular dishes
function toggleDishLike(btn, productId, productName) {
    const icon = btn.querySelector('i');
    const isLiked = btn.classList.contains('liked');
    
    if (isLiked) {
        btn.classList.remove('liked', 'bg-red-500', 'text-white');
        btn.classList.add('text-gray-600');
        icon.classList.remove('bi-heart-fill');
        icon.classList.add('bi-heart');
        removeFavoriteDish(productId);
        showPopularNotification('Removed from favorites', 'info');
    } else {
        btn.classList.add('liked', 'bg-red-500', 'text-white');
        btn.classList.remove('text-gray-600');
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill');
        addFavoriteDish(productId, productName);
        showPopularNotification('Added to favorites ❤️', 'success');
    }
}

function addFavoriteDish(productId, productName) {
    let favorites = JSON.parse(localStorage.getItem('favorites') || '{}');
    favorites[productId] = {
        id: productId,
        name: productName,
        addedAt: new Date().toISOString()
    };
    localStorage.setItem('favorites', JSON.stringify(favorites));
}

function removeFavoriteDish(productId) {
    let favorites = JSON.parse(localStorage.getItem('favorites') || '{}');
    delete favorites[productId];
    localStorage.setItem('favorites', JSON.stringify(favorites));
}

function loadDishFavoritesState() {
    const favorites = JSON.parse(localStorage.getItem('favorites') || '{}');
    
    document.querySelectorAll('.popular-dish-card').forEach(card => {
        const productId = card.dataset.productId;
        const likeBtn = card.querySelector('.popular-like-btn');
        
        if (likeBtn && favorites[productId]) {
            const icon = likeBtn.querySelector('i');
            likeBtn.classList.add('liked', 'bg-red-500', 'text-white');
            likeBtn.classList.remove('text-gray-600');
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill');
        }
    });
}

function showPopularNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white font-semibold shadow-lg z-50 animate-pulse ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

function formatPrice(amount) {
    return '₦' + amount.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Initialize favorites on page load
document.addEventListener('DOMContentLoaded', () => {
    loadDishFavoritesState();
});
</script>

<?php include 'includes/footer.php'; ?>