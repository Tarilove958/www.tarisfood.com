<?php
require_once 'includes/config.php';
$page_title = 'Our Menu';
include 'includes/header.php';

// Filter logic
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['q']) ? sanitize($_GET['q']) : '';

$query = "SELECT * FROM products WHERE status = 'available'";
$params = [];

if ($category_id) {
    $query .= " AND category_id = ?";
    $params[] = $category_id;
}
if ($search) {
    $query .= " AND product_name LIKE ?";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="bg-gradient-to-r from-primary via-blue-600 to-primary py-16 relative overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute inset-0 bg-black/5"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/10 rounded-full blur-3xl translate-y-1/2"></div>
    
    <div class="max-w-6xl mx-auto px-4 relative z-10">
        <!-- Greeting Card -->
        <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8 mb-8">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Left: Greeting Message -->
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-4xl" id="greetingEmoji">🌞</span>
                        <h2 id="greetingTitle" class="text-3xl font-bold text-dark">Good Morning</h2>
                    </div>
                    <p class="text-gray-600 text-lg mb-6">Welcome, <span id="userName" class="font-semibold text-primary">Guest</span>!</p>
                    
                    <!-- Location Display -->
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="bi bi-geo-alt-fill text-red-500"></i>
                        <div>
                            <p class="text-sm font-medium">Your Location</p>
                            <p id="locationDisplay" class="text-gray-600 font-semibold">Loading location...</p>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Current Time and Stats -->
                <div class="flex flex-col justify-between">
                    <!-- Time Display -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200">
                        <p class="text-sm text-gray-600 mb-2">Current Time</p>
                        <p id="currentTime" class="text-2xl font-bold text-primary">00:00:00</p>
                        <p id="currentDate" class="text-gray-600 text-sm mt-2">Loading date...</p>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="flex gap-4">
                        <div class="flex-1 bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                            <p class="text-2xl font-bold text-green-600">Fresh</p>
                            <p class="text-xs text-gray-600">Daily Prepared</p>
                        </div>
                        <div class="flex-1 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 border border-orange-200">
                            <p class="text-2xl font-bold text-orange-600">Local</p>
                            <p class="text-xs text-gray-600">Nigerian Foods</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Heading -->
        <div class="text-center mb-8">
            <h1 class="font-bricolage font-bold text-4xl text-white mb-3">Our Menu</h1>
            <p class="text-blue-100 text-lg">Explore our delicious offerings</p>
        </div>
    </div>
</div>

<section class="py-12 px-4 max-w-6xl mx-auto min-h-screen">
    <div class="flex flex-col md:flex-row justify-between gap-4 mb-8">
        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
            <a href="menu.php" class="px-5 py-2 rounded-full text-sm font-semibold transition-all <?php echo !$category_id ? 'bg-dark text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-primary hover:text-primary'; ?>">All</a>
            <?php
            $cats = $pdo->query("SELECT * FROM categories WHERE status='active'");
            while($c = $cats->fetch()):
            ?>
            <a href="menu.php?category=<?php echo $c['category_id']; ?>" class="px-5 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-all <?php echo $category_id == $c['category_id'] ? 'bg-dark text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-primary hover:text-primary'; ?>">
                <?php echo $c['category_name']; ?>
            </a>
            <?php endwhile; ?>
        </div>
        <form class="relative group">
            <input type="text" name="q" placeholder="Search food..." value="<?php echo $search; ?>" class="pl-10 pr-4 py-2 border border-gray-200 rounded-full focus:outline-none focus:border-primary focus:ring-2 focus:ring-blue-100 w-full md:w-64 transition-all">
            <i class="bi bi-search absolute left-3 top-2.5 text-gray-400 group-focus-within:text-primary"></i>
        </form>
    </div>

    <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all group hover:-translate-y-1 duration-300 cursor-pointer product-card" data-product-id="<?php echo $product['product_id']; ?>" data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>" data-product-price="<?php echo $product['discount_price'] ?? $product['price']; ?>">
                <div class="h-48 bg-gray-50 overflow-hidden relative">
                     <?php if($product['image']): ?>
                        <?php 
                        $imageUrl = (!empty($product['image']) && strpos($product['image'], 'http') === 0) 
                            ? $product['image'] 
                            : '/food_website/assets/images/products/' . $product['image'];
                        ?>
                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <?php else: ?>
                         <div class="w-full h-full flex items-center justify-center"><i class="bi bi-egg-fried text-4xl text-gray-300"></i></div>
                    <?php endif; ?>
                    
                    <?php if($product['discount_price']): ?>
                        <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">-<?php echo round((($product['price'] - $product['discount_price'])/$product['price'])*100); ?>%</span>
                    <?php endif; ?>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-dark mb-1 text-lg leading-tight"><?php echo $product['product_name']; ?></h3>
                    <p class="text-xs text-gray-500 mb-4 line-clamp-2 min-h-[2.5em]"><?php echo $product['description']; ?></p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex flex-col">
                            <?php if($product['discount_price']): ?>
                                <span class="text-xs text-gray-400 line-through"><?php echo formatPrice($product['price']); ?></span>
                                <span class="font-bold text-secondary text-lg"><?php echo formatPrice($product['discount_price']); ?></span>
                            <?php else: ?>
                                <span class="font-bold text-secondary text-lg"><?php echo formatPrice($product['price']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center hover:bg-blue-700 shadow-md shadow-blue-100 transition-all hover:scale-110 active:scale-95 add-to-cart-btn">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-20">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-search text-3xl text-gray-400"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-700">No products found</h3>
                <p class="text-gray-500">Try adjusting your search or category.</p>
                <a href="menu.php" class="mt-4 inline-block text-primary font-semibold hover:underline">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Product Selection Modal -->
<div id="productModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex items-center justify-between z-10">
            <h2 id="modalProductName" class="font-bricolage font-bold text-2xl text-dark">Product</h2>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="bi bi-x-lg text-2xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <!-- Product Image -->
            <div class="mb-6 rounded-2xl overflow-hidden bg-gray-100 h-80">
                <img id="modalProductImage" src="" alt="" class="w-full h-full object-cover">
            </div>
            
            <!-- Product Details -->
            <div class="space-y-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p id="modalProductPrice" class="text-3xl font-bold text-secondary font-bricolage">₦0</p>
                        <p id="modalProductOldPrice" class="text-sm text-gray-400 line-through"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">In Stock</p>
                        <p id="modalProductStock" class="text-lg font-bold text-green-600">Available</p>
                    </div>
                </div>
                
                <p id="modalProductDescription" class="text-gray-600 leading-relaxed"></p>
            </div>
            
            <!-- Quantity Selector -->
            <div class="mb-6 bg-gray-50 rounded-2xl p-6">
                <label class="block text-sm font-semibold text-dark mb-4">Select Quantity</label>
                <div class="flex items-center gap-6 justify-center">
                    <button id="quantityMinus" class="w-12 h-12 rounded-full border-2 border-gray-300 flex items-center justify-center text-xl font-bold text-gray-700 hover:border-primary hover:text-primary transition-all hover:scale-110">
                        <i class="bi bi-dash-lg"></i>
                    </button>
                    <div class="text-center">
                        <input type="number" id="quantityInput" min="1" value="1" class="w-20 text-center text-2xl font-bold text-dark border-2 border-gray-300 rounded-lg focus:border-primary focus:outline-none">
                        <p class="text-xs text-gray-500 mt-2">pieces</p>
                    </div>
                    <button id="quantityPlus" class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center text-xl font-bold hover:bg-blue-700 transition-all hover:scale-110">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Total Price -->
            <div class="mb-6 bg-blue-50 rounded-2xl p-6 border-2 border-blue-100">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 font-semibold">Total for this order:</span>
                    <span id="totalPrice" class="text-3xl font-bold text-primary font-bricolage">₦0</span>
                </div>
            </div>
            
            <!-- Add to Cart Button -->
            <button id="confirmAddToCart" class="w-full py-4 bg-primary text-white rounded-xl font-bold text-lg hover:bg-blue-700 transition-all hover:scale-[1.02] shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                <i class="bi bi-cart-plus"></i> Add to Cart
            </button>
            
            <button class="w-full mt-3 py-3 border-2 border-gray-200 text-dark rounded-xl font-semibold hover:border-gray-300 transition-all" id="cancelModal">
                Continue Shopping
            </button>
        </div>
    </div>
</div>

<script>
// Product Modal Management
document.querySelectorAll('.product-card').forEach(card => {
    const addBtn = card.querySelector('.add-to-cart-btn');
    addBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        openProductModal(card);
    });
    
    // Also make the entire card clickable
    card.addEventListener('click', () => {
        openProductModal(card);
    });
});

function openProductModal(card) {
    const productId = card.dataset.productId;
    const productName = card.dataset.productName;
    const productPrice = parseFloat(card.dataset.productPrice);
    
    // Get image from card
    const img = card.querySelector('img');
    const imageSrc = img ? img.src : '';
    
    // Get description
    const description = card.querySelector('p').textContent;
    
    // Get old price if exists
    const oldPriceEl = card.querySelector('.line-through');
    const oldPrice = oldPriceEl ? oldPriceEl.textContent : '';
    
    // Set modal data
    document.getElementById('modalProductName').textContent = productName;
    document.getElementById('modalProductImage').src = imageSrc;
    document.getElementById('modalProductImage').alt = productName;
    document.getElementById('modalProductPrice').textContent = formatPrice(productPrice);
    document.getElementById('modalProductDescription').textContent = description;
    document.getElementById('modalProductOldPrice').textContent = oldPrice;
    
    // Reset quantity
    document.getElementById('quantityInput').value = 1;
    updateTotalPrice(productPrice);
    
    // Store current product
    window.currentProduct = {
        id: productId,
        name: productName,
        price: productPrice
    };
    
    // Show modal
    document.getElementById('productModal').classList.remove('hidden');
}

function closeProductModal() {
    document.getElementById('productModal').classList.add('hidden');
}

// Modal Controls
document.getElementById('closeModal').addEventListener('click', closeProductModal);
document.getElementById('cancelModal').addEventListener('click', closeProductModal);

// Quantity Controls
const quantityInput = document.getElementById('quantityInput');
const quantityMinus = document.getElementById('quantityMinus');
const quantityPlus = document.getElementById('quantityPlus');

quantityMinus.addEventListener('click', (e) => {
    e.preventDefault();
    const currentValue = parseInt(quantityInput.value) || 1;
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
        updateTotalPrice(window.currentProduct.price);
    }
});

quantityPlus.addEventListener('click', (e) => {
    e.preventDefault();
    const currentValue = parseInt(quantityInput.value) || 1;
    quantityInput.value = currentValue + 1;
    updateTotalPrice(window.currentProduct.price);
});

quantityInput.addEventListener('change', () => {
    if (quantityInput.value < 1) quantityInput.value = 1;
    updateTotalPrice(window.currentProduct.price);
});

function updateTotalPrice(price) {
    const quantity = parseInt(quantityInput.value) || 1;
    const total = price * quantity;
    document.getElementById('totalPrice').textContent = formatPrice(total);
}

// Helper function to format price
function formatPrice(amount) {
    return '₦' + amount.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Add to Cart Button
document.getElementById('confirmAddToCart').addEventListener('click', () => {
    const product = window.currentProduct;
    const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
    
    // Send to cart
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
            showNotification(`✓ Added ${quantity}x ${product.name} to cart!`, 'success');
            closeProductModal();
            updateCartCount(data.cart_count);
        } else {
            showNotification(`✗ ${data.message || 'Error adding to cart'}`, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding to cart', 'error');
    });
});

// Close modal on outside click
document.getElementById('productModal').addEventListener('click', (e) => {
    if (e.target.id === 'productModal') {
        closeProductModal();
    }
});

// ============================================================================
// GREETING MESSAGE AND LOCATION FUNCTIONALITY
// ============================================================================

// Get current user name from session
const userName = '<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Guest'); ?>';
const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

// Update greeting based on time of day
function updateGreeting() {
    const now = new Date();
    const hour = now.getHours();
    let greeting, emoji;
    
    if (hour >= 5 && hour < 12) {
        greeting = 'Good Morning';
        emoji = '🌞';
    } else if (hour >= 12 && hour < 17) {
        greeting = 'Good Afternoon';
        emoji = '☀️';
    } else if (hour >= 17 && hour < 21) {
        greeting = 'Good Evening';
        emoji = '🌅';
    } else {
        greeting = 'Good Night';
        emoji = '🌙';
    }
    
    document.getElementById('greetingTitle').textContent = greeting;
    document.getElementById('greetingEmoji').textContent = emoji;
}

// Update current time display
function updateTime() {
    const now = new Date();
    
    // Time
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
    
    // Date
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateStr = now.toLocaleDateString('en-US', options);
    document.getElementById('currentDate').textContent = dateStr;
    
    // Update greeting every hour
    updateGreeting();
}

// Get user location
function getUserLocation() {
    const locationDisplay = document.getElementById('locationDisplay');
    
    if (navigator.geolocation) {
        // Try to get high accuracy location
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                
                // Use reverse geocoding API (OpenStreetMap Nominatim - free)
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        const city = data.address.city || data.address.town || data.address.village || 'Unknown Location';
                        const country = data.address.country || '';
                        locationDisplay.innerHTML = `
                            <div class="flex items-center gap-2">
                                <span>${city}, ${country}</span>
                                <span class="text-xs text-gray-500">(${latitude.toFixed(2)}°, ${longitude.toFixed(2)}°)</span>
                            </div>
                        `;
                    })
                    .catch(() => {
                        // Fallback if reverse geocoding fails
                        locationDisplay.textContent = `Location: ${latitude.toFixed(2)}°, ${longitude.toFixed(2)}°`;
                    });
            },
            (error) => {
                // Handle permission denied or other errors
                let errorMsg = 'Location access denied';
                
                if (error.code === error.PERMISSION_DENIED) {
                    errorMsg = 'Location access denied by user';
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    errorMsg = 'Location information unavailable';
                } else if (error.code === error.TIMEOUT) {
                    errorMsg = 'Location request timed out';
                }
                
                locationDisplay.innerHTML = `
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="bi bi-exclamation-circle"></i>
                        <span>${errorMsg}</span>
                    </div>
                `;
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    } else {
        locationDisplay.innerHTML = `
            <div class="flex items-center gap-2 text-gray-600">
                <i class="bi bi-exclamation-circle"></i>
                <span>Geolocation not supported</span>
            </div>
        `;
    }
}

// Set user name display
function setUserNameDisplay() {
    const userNameElement = document.getElementById('userName');
    if (isLoggedIn && userName) {
        userNameElement.textContent = userName.split(' ')[0]; // Show first name only
        userNameElement.classList.add('text-primary', 'font-semibold');
    } else {
        userNameElement.textContent = 'Guest';
        userNameElement.classList.add('text-gray-600');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    updateGreeting();
    updateTime();
    setUserNameDisplay();
    getUserLocation();
    
    // Update time every second
    setInterval(updateTime, 1000);
});
</script>

<?php include 'includes/footer.php'; ?>