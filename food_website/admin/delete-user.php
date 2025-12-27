<?php
/**
 * Delete User Handler
 * Permanently deletes a user and all associated data
 */

require_once '../includes/config.php';

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] != 'admin') {
    header('HTTP/1.0 403 Forbidden');
    setFlashMessage('error', 'Unauthorized access');
    redirect('manage-users.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method');
    redirect('manage-users.php');
}

try {
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    
    if (!$user_id) {
        setFlashMessage('error', 'Invalid user ID');
        redirect('manage-users.php');
    }
    
    // Get user details first
    $stmt = $conn->prepare("SELECT full_name, user_type FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        setFlashMessage('error', 'User not found');
        redirect('manage-users.php');
    }
    
    // Prevent deletion of admin accounts
    if ($user['user_type'] == 'admin') {
        setFlashMessage('error', 'Cannot delete admin accounts');
        redirect('manage-users.php');
    }
    
    // Start transaction to delete user and all related data
    $conn->begin_transaction();
    
    try {
        // Delete user's cart items
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Delete user's orders (cascade will handle order_items)
        $stmt = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Delete the user
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        setFlashMessage('success', 'User "' . htmlspecialchars($user['full_name']) . '" has been permanently deleted');
        redirect('manage-users.php');
        
    } catch (Exception $e) {
        $conn->rollback();
        setFlashMessage('error', 'Error deleting user: ' . $e->getMessage());
        redirect('manage-users.php');
    }
    
} catch (Exception $e) {
    setFlashMessage('error', 'Error: ' . $e->getMessage());
    redirect('manage-users.php');
}
?>
