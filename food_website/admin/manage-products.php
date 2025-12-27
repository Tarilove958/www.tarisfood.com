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

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="font-heading text-3xl font-bold text-gray-800">Manage Products</h1>
        <p class="text-gray-500">Total: <?php echo $total_products; ?> products</p>
    </div>
    <a href="add-product.php" class="bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-colors flex items-center gap-2">
        <i class="bi bi-plus-lg"></i> Add New Product
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-900 font-bold uppercase text-xs">
                <tr>
                    <th class="px-6 py-4">Product Name</th>
                    <th class="px-6 py-4">Category</th>
                    <th class="px-6 py-4">Price</th>
                    <th class="px-6 py-4">Stock</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-dark"><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                        <td class="px-6 py-4 font-bold">₦<?php echo number_format($product['price'], 2); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $product['stock_quantity'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $product['stock_quantity']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold 
                                <?php 
                                if ($product['status'] == 'available') echo 'bg-green-100 text-green-800';
                                elseif ($product['status'] == 'unavailable') echo 'bg-gray-100 text-gray-800';
                                else echo 'bg-red-100 text-red-800';
                                ?>
                            ">
                                <?php echo ucfirst($product['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 flex gap-2">
                            <a href="edit-product.php?id=<?php echo $product['product_id']; ?>" class="text-primary hover:text-blue-700 font-bold text-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="delete-product.php?id=<?php echo $product['product_id']; ?>" class="text-red-600 hover:text-red-800 font-bold text-sm" onclick="return confirm('Are you sure?');">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <p class="mb-4">No products found</p>
                        <a href="add-product.php" class="text-primary font-bold hover:underline">Create your first product</a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center gap-2">
        <?php if ($page > 1): ?>
            <a href="?page=1" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">First</a>
            <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Prev</a>
        <?php endif; ?>

        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="px-3 py-2 rounded-lg <?php echo $i === $page ? 'bg-primary text-white' : 'bg-white border border-gray-300 hover:bg-gray-50'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
            <a href="?page=<?php echo $total_pages; ?>" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Last</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/admin-footer.php'; ?>
