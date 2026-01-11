<?php
/**
 * Additional Utility Functions
 * Non-database utility functions only
 */

/**
 * Validate phone number (Nigerian format)
 */
function isValidPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return preg_match('/^(0|\+?234)?[7-9][0-1]\d{8}$/', $phone);
}

/**
 * Format phone number
 */
function formatPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
        return '+234' . substr($phone, 1);
    } elseif (strlen($phone) === 10) {
        return '+234' . $phone;
    } elseif (strlen($phone) === 13 && substr($phone, 0, 3) === '234') {
        return '+' . $phone;
    }
    
    return $phone;
}

/**
 * Generate random string for tokens/codes
 */
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Truncate text to specified length
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get order status badge HTML
 */
function getOrderStatusBadge($status) {
    $badges = [
        'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
        'confirmed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Confirmed</span>',
        'processing' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Processing</span>',
        'out_for_delivery' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Out for Delivery</span>',
        'delivered' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Delivered</span>',
        'cancelled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>',
    ];
    
    return $badges[$status] ?? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">' . ucfirst($status) . '</span>';
}

/**
 * Get payment status badge HTML
 */
function getPaymentStatusBadge($status) {
    $badges = [
        'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
        'paid' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>',
        'failed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Failed</span>',
        'refunded' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Refunded</span>',
    ];
    
    return $badges[$status] ?? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">' . ucfirst($status) . '</span>';
}
?>
