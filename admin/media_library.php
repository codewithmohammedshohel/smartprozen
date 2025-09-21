<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_media')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

require_once '../includes/admin_header.php';
?>
<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Media Library</h1>
        </div>
        
        <?php show_flash_messages(); ?>

        <div class="form-container">
            <h2>Upload New Media</h2>
            <form action="/smartprozen/core/media_handler.php?action=upload" method="POST" enctype="multipart/form-data">
                <label for="media_file">Select Image (JPG, PNG, GIF):</label>
                <input type="file" name="media_file" id="media_file" required accept="image/jpeg,image/png,image/gif">
                
                <label for="alt_text">Alt Text (for SEO):</label>
                <input type="text" name="alt_text" id="alt_text" placeholder="Describe the image for search engines">
                
                <button type="submit" class="btn">Upload</button>
            </form>
        </div>

        <div class="media-grid-container">
            <h2>Existing Media</h2>
            <div class="media-grid">
                <?php
                $media_items = $conn->query("SELECT * FROM media_library ORDER BY uploaded_at DESC");
                while ($item = $media_items->fetch_assoc()):
                ?>
                <div class="media-item">
                    <img src="<?php echo SITE_URL . '/uploads/media/' . $item['thumbnail_filename']; ?>" 
                         alt="<?php echo htmlspecialchars($item['alt_text']); ?>" 
                         loading="lazy">
                    <p title="<?php echo htmlspecialchars($item['original_filename']); ?>">
                        <?php echo htmlspecialchars(substr($item['original_filename'], 0, 20)) . '...'; ?>
                    </p>
                    <div class="media-actions">
                        <a href="/smartprozen/core/media_handler.php?action=delete&id=<?php echo $item['id']; ?>" class="btn-danger-small" onclick="return confirm('Are you sure?')">Delete</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>