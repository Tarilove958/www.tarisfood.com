<?php
// admin/manage-categories.php
require_once '../includes/config.php';
require_once 'includes/admin-functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$error = '';
$success = '';

// Add new category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $category_name = sanitize($_POST['category_name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $status = sanitize($_POST['status'] ?? 'active');

    if (empty($category_name)) {
        $error = 'Category name is required';
    } else {
        $sql = "INSERT INTO categories (category_name, description, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $category_name, $description, $status);

        if ($stmt->execute()) {
            setFlashMessage('success', 'Category added successfully!');
            redirect('manage-categories.php');
        } else {
            $error = 'Error adding category: ' . $conn->error;
        }
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    $sql = "DELETE FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);

    if ($stmt->execute()) {
        setFlashMessage('success', 'Category deleted successfully!');
        redirect('manage-categories.php');
    }
}

// Load categories and include header AFTER GET/POST processing
$categories = getAllCategories();
require_once 'includes/admin-header.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Add Category Form -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
            <h2 class="font-heading text-lg font-bold text-gray-800 mb-4">Add New Category</h2>

            <?php if ($error): ?>
            <div class="mb-4 bg-red-50 text-red-800 p-3 rounded-lg border border-red-200 text-sm">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Category Name *</label>
                    <input type="text" name="category_name" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Description</label>
                    <textarea name="description" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" rows="3"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors">
                    <i class="bi bi-plus-lg"></i> Add Category
                </button>
            </form>
        </div>
    </div>

    <!-- Categories List -->
    <div class="lg:col-span-2">
        <div class="mb-8">
            <h1 class="font-heading text-3xl font-bold text-gray-800">Manage Categories</h1>
            <p class="text-gray-500">Total: <?php echo count($categories); ?> categories</p>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-heading text-lg font-bold text-dark mb-2">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </h3>
                            <p class="text-gray-600 text-sm mb-3">
                                <?php echo htmlspecialchars(substr($category['description'] ?? '', 0, 100)); ?>
                            </p>
                            <div class="flex gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-bold 
                                    <?php echo $category['status'] == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>
                                ">
                                    <?php echo ucfirst($category['status']); ?>
                                </span>
                                <?php 
                                $product_count = $conn->query("SELECT COUNT(*) as total FROM products WHERE category_id = " . $category['category_id'])->fetch_assoc();
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                    <?php echo $product_count['total']; ?> products
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <a href="edit-category.php?id=<?php echo $category['category_id']; ?>" class="px-4 py-2 bg-blue-100 text-primary rounded-lg font-bold hover:bg-blue-200 transition-colors text-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="?delete=<?php echo $category['category_id']; ?>" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg font-bold hover:bg-red-200 transition-colors text-sm" onclick="return confirm('Delete this category?');">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <p class="text-gray-500 mb-4">No categories found</p>
                <p class="text-sm text-gray-400">Create your first category to get started</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
