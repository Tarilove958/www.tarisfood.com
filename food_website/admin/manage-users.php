<?php
// admin/manage-users.php
require_once 'includes/admin-header.php';
require_once 'includes/admin-functions.php';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get total users
$total_result = $conn->query("SELECT COUNT(user_id) as total FROM users");
$total_row = $total_result->fetch_assoc();
$total_users = $total_row['total'];
$total_pages = ceil($total_users / $per_page);

// Get users for current page
$sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT $offset, $per_page";
$users = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<div class="mb-8">
    <h1 class="font-heading text-3xl font-bold text-gray-800">Manage Users</h1>
    <p class="text-gray-500">Total: <?php echo $total_users; ?> users</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-900 font-bold uppercase text-xs">
                <tr>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Phone</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Joined</th>
                    <th class="px-6 py-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-dark"><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $user['user_type'] == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                                <?php echo ucfirst($user['user_type']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold 
                                <?php 
                                if ($user['status'] == 'active') echo 'bg-green-100 text-green-800';
                                elseif ($user['status'] == 'inactive') echo 'bg-gray-100 text-gray-800';
                                else echo 'bg-red-100 text-red-800';
                                ?>
                            ">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="view-user.php?id=<?php echo $user['user_id']; ?>" class="text-primary hover:text-blue-700 font-bold text-sm" title="View Details">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <?php if ($user['user_type'] != 'admin'): ?>
                                <form method="POST" action="delete-user.php" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this user? This action cannot be undone.');">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-sm" title="Delete User">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No users found
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
