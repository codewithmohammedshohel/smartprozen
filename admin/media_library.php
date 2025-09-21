<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_media')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

require_once '../includes/admin_header.php';
require_once '../includes/admin_sidebar.php';
?>
<div class="container-fluid px-4">
    <div class="media-library-header">
        <h1 class="mt-4">Media Library</h1>
    </div>
    
    <?php show_flash_messages(); ?>

    <div class="upload-card">
        <h2>Upload New Media</h2>
        <form action="/smartprozen/core/media_handler.php?action=upload" method="POST" enctype="multipart/form-data">
            <div class="upload-drop-zone" id="drop-zone">
                <i class="bi bi-cloud-arrow-up"></i>
                <p>Drag & drop files here, or click to select files</p>
                <input type="file" name="media_files[]" id="media_file" class="d-none" multiple required accept="image/jpeg,image/png,image/gif">
            </div>
            <div class="mb-3 mt-3">
                <label for="alt_text" class="form-label">Alt Text (for SEO):</label>
                <input type="text" name="alt_text" id="alt_text" class="form-control" placeholder="Describe the image for search engines">
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>

    <div class="media-grid">
        <?php
        $media_items = $conn->query("SELECT * FROM media_library ORDER BY uploaded_at DESC");
        while ($item = $media_items->fetch_assoc()):
        ?>
        <div class="media-item-card">
            <img src="<?php echo SITE_URL . '/uploads/media/' . $item['thumbnail_filename']; ?>" 
                 alt="<?php echo htmlspecialchars($item['alt_text']); ?>" 
                 loading="lazy">
            <div class="card-body">
                <p class="card-title" title="<?php echo htmlspecialchars($item['original_filename']); ?>">
                    <?php echo htmlspecialchars($item['original_filename']); ?>
                </p>
                <div class="media-actions">
                    <button class="btn btn-sm btn-outline-secondary copy-url-btn" data-url="<?php echo SITE_URL . '/uploads/media/' . $item['filename']; ?>">
                        <i class="bi bi-clipboard"></i> Copy URL
                    </button>
                    <a href="/smartprozen/core/media_handler.php?action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('media_file');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.backgroundColor = '#f0f0f0';
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.style.backgroundColor = 'transparent';
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.backgroundColor = 'transparent';
        fileInput.files = e.dataTransfer.files;
    });

    const copyUrlButtons = document.querySelectorAll('.copy-url-btn');
    copyUrlButtons.forEach(button => {
        button.addEventListener('click', function() {
            const url = this.dataset.url;
            navigator.clipboard.writeText(url).then(() => {
                this.innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
                setTimeout(() => {
                    this.innerHTML = '<i class="bi bi-clipboard"></i> Copy URL';
                }, 2000);
            });
        });
    });
});
</script>

<?php require_once '../includes/admin_footer.php'; ?>