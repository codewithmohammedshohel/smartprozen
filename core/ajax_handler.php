<?php
/**
 * AJAX Handler for SmartProZen CMS
 * Handles various AJAX requests for improved user experience
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Set header to JSON
header('Content-Type: application/json');

// Check if request is AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Get the action
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Process based on action
switch ($action) {
    case 'quick_search':
        handleQuickSearch();
        break;
    
    case 'update_cart':
        handleUpdateCart();
        break;
    
    case 'load_products':
        handleLoadProducts();
        break;
        
    case 'toggle_wishlist':
        handleToggleWishlist();
        break;
        
    case 'filter_products':
        handleFilterProducts();
        break;
        
    case 'load_notifications':
        handleLoadNotifications();
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}

/**
 * Handle quick search functionality
 */
function handleQuickSearch() {
    global $conn;
    
    $query = isset($_POST['query']) ? $_POST['query'] : '';
    
    if (empty($query) || strlen($query) < 2) {
        echo json_encode(['success' => false, 'message' => 'Search query too short']);
        return;
    }
    
    // Sanitize the query
    $search_term = '%' . $conn->real_escape_string($query) . '%';
    
    // Prepare the query
    $stmt = $conn->prepare("
        SELECT 
            'product' as type,
            id,
            name as title,
            CONCAT('/smartprozen/product.php?id=', id) as url,
            featured_image as image,
            price
        FROM products 
        WHERE name LIKE ? OR description LIKE ?
        UNION
        SELECT 
            'post' as type,
            id,
            title,
            CONCAT('/smartprozen/post.php?id=', id) as url,
            featured_image as image,
            NULL as price
        FROM posts 
        WHERE title LIKE ? OR content LIKE ?
        LIMIT 10
    ");
    
    $stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        // Format price if it exists
        if ($row['price'] !== null) {
            $row['price_formatted'] = number_format($row['price'], 2);
        }
        
        // Get image URL
        if (!empty($row['image'])) {
            $row['image_url'] = '/smartprozen/uploads/media/' . $row['image'];
        } else {
            $row['image_url'] = '/smartprozen/assets/images/placeholder.jpg';
        }
        
        $items[] = $row;
    }
    
    echo json_encode([
        'success' => true, 
        'results' => $items,
        'count' => count($items)
    ]);
}

/**
 * Handle cart updates via AJAX
 */
function handleUpdateCart() {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    
    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product']);
        return;
    }
    
    // Initialize cart if it doesn't exist
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Update quantity or remove if quantity is 0
    if ($quantity > 0) {
        $_SESSION['cart'][$product_id] = [
            'product_id' => $product_id,
            'quantity' => $quantity
        ];
    } else {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    
    // Calculate cart totals
    $total_items = 0;
    $total_price = 0;
    
    global $conn;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
        
        // Get product price
        $stmt = $conn->prepare("SELECT price, sale_price FROM products WHERE id = ?");
        $stmt->bind_param("i", $item['product_id']);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        
        if ($product) {
            $price = ($product['sale_price'] > 0) ? $product['sale_price'] : $product['price'];
            $total_price += $price * $item['quantity'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'cart_count' => $total_items,
        'cart_total' => number_format($total_price, 2),
        'message' => 'Cart updated successfully'
    ]);
}

/**
 * Handle loading products via AJAX for infinite scroll or pagination
 */
function handleLoadProducts() {
    global $conn;
    
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 12;
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    
    $offset = ($page - 1) * $limit;
    
    // Build the query
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.status = 'active'";
    
    if ($category_id > 0) {
        $query .= " AND p.category_id = " . $category_id;
    }
    
    $query .= " ORDER BY p.created_at DESC LIMIT ?, ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $offset, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Format prices
        $row['price_formatted'] = number_format($row['price'], 2);
        if ($row['sale_price'] > 0) {
            $row['sale_price_formatted'] = number_format($row['sale_price'], 2);
        }
        
        // Get image URL
        if (!empty($row['featured_image'])) {
            $row['image_url'] = '/smartprozen/uploads/media/' . $row['featured_image'];
        } else {
            $row['image_url'] = '/smartprozen/assets/images/placeholder.jpg';
        }
        
        $products[] = $row;
    }
    
    // Get total products count for pagination
    $count_query = "SELECT COUNT(*) as total FROM products WHERE status = 'active'";
    if ($category_id > 0) {
        $count_query .= " AND category_id = " . $category_id;
    }
    
    $total_result = $conn->query($count_query);
    $total_row = $total_result->fetch_assoc();
    $total_products = $total_row['total'];
    
    $total_pages = ceil($total_products / $limit);
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_products' => $total_products,
            'has_more' => ($page < $total_pages)
        ]
    ]);
}

/**
 * Handle toggling wishlist items
 */
function handleToggleWishlist() {
    if (!is_user_logged_in()) {
        echo json_encode(['success' => false, 'message' => 'Please login to add items to your wishlist']);
        return;
    }
    
    global $conn;
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $user_id = $_SESSION['user_id'];
    
    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product']);
        return;
    }
    
    // Check if product exists in wishlist
    $check_stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Remove from wishlist
        $delete_stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $delete_stmt->bind_param("ii", $user_id, $product_id);
        $delete_stmt->execute();
        
        echo json_encode([
            'success' => true,
            'in_wishlist' => false,
            'message' => 'Product removed from wishlist'
        ]);
    } else {
        // Add to wishlist
        $insert_stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())");
        $insert_stmt->bind_param("ii", $user_id, $product_id);
        $insert_stmt->execute();
        
        echo json_encode([
            'success' => true,
            'in_wishlist' => true,
            'message' => 'Product added to wishlist'
        ]);
    }
}

/**
 * Handle filtering products
 */
function handleFilterProducts() {
    global $conn;
    
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $min_price = isset($_POST['min_price']) ? (float)$_POST['min_price'] : 0;
    $max_price = isset($_POST['max_price']) ? (float)$_POST['max_price'] : 100000;
    $sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : 'newest';
    
    // Build the query
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.status = 'active'";
    
    if ($category_id > 0) {
        $query .= " AND p.category_id = " . $category_id;
    }
    
    // Price filter
    $query .= " AND (
                    (p.sale_price > 0 AND p.sale_price BETWEEN $min_price AND $max_price)
                    OR 
                    (p.sale_price = 0 AND p.price BETWEEN $min_price AND $max_price)
                )";
    
    // Sorting
    switch ($sort_by) {
        case 'price_low':
            $query .= " ORDER BY CASE WHEN p.sale_price > 0 THEN p.sale_price ELSE p.price END ASC";
            break;
        case 'price_high':
            $query .= " ORDER BY CASE WHEN p.sale_price > 0 THEN p.sale_price ELSE p.price END DESC";
            break;
        case 'name_asc':
            $query .= " ORDER BY p.name ASC";
            break;
        case 'name_desc':
            $query .= " ORDER BY p.name DESC";
            break;
        case 'oldest':
            $query .= " ORDER BY p.created_at ASC";
            break;
        case 'newest':
        default:
            $query .= " ORDER BY p.created_at DESC";
            break;
    }
    
    $result = $conn->query($query);
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Format prices
        $row['price_formatted'] = number_format($row['price'], 2);
        if ($row['sale_price'] > 0) {
            $row['sale_price_formatted'] = number_format($row['sale_price'], 2);
        }
        
        // Get image URL
        if (!empty($row['featured_image'])) {
            $row['image_url'] = '/smartprozen/uploads/media/' . $row['featured_image'];
        } else {
            $row['image_url'] = '/smartprozen/assets/images/placeholder.jpg';
        }
        
        $products[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'count' => count($products)
    ]);
}

/**
 * Handle loading user notifications
 */
function handleLoadNotifications() {
    if (!is_user_logged_in()) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        return;
    }
    
    global $conn;
    $user_id = $_SESSION['user_id'];
    
    $query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        // Format date
        $row['created_at_formatted'] = date('M j, Y g:i A', strtotime($row['created_at']));
        $notifications[] = $row;
    }
    
    // Get unread count
    $unread_query = "SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
    $unread_stmt = $conn->prepare($unread_query);
    $unread_stmt->bind_param("i", $user_id);
    $unread_stmt->execute();
    $unread_result = $unread_stmt->get_result();
    $unread_row = $unread_result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unread_row['unread_count']
    ]);
}