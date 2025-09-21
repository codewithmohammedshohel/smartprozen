<?php
require_once '../config.php';
require_once 'db.php';
require_once 'functions.php';

// Security: Ensure an admin is logged in and has permission to manage media
if (!is_admin_logged_in() || !has_permission('manage_media')) {
    // We can't redirect with die(), so we set an error and do it in JS or the calling page
    $_SESSION['error_message'] = "You do not have permission to perform this action.";
    header('Location: /smartprozen/admin/media_library.php');
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'upload':
        handle_upload();
        break;
    
    case 'delete':
        handle_delete();
        break;

    default:
        $_SESSION['error_message'] = "Invalid action specified.";
        header('Location: /smartprozen/admin/media_library.php');
        exit;
}

/**
 * Handles the file upload process.
 */
function handle_upload() {
    global $conn;

    if (!isset($_FILES['media_file']) || $_FILES['media_file']['error'] != 0) {
        $_SESSION['error_message'] = "File upload error. Please try again.";
        header('Location: /smartprozen/admin/media_library.php');
        exit;
    }

    $file = $_FILES['media_file'];
    $alt_text = trim($_POST['alt_text'] ?? '');

    // --- Validation ---
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['error_message'] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        header('Location: /smartprozen/admin/media_library.php');
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5 MB limit
        $_SESSION['error_message'] = "File is too large. Maximum size is 5 MB.";
        header('Location: /smartprozen/admin/media_library.php');
        exit;
    }

    // --- File Processing ---
    $upload_dir = __DIR__ . '/../uploads/media/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $original_filename = basename($file["name"]);
    $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
    $stored_filename = uniqid('media_', true) . '.' . $file_extension;
    $thumbnail_filename = 'thumb-' . $stored_filename;
    
    $target_path = $upload_dir . $stored_filename;
    $thumb_path = $upload_dir . $thumbnail_filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Create a thumbnail
        if (create_thumbnail($target_path, $thumb_path, 300, 300)) {
            // Save to database
            $stmt = $conn->prepare("INSERT INTO media_library (original_filename, stored_filename, thumbnail_filename, file_type, alt_text) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $original_filename, $stored_filename, $thumbnail_filename, $file['type'], $alt_text);
            $stmt->execute();

            log_activity('admin', $_SESSION['admin_id'], 'media_upload', "Uploaded file: {$original_filename}");
            $_SESSION['success_message'] = "File uploaded successfully.";
        } else {
            unlink($target_path); // Clean up if thumbnail creation failed
            $_SESSION['error_message'] = "Could not create thumbnail for the uploaded image.";
        }
    } else {
        $_SESSION['error_message'] = "Failed to move uploaded file.";
    }

    header('Location: /smartprozen/admin/media_library.php');
    exit;
}

/**
 * Handles the file deletion process.
 */
function handle_delete() {
    global $conn;

    if (!isset($_GET['id'])) {
        $_SESSION['error_message'] = "No media ID specified for deletion.";
        header('Location: /smartprozen/admin/media_library.php');
        exit;
    }

    $media_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT stored_filename, thumbnail_filename FROM media_library WHERE id = ?");
    $stmt->bind_param("i", $media_id);
    $stmt->execute();
    $media = $stmt->get_result()->fetch_assoc();

    if ($media) {
        $upload_dir = __DIR__ . '/../uploads/media/';
        $original_file = $upload_dir . $media['stored_filename'];
        $thumb_file = $upload_dir . $media['thumbnail_filename'];

        // Delete files from the server
        if (file_exists($original_file)) { unlink($original_file); }
        if (file_exists($thumb_file)) { unlink($thumb_file); }

        // Delete record from the database
        $delete_stmt = $conn->prepare("DELETE FROM media_library WHERE id = ?");
        $delete_stmt->bind_param("i", $media_id);
        $delete_stmt->execute();
        
        log_activity('admin', $_SESSION['admin_id'], 'media_delete', "Deleted file: {$media['stored_filename']}");
        $_SESSION['success_message'] = "Media file deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Media file not found in the database.";
    }

    header('Location: /smartprozen/admin/media_library.php');
    exit;
}

/**
 * Creates a resized thumbnail from a source image.
 *
 * @param string $source_path Path to the original image.
 * @param string $dest_path Path to save the new thumbnail.
 * @param int $max_width Maximum width of the thumbnail.
 * @param int $max_height Maximum height of the thumbnail.
 * @return bool True on success, false on failure.
 */
function create_thumbnail($source_path, $dest_path, $max_width, $max_height) {
    list($source_width, $source_height, $source_type) = getimagesize($source_path);

    switch ($source_type) {
        case IMAGETYPE_JPEG:
            $source_gd_image = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source_gd_image = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_GIF:
            $source_gd_image = imagecreatefromgif($source_path);
            break;
        default:
            return false;
    }

    if ($source_gd_image === false) {
        return false;
    }

    // Calculate thumbnail dimensions while maintaining aspect ratio
    $ratio = $source_width / $source_height;
    if ($max_width / $max_height > $ratio) {
        $max_width = $max_height * $ratio;
    } else {
        $max_height = $max_width / $ratio;
    }

    $thumb_gd_image = imagecreatetruecolor($max_width, $max_height);

    // Handle PNG transparency
    if ($source_type == IMAGETYPE_PNG) {
        imagealphablending($thumb_gd_image, false);
        imagesavealpha($thumb_gd_image, true);
        $transparent = imagecolorallocatealpha($thumb_gd_image, 255, 255, 255, 127);
        imagefilledrectangle($thumb_gd_image, 0, 0, $max_width, $max_height, $transparent);
    }

    imagecopyresampled($thumb_gd_image, $source_gd_image, 0, 0, 0, 0, $max_width, $max_height, $source_width, $source_height);

    // Save the thumbnail
    switch ($source_type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb_gd_image, $dest_path, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumb_gd_image, $dest_path, 9); // Compression level
            break;
        case IMAGETYPE_GIF:
            imagegif($thumb_gd_image, $dest_path);
            break;
    }

    imagedestroy($source_gd_image);
    imagedestroy($thumb_gd_image);

    return true;
}