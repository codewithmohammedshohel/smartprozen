<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_settings')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle text settings
    $settings_to_update = ['business_name', 'whatsapp_number', 'maintenance_mode', 'maintenance_message', 'robots_txt', 'business_address', 'footer_text'];
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    foreach ($settings_to_update as $key) {
        $value = isset($_POST[$key]) ? $_POST[$key] : ($key === 'maintenance_mode' ? '0' : '');
        $stmt->bind_param("ss", $value, $key);
        $stmt->execute();
    }
    $stmt->close();

    // Handle logo upload from media library
    if (isset($_POST['business_logo_id']) && !empty($_POST['business_logo_id'])) {
        $logo_id = (int)$_POST['business_logo_id'];
        $logo_filename = null;
        $stmt = $conn->prepare("SELECT stored_filename FROM media_library WHERE id = ?");
        $stmt->bind_param("i", $logo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $logo_filename = $row['stored_filename'];
        }
        $stmt->close();

        // Update business_logo_id setting
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'business_logo_id'");
        $stmt->bind_param("s", $logo_id);
        $stmt->execute();
        $stmt->close();

        // Update business_logo_filename setting
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'business_logo_filename'");
        $stmt->bind_param("s", $logo_filename);
        $stmt->execute();
        $stmt->close();
    } else if (isset($_POST['business_logo_id']) && empty($_POST['business_logo_id'])) {
        // If logo is cleared, set settings to empty
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'business_logo_id'");
        $empty_val = '';
        $stmt->bind_param("s", $empty_val);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'business_logo_filename'");
        $stmt->bind_param("s", $empty_val);
        $stmt->execute();
        $stmt->close();
    }

    // Update robots.txt file
    file_put_contents(__DIR__ . '/../robots.txt', $_POST['robots_txt']);

    log_activity('admin', $_SESSION['admin_id'], 'settings_update', 'Updated site settings.');
    $_SESSION['success_message'] = "Settings updated successfully!";
    header("Location: settings.php");
    exit;
}

$settings = get_all_settings($conn);

// Ensure logo settings are available
$settings['business_logo_id'] = $settings['business_logo_id'] ?? '';
$settings['business_logo_filename'] = $settings['business_logo_filename'] ?? '';

require_once '../includes/admin_header.php';
?>
<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <h1>Site Settings</h1>
        <?php show_flash_messages(); ?>
        <form action="settings.php" method="POST">
                        <div class="mb-3">
                            <label for="business_name" class="form-label">Business Name</label>
                            <input type="text" id="business_name" name="business_name" class="form-control" value="<?php echo htmlspecialchars($settings['business_name'] ?? ''); ">
                        </div>

                        <div class="mb-3">
                            <label for="business_address" class="form-label">Business Address</label>
                            <textarea id="business_address" name="business_address" class="form-control" rows="3"><?php echo htmlspecialchars($settings['business_address'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="robots_txt" class="form-label">robots.txt Content</label>
                            <textarea id="robots_txt" name="robots_txt" class="form-control" rows="10"><?php echo htmlspecialchars($settings['robots_txt'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="footer_text" class="form-label">Footer Text</label>
                            <textarea id="footer_text" name="footer_text" class="form-control" rows="5"><?php echo htmlspecialchars($settings['footer_text'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="business_logo" class="form-label">Business Logo</label>
                            <div class="input-group">
                                <input type="text" id="business_logo_filename" class="form-control" value="<?php echo htmlspecialchars($settings['business_logo_filename'] ?? ''); ?>" readonly>
                                <input type="hidden" id="business_logo_id" name="business_logo_id" value="<?php echo htmlspecialchars($settings['business_logo_id'] ?? ''); ?>">
                                <button type="button" class="btn btn-outline-secondary" id="selectLogoBtn">Select from Media Library</button>
                            </div>
                            <div id="logoPreview" class="mt-2">
                                <?php if (!empty($settings['business_logo_filename'])): ?>
                                    <img src="<?php echo SITE_URL . '/uploads/media/' . htmlspecialchars($settings['business_logo_filename']); ?>" alt="Business Logo" style="max-height: 100px;">
                                <?php endif; ?>
                            </div>
                        </div>
            <button type="submit" class="btn">Save Settings</button>
        </form>
    </div>
</div>

<!-- Media Library Modal -->
<div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-labelledby="mediaLibraryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Content will be loaded here via AJAX -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mediaLibraryModal = new bootstrap.Modal(document.getElementById('mediaLibraryModal'));
    let currentTargetInputId = ''; // To track which input triggered the modal

    document.getElementById('selectLogoBtn').addEventListener('click', function() {
        currentTargetInputId = 'business_logo';
        loadMediaLibraryModal();
    });

    function loadMediaLibraryModal() {
        fetch('media_library_modal.php')
            .then(response => response.text())
            .then(html => {
                document.querySelector('#mediaLibraryModal .modal-content').innerHTML = html;
                mediaLibraryModal.show();
                initializeMediaLibraryEvents();
            })
            .catch(error => console.error('Error loading media library modal:', error));
    }

    function initializeMediaLibraryEvents() {
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
            if (selectedMediaItem) {
                const mediaId = selectedMediaItem.dataset.id;
                const filename = selectedMediaItem.dataset.filename;
                
                if (currentTargetInputId === 'business_logo') {
                    document.getElementById('business_logo_id').value = mediaId;
                    document.getElementById('business_logo_filename').value = filename;
                    document.getElementById('logoPreview').innerHTML = `<img src="<?php echo SITE_URL; ?>/uploads/media/${filename}" alt="Business Logo" style="max-height: 100px;">`;
                }
                // Add more conditions here for other media inputs if needed

                mediaLibraryModal.hide();
            }
        });

        // Handle Upload Tab
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
                        loadMediaLibraryModal(); // Reload modal to show new item
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
});
</script>

<?php require_once '../includes/admin_footer.php'; ?>