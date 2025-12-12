<?php
// Common functions for the Lost & Found system

/**
 * Get user profile information
 */
function getUserProfile($conn, $username) {
    $query = "SELECT gambar, dn_pengguna FROM pengguna WHERE nm_pengguna = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $profile = [
        'image' => 'images/default-profile.png',
        'display_name' => $username
    ];
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        if (!empty($row['gambar'])) $profile['image'] = $row['gambar'];
        $profile['display_name'] = !empty($row['dn_pengguna']) ? $row['dn_pengguna'] : $username;
    }
    
    mysqli_stmt_close($stmt);
    return $profile;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['username']);
}

/**
 * Get current user's username
 */
function getCurrentUser() {
    return $_SESSION['username'] ?? null;
}

/**
 * Handle image upload
 */
function uploadImage($file, $target_dir = "uploads/") {
    if (!isset($file) || $file['error'] != 0) {
        return null;
    }
    
    $filename = $target_dir . time() . "_" . basename($file["name"]);
    if (move_uploaded_file($file["tmp_name"], $filename)) {
        return $filename;
    }
    return null;
}

/**
 * Get items with pagination
 */
function getItems($conn, $page = 1, $items_per_page = 12, $status = null) {
    $offset = ($page - 1) * $items_per_page;
    
    $where_clause = "";
    $params = [];
    $types = "ii";
    
    if ($status) {
        $where_clause = "WHERE status = ?";
        $params[] = $status;
        $types = "sii";
    }
    
    $query = "SELECT nm_item, th_item, etc_item, img_item, status, nm_pengguna, reward, no_pengguna 
              FROM item 
              $where_clause
              ORDER BY th_item DESC 
              LIMIT ? OFFSET ?";
    
    $params[] = $items_per_page;
    $params[] = $offset;
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $items;
}

/**
 * Format date for display
 */
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Get default image if none provided
 */
function getDefaultImage($image, $type = 'item') {
    if (empty($image)) {
        return $type === 'profile' ? 'images/default-profile.png' : 'images/default-item.png';
    }
    return $image;
}

/**
 * Escape HTML output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?> 