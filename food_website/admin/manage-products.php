<?php
// admin/manage-products.php
require_once 'includes/admin-header.php';
require_once 'includes/admin-functions.php';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get total products
$total_result = $conn->query("SELECT COUNT(product_id) as total FROM products");
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $per_page);

// Get products for current page
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        ORDER BY p.created_at DESC 
        LIMIT $offset, $per_page";
$products = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<!-- Page Header -->
<div class="mb-8">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="font-heading text-4xl font-bold text-dark mb-2">Product Management</h1>
            <p class="text-gray-500 text-sm font-medium">Manage all your food items and their details</p>
        </div>
        <a href="add-product.php" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-bold hover:shadow-lg transition-smooth">
            <i class="bi bi-plus-lg"></i> Add New Product
        </a>
    </div>
</div>

<!-- Product Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
    <div class="admin-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide mb-1">Total Products</p>
                <h3 class="text-3xl font-bold text-dark"><?php echo $total_products; ?></h3>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); color: #16A34A; width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                <i class="bi bi-basket2-fill"></i>
            </div>
        </div>
    </div>

    <div class="admin-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide mb-1">Low Stock</p>
                <h3 class="text-3xl font-bold text-dark"><?php 
                    $low_stock = $conn->query("SELECT COUNT(product_id) as count FROM products WHERE stock_quantity < 10")->fetch_assoc();
                    echo $low_stock['count'] ?? 0;
                ?></h3>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(234, 88, 12, 0.1) 100%); color: #EA580C; width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
        </div>
    </div>

    <div class="admin-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide mb-1">Active Products</p>
                <h3 class="text-3xl font-bold text-dark"><?php 
                    $active = $conn->query("SELECT COUNT(product_id) as count FROM products WHERE status = 'available'")->fetch_assoc();
                    echo $active['count'] ?? 0;
                ?></h3>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.1) 100%); color: var(--primary); width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="admin-card overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h2 class="font-heading text-xl font-bold text-dark">All Products</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                    <tr class="hover:bg-gray-50 transition-smooth">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500">
                                    <i class="bi bi-image text-sm"></i>
                                </div>
                                <span class="font-bold text-dark"><?php echo htmlspecialchars($product['product_name']); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <span class="px-3 py-1 rounded-lg bg-gray-100 text-xs font-medium text-gray-700">
                                <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-dark">₦<?php echo number_format($product['price'], 2); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $product['stock_quantity'] > 0 ? 'badge-success' : 'badge-cancelled'; ?>">
                                <?php echo $product['stock_quantity']; ?> items
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold 
                                <?php 
                                if ($product['status'] == 'available') echo 'badge-success';
                                elseif ($product['status'] == 'unavailable') echo 'badge-pending';
                                else echo 'badge-cancelled';
                                ?>
                            ">
                                <?php echo ucfirst($product['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="edit-product.php?id=<?php echo $product['product_id']; ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-blue-100 text-primary transition-smooth" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="delete-product.php?id=<?php echo $product['product_id']; ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-red-100 text-red-600 transition-smooth" title="Delete" onclick="return confirm('Are you sure?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="px-6 py-12">
                        <div class="text-center text-gray-400">
                            <i class="bi bi-inbox text-4xl mb-3 block"></i>
                            <p class="font-medium mb-3">No products found</p>
                            <a href="add-product.php" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-bold hover:shadow-lg transition-smooth">
                                <i class="bi bi-plus-lg"></i> Create Your First Product
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center gap-2">
        <div class="flex items-center gap-2 flex-wrap justify-center">
            <?php if ($page > 1): ?>
                <a href="?page=1" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-smooth font-medium text-sm">
                    <i class="bi bi-chevron-double-left"></i>
                </a>
                <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-smooth font-medium text-sm">
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
            <?php endif; ?>
            
            <span class="px-4 py-2 text-sm font-medium text-gray-700">
                Page <strong><?php echo $page; ?></strong> of <strong><?php echo $total_pages; ?></strong>
            </span>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-smooth font-medium text-sm">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
                <a href="?page=<?php echo $total_pages; ?>" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-smooth font-medium text-sm">
                    <i class="bi bi-chevron-double-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/admin-footer.php'; ?>