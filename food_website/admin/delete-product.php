<?php
// admin/delete-product.php
// Handle deletion logic BEFORE including admin-header (which outputs HTML)
session_start();
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once 'includes/admin-functions.php';

// Security check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$product_id = (int)($_GET['id'] ?? 0);

if ($product_id === 0) {
    setFlashMessage('error', 'Invalid product ID');
    redirect('manage-products.php');
}

$product = getProductById($product_id);
if (!$product) {
    setFlashMessage('error', 'Product not found');
    redirect('manage-products.php');
}

// Delete product image if exists
if (!empty($product['image'])) {
    $imagePath = PRODUCT_IMAGE_PATH . $product['image'];
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

// Delete product from database
$sql = "DELETE FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    setFlashMessage('success', 'Product deleted successfully!');
} else {
    setFlashMessage('error', 'Error deleting product: ' . $conn->error);
}

redirect('manage-products.php');
?>
