<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../core/db.php';
require_once __DIR__ . '/../../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_pages')) {
    exit('Unauthorized');
}

$section_id = $_GET['section_id'] ?? null;
$page_id = $_GET['page_id'] ?? null;
$section_data = [];

if ($section_id) {
    $stmt = $conn->prepare("SELECT content_json FROM page_sections WHERE id = ? AND page_id = ?");
    $stmt->bind_param("ii", $section_id, $page_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($section = $result->fetch_assoc()) {
        $section_data = json_decode($section['content_json'], true);
    }
    $stmt->close();
}

$title = $section_data['title']['en'] ?? '';
$subtitle = $section_data['subtitle']['en'] ?? '';
$image_id = $section_data['image_id'] ?? '';
$image_filename = $section_data['image_filename'] ?? '';
$button_text = $section_data['button_text']['en'] ?? '';
$button_link = $section_data['button_link'] ?? '';

?>

<form id="heroSectionForm">
    <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($section_id); ?>">
    <input type="hidden" name="page_id" value="<?php echo htmlspecialchars($page_id); ?>">
    <input type="hidden" name="section_type" value="hero">

    <div class="mb-3">
        <label for="heroTitle" class="form-label">Title (English)</label>
        <input type="text" class="form-control" id="heroTitle" name="content_json[title][en]" value="<?php echo htmlspecialchars($title); ?>">
    </div>

    <div class="mb-3">
        <label for="heroSubtitle" class="form-label">Subtitle (English)</label>
        <textarea class="form-control" id="heroSubtitle" name="content_json[subtitle][en]" rows="3"><?php echo htmlspecialchars($subtitle); ?></textarea>
    </div>

    <div class="mb-3">
        <label for="heroImage" class="form-label">Hero Image</label>
        <div class="input-group">
            <input type="text" id="heroImageFilename" class="form-control" value="<?php echo htmlspecialchars($image_filename); ?>" readonly>
            <input type="hidden" id="heroImageId" name="content_json[image_id]" value="<?php echo htmlspecialchars($image_id); ?>">
            <button type="button" class="btn btn-outline-secondary select-media-btn" data-target-id="heroImageId" data-target-filename="heroImageFilename" data-target-preview="heroImagePreview">Select Image</button>
        </div>
        <div id="heroImagePreview" class="mt-2">
            <?php if (!empty($image_filename)): ?>
                <img src="<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($image_filename); ?>" alt="Hero Image" style="max-height: 100px;">
            <?php endif; ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="heroButtonText" class="form-label">Button Text (English)</label>
        <input type="text" class="form-control" id="heroButtonText" name="content_json[button_text][en]" value="<?php echo htmlspecialchars($button_text); ?>">
    </div>

    <div class="mb-3">
        <label for="heroButtonLink" class="form-label">Button Link</label>
        <input type="text" class="form-control" id="heroButtonLink" name="content_json[button_link]" value="<?php echo htmlspecialchars($button_link); ?>">
    </div>
</form>

<script>
    document.querySelectorAll('.select-media-btn').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.targetId;
            const targetFilename = this.dataset.targetFilename;
            const targetPreview = this.dataset.targetPreview;

            // Store targets in a global/accessible way for the media library modal
            window.mediaLibraryTarget = {
                id: targetId,
                filename: targetFilename,
                preview: targetPreview
            };

            // Trigger the media library modal (assuming it's already in the DOM)
            const mediaLibraryModal = new bootstrap.Modal(document.getElementById('mediaLibraryModal'));
            // Load content into the modal
            fetch('media_library_modal.php')
                .then(response => response.text())
                .then(html => {
                    document.querySelector('#mediaLibraryModal .modal-content').innerHTML = html;
                    mediaLibraryModal.show();
                    // Re-initialize media library events for selection and upload
                    initializeMediaLibraryEventsForForms();
                })
                .catch(error => console.error('Error loading media library modal:', error));
        });
    });

    // This function needs to be defined globally or passed correctly
    // It's a simplified version of the one in settings.php
    function initializeMediaLibraryEventsForForms() {
        const mediaItems = document.querySelectorAll('.media-item');
        let selectedMediaItem = null;

        mediaItems.forEach(item => {
            item.addEventListener('click', function() {
                if (selectedMediaItem) {
                    selectedMediaItem.classList.remove('border', 'border-primary', 'border-3');
                }
                this.classList.add('border', 'border-primary', 'border-3');
                selectedMediaItem = this;
                document.getElementById('insertMediaBtn').disabled = false;
            });
        });

        document.getElementById('insertMediaBtn').addEventListener('click', function() {
            if (selectedMediaItem && window.mediaLibraryTarget) {
                const mediaId = selectedMediaItem.dataset.id;
                const filename = selectedMediaItem.dataset.filename;
                
                document.getElementById(window.mediaLibraryTarget.id).value = mediaId;
                document.getElementById(window.mediaLibraryTarget.filename).value = filename;
                document.getElementById(window.mediaLibraryTarget.preview).innerHTML = `<img src="<?php echo SITE_URL; ?>/uploads/media/${filename}" alt="Selected Image" style="max-height: 100px;">`;
                
                const mediaLibraryModal = bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal'));
                if (mediaLibraryModal) mediaLibraryModal.hide();
            }
        });

        // Handle Upload Tab within the modal
        const mediaUploadForm = document.getElementById('mediaUploadForm');
        if (mediaUploadForm) {
            mediaUploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                fetch('media_library_modal.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const uploadStatus = document.getElementById('uploadStatus');
                    if (data.success) {
                        uploadStatus.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                        // Optionally, refresh the browse tab or add the new item to it
                        // For simplicity, just alert and close for now, or reload the modal
                        // A more robust solution would add the item to the browse list
                        alert('Upload successful! Please select the newly uploaded image from the browse tab.');
                        // Reload the modal content to show the new image in browse tab
                        fetch('media_library_modal.php')
                            .then(response => response.text())
                            .then(html => {
                                document.querySelector('#mediaLibraryModal .modal-content').innerHTML = html;
                                initializeMediaLibraryEventsForForms(); // Re-init events
                            });
                    } else {
                        uploadStatus.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error uploading file:', error);
                    document.getElementById('uploadStatus').innerHTML = `<div class="alert alert-danger">An error occurred during upload.</div>`;
                });
            });
        }
    }
</script>