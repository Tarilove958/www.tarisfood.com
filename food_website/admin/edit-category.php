<?php
// admin/edit-category.php
require_once '../includes/config.php';
require_once 'includes/admin-functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$category_id = (int)($_GET['id'] ?? 0);
if ($category_id === 0) {
    redirect('manage-categories.php');
}

$category = getCategoryById($category_id);
if (!$category) {
    setFlashMessage('error', 'Category not found');
    redirect('manage-categories.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = sanitize($_POST['category_name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $status = sanitize($_POST['status'] ?? 'active');

    if (empty($category_name)) {
        $error = 'Category name is required';
    } else {
        $sql = "UPDATE categories SET category_name = ?, description = ?, status = ? WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $category_name, $description, $status, $category_id);

        if ($stmt->execute()) {
            setFlashMessage('success', 'Category updated successfully!');
            redirect('manage-categories.php');
        } else {
            $error = 'Error updating category: ' . $conn->error;
        }
    }
}

// Include header AFTER POST processing
require_once 'includes/admin-header.php';
?>

<div class="max-w-2xl">
    <div class="mb-8">
        <a href="manage-categories.php" class="text-primary font-bold mb-4 inline-flex items-center gap-2 hover:underline">
            <i class="bi bi-arrow-left"></i> Back to Categories
        </a>
        <h1 class="font-heading text-3xl font-bold text-gray-800">Edit Category</h1>
    </div>

    <?php if ($error): ?>
    <div class="mb-6 bg-red-50 text-red-800 p-4 rounded-xl border border-red-200">
        <p class="font-bold"><i class="bi bi-exclamation-circle-fill"></i> Error</p>
        <p><?php echo htmlspecialchars($error); ?></p>
    </div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <label class="block text-sm font-bold mb-2">Category Name *</label>
            <input type="text" name="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" required>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2">Description</label>
            <textarea name="description" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" rows="4"><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2">Status</label>
            <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="active" <?php echo $category['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $category['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors">
                <i class="bi bi-check-lg"></i> Update Category
            </button>
            <a href="manage-categories.php" class="px-6 py-3 rounded-xl font-bold border border-gray-300 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
