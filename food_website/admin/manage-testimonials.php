<?php
// admin/manage-testimonials.php
require_once '../includes/config.php';
require_once 'includes/admin-functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testimonial_id = (int)($_POST['testimonial_id'] ?? 0);
    $action = sanitize($_POST['action'] ?? '');

    if ($testimonial_id > 0 && in_array($action, ['approve', 'reject'])) {
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $sql = "UPDATE testimonials SET status = ? WHERE testimonial_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $testimonial_id);

        if ($stmt->execute()) {
            setFlashMessage('success', 'Testimonial updated successfully!');
            redirect('manage-testimonials.php?page=' . $page);
        }
    }
}

// Delete testimonial
if (isset($_GET['delete'])) {
    $testimonial_id = (int)$_GET['delete'];
    
    // Get testimonial to delete image
    $testimonial = $conn->query("SELECT * FROM testimonials WHERE testimonial_id = $testimonial_id")->fetch_assoc();
    
    if ($testimonial && !empty($testimonial['image'])) {
        $imagePath = __DIR__ . '/../uploads/testimonials/' . $testimonial['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    $sql = "DELETE FROM testimonials WHERE testimonial_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $testimonial_id);
    
    if ($stmt->execute()) {
        setFlashMessage('success', 'Testimonial deleted successfully!');
        redirect('manage-testimonials.php');
    }
}

// Get total testimonials
$total_result = $conn->query("SELECT COUNT(testimonial_id) as total FROM testimonials");
$total_row = $total_result->fetch_assoc();
$total_testimonials = $total_row['total'];
$total_pages = ceil($total_testimonials / $per_page);

// Get testimonials for current page
$sql = "SELECT * FROM testimonials ORDER BY created_at DESC LIMIT $offset, $per_page";
$testimonials = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

// Include header AFTER GET/POST processing
require_once 'includes/admin-header.php';
?>

<div class="mb-8">
    <h1 class="font-heading text-3xl font-bold text-gray-800">Manage Testimonials</h1>
    <p class="text-gray-500">Total: <?php echo $total_testimonials; ?> testimonials</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-900 font-bold uppercase text-xs">
                <tr>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Rating</th>
                    <th class="px-6 py-4">Testimonial</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Featured</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($testimonials)): ?>
                    <?php foreach ($testimonials as $testimonial): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-bold text-dark"><?php echo htmlspecialchars($testimonial['customer_name']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($testimonial['customer_email'] ?? ''); ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-1">
                                <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                    <i class="bi bi-star-fill text-yellow-500"></i>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <p class="text-gray-600 truncate"><?php echo htmlspecialchars(substr($testimonial['testimonial_text'], 0, 50)) . '...'; ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                            if ($testimonial['status'] == 'approved') $statusClass = 'bg-green-100 text-green-800';
                            if ($testimonial['status'] == 'rejected') $statusClass = 'bg-red-100 text-red-800';
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $statusClass; ?>">
                                <?php echo ucfirst($testimonial['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $testimonial['is_featured'] ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo $testimonial['is_featured'] ? 'Yes' : 'No'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs"><?php echo date('M d, Y', strtotime($testimonial['created_at'])); ?></td>
                        <td class="px-6 py-4 flex gap-2">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="testimonial_id" value="<?php echo $testimonial['testimonial_id']; ?>">
                                <?php if ($testimonial['status'] !== 'approved'): ?>
                                <button type="submit" name="action" value="approve" class="text-green-600 hover:text-green-800 font-bold text-sm" title="Approve">
                                    <i class="bi bi-check-circle"></i> Approve
                                </button>
                                <?php endif; ?>
                            </form>
                            <a href="?delete=<?php echo $testimonial['testimonial_id']; ?>" class="text-red-600 hover:text-red-800 font-bold text-sm" onclick="return confirm('Are you sure?');">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No testimonials found
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
