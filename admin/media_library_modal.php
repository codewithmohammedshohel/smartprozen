<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (!is_admin_logged_in() || !has_permission('media_library')) {
    // Handle unauthorized access, perhaps return an empty modal or an error message
    exit;
}

// Handle image upload via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_file'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];

    $upload_dir = __DIR__ . '/../uploads/media/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = $_FILES['media_file']['name'];
    $file_tmp_name = $_FILES['media_file']['tmp_name'];
    $file_type = mime_content_type($file_tmp_name);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file_type, $allowed_types)) {
        $response['message'] = 'Invalid file type. Only JPG, PNG, GIF, WEBP are allowed.';
        echo json_encode($response);
        exit;
    }

    $new_file_name = uniqid('media_') . '.' . $file_ext;
    $destination = $upload_dir . $new_file_name;

    if (move_uploaded_file($file_tmp_name, $destination)) {
        // Insert into media_library
        $stmt = $conn->prepare("INSERT INTO media_library (original_filename, stored_filename, thumbnail_filename, file_type, alt_text, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
        // For simplicity, thumbnail_filename is same as stored_filename for now. Alt text is empty.
        $alt_text = ''; // Can be added later via editing
        $uploaded_by = $_SESSION['admin_id'] ?? null;
        $stmt->bind_param("sssssi", $file_name, $new_file_name, $new_file_name, $file_type, $alt_text, $uploaded_by);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'File uploaded successfully.';
            $response['media_item'] = [
                'id' => $stmt->insert_id,
                'original_filename' => $file_name,
                'stored_filename' => $new_file_name,
                'thumbnail_filename' => $new_file_name,
                'file_type' => $file_type,
                'alt_text' => $alt_text
            ];
        } else {
            $response['message'] = 'Failed to save file info to database.';
            unlink($destination); // Delete uploaded file if DB insert fails
        }
        $stmt->close();
    } else {
        $response['message'] = 'Failed to move uploaded file.';
    }
    echo json_encode($response);
    exit;
}

// Fetch media items for display
$media_items = [];
$media_query = $conn->query("SELECT id, original_filename, stored_filename, thumbnail_filename, file_type, alt_text FROM media_library ORDER BY created_at DESC");
if ($media_query) {
    while ($row = $media_query->fetch_assoc()) {
        $media_items[] = $row;
    }
}

?>
<div class="modal-header">
    <h5 class="modal-title" id="mediaLibraryModalLabel">Media Library</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <ul class="nav nav-tabs" id="mediaTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="browse-tab" data-bs-toggle="tab" data-bs-target="#browse" type="button" role="tab" aria-controls="browse" aria-selected="true">Browse</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab" aria-controls="upload" aria-selected="false">Upload</button>
        </li>
    </ul>
    <div class="tab-content" id="mediaTabContent">
        <div class="tab-pane fade show active" id="browse" role="tabpanel" aria-labelledby="browse-tab">
            <div class="row mt-3">
                <?php if (!empty($media_items)): ?>
                    <?php foreach ($media_items as $item): ?>
                        <div class="col-md-3 mb-3">
                            <div class="card media-item" data-id="<?php echo $item['id']; ?>" data-filename="<?php echo htmlspecialchars($item['stored_filename']); ?>">
                                <img src="<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($item['thumbnail_filename']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['alt_text']); ?>">
                                <div class="card-body p-2">
                                    <small class="card-title text-truncate d-block"><?php echo htmlspecialchars($item['original_filename']); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">No media items found. Upload some!</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
            <form id="mediaUploadForm" class="mt-3" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="mediaFile" class="form-label">Select File</label>
                    <input class="form-control" type="file" id="mediaFile" name="media_file" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
            <div id="uploadStatus" class="mt-3"></div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" id="insertMediaBtn" disabled>Insert Selected</button>
</div>