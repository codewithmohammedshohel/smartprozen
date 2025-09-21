<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_pages')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $slug = slugify($_POST['slug']); // Use a slugify function
    $template_slug = $_POST['template_slug'] ?? 'default_page';
    $page_id = $_POST['page_id'] ?? null;

    // Convert title to JSON for multilingual support
    $title_json = json_encode(['en' => $title, 'bn' => $title]); // Assuming English and Bengali
    $empty_content_json = '{}'; // Content will be managed via sections

    if ($page_id) {
        $stmt = $conn->prepare("UPDATE pages SET title = ?, slug = ?, content = ?, template_slug = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $title_json, $slug, $empty_content_json, $template_slug, $page_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO pages (title, slug, content, template_slug) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title_json, $slug, $empty_content_json, $template_slug);
    }
    $stmt->execute();
    log_activity('admin', $_SESSION['admin_id'], 'page_save', "Saved page: $title");
    $_SESSION['success_message'] = "Page saved successfully.";
    header('Location: manage_pages.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM pages WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    log_activity('admin', $_SESSION['admin_id'], 'page_delete', "Deleted page ID: {$_GET['delete']}");
    $_SESSION['success_message'] = "Page deleted.";
    header('Location: manage_pages.php');
    exit;
}

$pages = $conn->query("SELECT * FROM pages ORDER BY title ASC");
$edit_page = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit_page = $stmt->get_result()->fetch_assoc();
    // Decode JSON for editing
    if ($edit_page) {
        $edit_page['title'] = json_decode($edit_page['title'], true)['en'] ?? ''; // Assuming English

        // Fetch sections for this page
        $sections_stmt = $conn->prepare("SELECT * FROM page_sections WHERE page_id = ? ORDER BY display_order ASC");
        $sections_stmt->bind_param("i", $edit_page['id']);
        $sections_stmt->execute();
        $page_sections = $sections_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $sections_stmt->close();
    }
}

// List available templates
$available_templates = [
    'default_page' => 'Default Page',
    'page_with_sidebar' => 'Page with Sidebar',
];

require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';
?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Manage Pages</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Manage Pages</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-file-earmark-fill me-1"></i>
                    <?php echo $edit_page ? 'Edit Page' : 'Add New Page'; ?>
                </div>
                <div class="card-body">
                    <form action="manage_pages.php" method="POST">
                        <input type="hidden" name="page_id" value="<?php echo $edit_page['id'] ?? ''; ?>">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Page Title (English)</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($edit_page['title'] ?? ''); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">URL Slug (e.g., about-us)</label>
                            <input type="text" id="slug" name="slug" class="form-control" value="<?php echo htmlspecialchars($edit_page['slug'] ?? ''); ?>" required>
                        </div>



                        <div class="mb-3">
                            <label for="template_slug" class="form-label">Page Template</label>
                            <select id="template_slug" name="template_slug" class="form-select">
                                <?php foreach ($available_templates as $slug => $name): ?>
                                    <option value="<?php echo $slug; ?>" <?php echo (isset($edit_page['template_slug']) && $edit_page['template_slug'] === $slug) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary"><?php echo $edit_page ? 'Update Page' : 'Add Page'; ?></button>
                        <?php if ($edit_page): ?>
                            <a href="manage_pages.php" class="btn btn-secondary">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if ($edit_page): // Only show sections if editing an existing page ?>
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-layout-text-sidebar-reverse me-1"></i>
                    Page Sections
                </div>
                <div class="card-body">
                    <div id="pageSectionsList" class="list-group mb-3">
                        <?php if (!empty($page_sections)): ?>
                            <?php foreach ($page_sections as $section): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center" data-section-id="<?php echo $section['id']; ?>" data-section-type="<?php echo htmlspecialchars($section['section_type']); ?>">
                                    <div>
                                        <h5 class="mb-1">Section Type: <?php echo htmlspecialchars($section['section_type']); ?></h5>
                                        <small>Order: <?php echo htmlspecialchars($section['display_order']); ?></small>
                                        <!-- Add a summary of content here later -->
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-info me-2 edit-section-btn" data-section-id="<?php echo $section['id']; ?>" data-section-type="<?php echo htmlspecialchars($section['section_type']); ?>">Edit</button>
                                        <button type="button" class="btn btn-sm btn-danger delete-section-btn" data-section-id="<?php echo $section['id']; ?>">Delete</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center">No sections added to this page yet.</p>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addSectionModal">Add New Section</button>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addTemplateSectionModal">Add Section from Template</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-collection-fill me-1"></i>
                    Existing Pages
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Template</th>
                                    <th>Link</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($page = $pages->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(json_decode($page['title'], true)['en'] ?? ''); ?></td>
                                        <td>/<?php echo htmlspecialchars($page['slug']); ?></td>
                                        <td><?php echo htmlspecialchars($available_templates[$page['template_slug']] ?? 'Default'); ?></td>
                                        <td><a href="<?php echo SITE_URL . '/page.php?slug=' . $page['slug']; ?>" target="_blank">View Page</a></td>
                                        <td>
                                            <a href="manage_pages.php?edit=<?php echo $page['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="manage_pages.php?delete=<?php echo $page['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this page?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add New Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSectionModalLabel">Add New Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSectionForm">
                    <input type="hidden" name="page_id" value="<?php echo $edit_page['id'] ?? ''; ?>">
                    <input type="hidden" name="action" value="add_new_section">
                    <div class="mb-3">
                        <label for="sectionType" class="form-label">Section Type</label>
                        <select class="form-select" id="sectionType" name="section_type" required>
                            <option value="rich_text">Rich Text</option>
                            <option value="hero">Hero Section</option>
                            <option value="featured_products">Featured Products</option>
                            <option value="faq">FAQ Section</option>
                            <!-- Add more section types as needed -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="displayOrder" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="displayOrder" name="display_order" value="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveNewSection">Add Section</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Section from Template Modal -->
<div class="modal fade" id="addTemplateSectionModal" tabindex="-1" aria-labelledby="addTemplateSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTemplateSectionModalLabel">Add Section from Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTemplateSectionForm">
                    <input type="hidden" name="page_id" value="<?php echo $edit_page['id'] ?? ''; ?>">
                    <input type="hidden" name="action" value="add_section_from_template">
                    <div class="mb-3">
                        <label for="templateSelect" class="form-label">Select Template</label>
                        <select class="form-select" id="templateSelect" name="template_id" required>
                            <!-- Options will be loaded dynamically via AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="templateDisplayOrder" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="templateDisplayOrder" name="display_order" value="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveTemplateSection">Add Section</button>
            </div>
        </div>
    </div>
</div>

<!-- Generic Section Edit Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" aria-labelledby="editSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSectionModalLabel">Edit Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Section-specific form will be loaded here via AJAX -->
                <div id="sectionEditFormContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSectionChanges">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Generic Section Edit Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" aria-labelledby="editSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSectionModalLabel">Edit Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Section-specific form will be loaded here via AJAX -->
                <div id="sectionEditFormContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSectionChanges">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pageId = document.querySelector('input[name="page_id"]').value;
    const addSectionModal = new bootstrap.Modal(document.getElementById('addSectionModal'));
    const addTemplateSectionModal = new bootstrap.Modal(document.getElementById('addTemplateSectionModal'));
    const editSectionModal = new bootstrap.Modal(document.getElementById('editSectionModal'));
    const pageSectionsList = document.getElementById('pageSectionsList');

    // Function to refresh section list
    function refreshSections() {
        if (!pageId) return; // Cannot refresh sections if no page ID

        fetch(`manage_pages.php?edit=${pageId}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newSectionList = doc.getElementById('pageSectionsList');
                if (newSectionList) {
                    pageSectionsList.innerHTML = newSectionList.innerHTML;
                    initializeSectionEvents(); // Re-initialize events for new elements
                }
            })
            .catch(error => console.error('Error refreshing sections:', error));
    }

    // Initialize events for section buttons (edit, delete)
    function initializeSectionEvents() {
        document.querySelectorAll('.edit-section-btn').forEach(button => {
            button.addEventListener('click', function() {
                const sectionId = this.dataset.sectionId;
                const sectionType = this.dataset.sectionType;
                loadSectionEditForm(sectionId, sectionType);
            });
        });

        document.querySelectorAll('.delete-section-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this section?')) {
                    const sectionId = this.dataset.sectionId;
                    deleteSection(sectionId);
                }
            });
        });

        // Make sections sortable (drag and drop)
        if (pageSectionsList) {
            new Sortable(pageSectionsList, {
                animation: 150,
                ghostClass: 'blue-background-class',
                onEnd: function (evt) {
                    const order = [];
                    Array.from(pageSectionsList.children).forEach((item, index) => {
                        if (item.dataset.sectionId) { // Ensure it's a section item
                            order.push({
                                id: item.dataset.sectionId,
                                order: index
                            });
                        }
                    });
                    reorderSections(order);
                },
            });
        }
    }

    // Load section edit form into modal
    function loadSectionEditForm(sectionId, sectionType) {
        fetch(`section_forms/${sectionType}_form.php?section_id=${sectionId}&page_id=${pageId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('sectionEditFormContent').innerHTML = html;
                editSectionModal.show();
            })
            .catch(error => console.error('Error loading section edit form:', error));
    }

    // Handle "Add New Section" button click
    document.querySelector('.btn-success[data-bs-target="#addSectionModal"]').addEventListener('click', function() {
        addSectionModal.show();
    });

    // Handle "Add Section from Template" button click
    document.querySelector('.btn-secondary[data-bs-target="#addTemplateSectionModal"]').addEventListener('click', function() {
        // Fetch templates dynamically
        fetch('handle_section_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=get_templates`
        })
        .then(response => response.json())
        .then(data => {
            const templateSelect = document.getElementById('templateSelect');
            templateSelect.innerHTML = '';
            if (data.success && data.templates.length > 0) {
                data.templates.forEach(template => {
                    const option = document.createElement('option');
                    option.value = template.id;
                    option.textContent = template.name;
                    templateSelect.appendChild(option);
                });
            } else {
                templateSelect.innerHTML = '<option value="">No templates found</option>';
            }
            addTemplateSectionModal.show();
        })
        .catch(error => console.error('Error fetching templates:', error));
    });

    // Handle saving new section
    document.getElementById('saveNewSection').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('addSectionForm'));
        formData.append('page_id', pageId); // Ensure page_id is always sent

        fetch('handle_section_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                addSectionModal.hide();
                refreshSections();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error adding new section:', error));
    });

    // Handle saving section from template
    document.getElementById('saveTemplateSection').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('addTemplateSectionForm'));
        formData.append('page_id', pageId); // Ensure page_id is always sent

        fetch('handle_section_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                addTemplateSectionModal.hide();
                refreshSections();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error adding section from template:', error));
    });

    // Handle saving section changes (from edit modal)
    document.getElementById('saveSectionChanges').addEventListener('click', function() {
        const form = document.querySelector('#sectionEditFormContent form');
        if (!form) return;

        const formData = new FormData(form);
        formData.append('action', 'update_section_content');
        formData.append('page_id', pageId); // Ensure page_id is always sent

        fetch('handle_section_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                editSectionModal.hide();
                refreshSections();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error updating section content:', error));
    });

    // Function to delete a section
    function deleteSection(sectionId) {
        fetch('handle_section_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=delete_section&section_id=${sectionId}&page_id=${pageId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                refreshSections();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error deleting section:', error));
    }

    // Function to reorder sections
    function reorderSections(order) {
        fetch('handle_section_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }, // Use JSON for reorder
            body: JSON.stringify({ action: 'reorder_sections', page_id: pageId, section_orders: order })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                refreshSections(); // Refresh to update display_order numbers
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error reordering sections:', error));
    }

    // Initial call to set up events if sections are already loaded
    initializeSectionEvents();
});
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; 