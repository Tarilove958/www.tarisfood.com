<?php
// admin/add-product.php
require_once '../includes/config.php';
require_once 'includes/admin-functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = sanitize($_POST['product_name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $discount_price = (float)($_POST['discount_price'] ?? 0);
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status = sanitize($_POST['status'] ?? 'available');
    $image_url = sanitize($_POST['image_url'] ?? '');

    // Validation
    if (empty($product_name)) {
        $error = 'Product name is required';
    } elseif ($category_id === 0) {
        $error = 'Please select a category';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than 0';
    } else {
        // Handle image upload or URL
        $image = '';
        
        // Check if URL is provided
        if (!empty($image_url)) {
            // Validate URL
            if (filter_var($image_url, FILTER_VALIDATE_URL)) {
                $image = $image_url;
            } else {
                $error = 'Invalid image URL';
            }
        } elseif (!empty($_FILES['image']['name'])) {
            // Handle file upload
            $upload = uploadImage($_FILES['image'], PRODUCT_IMAGE_PATH);
            if (!$upload['success']) {
                $error = $upload['message'];
            } else {
                $image = $upload['filename'];
            }
        } else {
            $error = 'Please provide either an image file or a URL';
        }

        if (empty($error)) {
            $sql = "INSERT INTO products (category_id, product_name, description, price, discount_price, image, stock_quantity, is_featured, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issddsisi", $category_id, $product_name, $description, $price, $discount_price, $image, $stock_quantity, $is_featured, $status);
            
            if ($stmt->execute()) {
                setFlashMessage('success', 'Product added successfully!');
                redirect('manage-products.php');
            } else {
                $error = 'Error adding product: ' . $conn->error;
            }
        }
    }
}

// Load categories and include header AFTER POST processing
$categories = getAllCategories();
require_once 'includes/admin-header.php';
?>

<div class="max-w-2xl">
    <div class="mb-8">
        <a href="manage-products.php" class="text-primary font-bold mb-4 inline-flex items-center gap-2 hover:underline">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
        <h1 class="font-heading text-3xl font-bold text-gray-800">Add New Product</h1>
    </div>

    <?php if ($error): ?>
    <div class="mb-6 bg-red-50 text-red-800 p-4 rounded-xl border border-red-200">
        <p class="font-bold"><i class="bi bi-exclamation-circle-fill"></i> Error</p>
        <p><?php echo htmlspecialchars($error); ?></p>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <label class="block text-sm font-bold mb-2">Product Name *</label>
            <input type="text" name="product_name" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" required>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2">Category *</label>
                <select name="category_id" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Status</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none">
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2">Description</label>
            <textarea name="description" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" rows="4"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2">Price (₦) *</label>
                <input type="number" name="price" step="0.01" min="0" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">Discount Price (₦)</label>
                <input type="number" name="discount_price" step="0.01" min="0" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2">Stock Quantity</label>
            <input type="number" name="stock_quantity" min="0" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" value="0">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2">Product Image</label>
            
            <!-- Image URL Option -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Image URL</label>
                <input type="url" name="image_url" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" placeholder="https://example.com/image.jpg">
                <p class="text-xs text-gray-500 mt-1">Paste an image URL or upload a file below</p>
            </div>

            <!-- OR Divider -->
            <div class="flex items-center gap-4 my-4">
                <div class="flex-1 h-px bg-gray-300"></div>
                <span class="text-xs font-bold text-gray-500">OR</span>
                <div class="flex-1 h-px bg-gray-300"></div>
            </div>

            <!-- File Upload Option -->
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-primary transition-colors">
                <input type="file" name="image" class="hidden" id="image-input" accept="image/*">
                <label for="image-input" class="cursor-pointer">
                    <i class="bi bi-cloud-arrow-up text-3xl text-gray-400 mb-2 block"></i>
                    <p class="text-sm font-bold text-gray-700">Click to upload or drag and drop</p>
                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                </label>
            </div>
        </div>

        <div class="mb-6">
            <label class="flex items-center gap-3">
                <input type="checkbox" name="is_featured" class="w-5 h-5 rounded border-gray-300 cursor-pointer">
                <span class="text-sm font-bold text-gray-700">Featured Product</span>
            </label>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors">
                <i class="bi bi-check-lg"></i> Add Product
            </button>
            <a href="manage-products.php" class="px-6 py-3 rounded-xl font-bold border border-gray-300 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
