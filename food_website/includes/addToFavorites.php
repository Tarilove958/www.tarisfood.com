<?php
/**
 * Add product to favorites
 * Saves to database if user is logged in, otherwise client-side localStorage
 */
require_once 'config.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = (int)($data['product_id'] ?? 0);
    
    if ($product_id === 0) {
        throw new Exception('Invalid product ID');
    }
    
    // If user is logged in, save to database
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        
        // Check if already favorited
        $checkSql = "SELECT * FROM favorites WHERE user_id = ? AND product_id = ?";
        $stmt = $pdo->prepare($checkSql);
        $stmt->execute([$user_id, $product_id]);
        
        if ($stmt->rowCount() === 0) {
            // Add to favorites
            $sql = "INSERT INTO favorites (user_id, product_id, created_at) VALUES (?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $product_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Added to favorites',
                'saved' => 'database'
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Already in favorites',
                'saved' => 'database'
            ]);
        }
    } else {
        // User not logged in, data saved client-side only
        echo json_encode([
            'success' => true,
            'message' => 'Saved locally (login to sync across devices)',
            'saved' => 'local'
        ]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
