<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_pages')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

// Get homepage
$homepage_stmt = $conn->prepare("SELECT * FROM pages WHERE slug = 'home' LIMIT 1");
$homepage_stmt->execute();
$homepage = $homepage_stmt->get_result()->fetch_assoc();

if (!$homepage) {
    // Create homepage if it doesn't exist
    $create_stmt = $conn->prepare("INSERT INTO pages (title, slug, content, template_slug, meta_title, meta_description, is_published, is_homepage) VALUES ('Home', 'home', '{}', 'default_page', 'SmartProZen - Smart Tech, Simplified Living', 'Discover our curated collection of smart gadgets and professional accessories designed to elevate your everyday.', 1, 1)");
    $create_stmt->execute();
    $homepage_id = $conn->insert_id;
} else {
    $homepage_id = $homepage['id'];
}

// Get homepage sections
$sections_stmt = $conn->prepare("SELECT * FROM page_sections WHERE page_id = ? ORDER BY display_order ASC");
$sections_stmt->bind_param("i", $homepage_id);
$sections_stmt->execute();
$sections = $sections_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get section templates
$templates_stmt = $conn->query("SELECT * FROM section_templates WHERE is_active = 1 ORDER BY category, name");
$templates = $templates_stmt->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_sidebar.php';
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Homepage Builder</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="previewHomepage()">
                <i class="bi bi-eye"></i> Preview
            </button>
            <button type="button" class="btn btn-sm btn-primary" onclick="publishHomepage()">
                <i class="bi bi-check-circle"></i> Publish Changes
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Section Builder -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Homepage Sections</h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                            <i class="bi bi-plus"></i> Add Section
                        </button>
                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                            <i class="bi bi-layout-text-sidebar"></i> From Template
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="homepageSections" class="sortable-sections">
                        <?php if (empty($sections)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-layout-text-sidebar-reverse fs-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No sections added yet</h5>
                                <p class="text-muted">Start building your homepage by adding sections</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($sections as $index => $section): ?>
                                <div class="section-item card mb-3" data-section-id="<?php echo $section['id']; ?>" data-order="<?php echo $section['display_order']; ?>">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-grip-vertical me-2 drag-handle" style="cursor: move;"></i>
                                            <h6 class="mb-0">
                                                <?php echo ucfirst(str_replace('_', ' ', $section['section_type'])); ?>
                                            </h6>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" onclick="editSection(<?php echo $section['id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="duplicateSection(<?php echo $section['id']; ?>)">
                                                <i class="bi bi-files"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteSection(<?php echo $section['id']; ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="section-preview">
                                            <?php
                                            $content = json_decode($section['content_json'], true);
                                            echo generateSectionPreview($section['section_type'], $content);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Homepage Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-0"><?php echo count($sections); ?></h4>
                                <small class="text-muted">Sections</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-0"><?php echo $homepage['is_published'] ? 'Live' : 'Draft'; ?></h4>
                            <small class="text-muted">Status</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addQuickSection('hero')">
                            <i class="bi bi-star"></i> Add Hero Section
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addQuickSection('featured_products')">
                            <i class="bi bi-box-seam"></i> Add Products
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addQuickSection('testimonials')">
                            <i class="bi bi-quote"></i> Add Testimonials
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addQuickSection('faq')">
                            <i class="bi bi-question-circle"></i> Add FAQ
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section Templates -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Section Templates</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($templates)): ?>
                        <?php foreach ($templates as $template): ?>
                            <div class="template-item border rounded p-2 mb-2 cursor-pointer" onclick="addFromTemplate(<?php echo $template['id']; ?>)">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($template['name']); ?></h6>
                                        <small class="text-muted"><?php echo ucfirst(str_replace('_', ' ', $template['section_type'])); ?></small>
                                    </div>
                                    <i class="bi bi-plus-circle text-primary"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">No templates available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addSectionForm">
                    <input type="hidden" name="page_id" value="<?php echo $homepage_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">Section Type</label>
                        <select class="form-select" name="section_type" required>
                            <option value="">Select section type...</option>
                            <option value="hero">Hero Section</option>
                            <option value="rich_text">Rich Text</option>
                            <option value="featured_products">Featured Products</option>
                            <option value="features">Features</option>
                            <option value="testimonials">Testimonials</option>
                            <option value="pricing">Pricing</option>
                            <option value="faq">FAQ</option>
                            <option value="custom_html">Custom HTML</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" name="display_order" value="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveNewSection()">Add Section</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Template Modal -->
<div class="modal fade" id="addTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Section from Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addTemplateForm">
                    <input type="hidden" name="page_id" value="<?php echo $homepage_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">Select Template</label>
                        <select class="form-select" name="template_id" required>
                            <option value="">Select template...</option>
                            <?php foreach ($templates as $template): ?>
                                <option value="<?php echo $template['id']; ?>">
                                    <?php echo htmlspecialchars($template['name']); ?> 
                                    (<?php echo ucfirst(str_replace('_', ' ', $template['section_type'])); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" name="display_order" value="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveTemplateSection()">Add Section</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sortable
    new Sortable(document.getElementById('homepageSections'), {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function(evt) {
            updateSectionOrder();
        }
    });
});

function generateSectionPreview(type, content) {
    // Generate preview based on section type
    switch(type) {
        case 'hero':
            return `<div class="hero-preview p-3 bg-primary text-white rounded">
                <h4>${content.title_en || 'Hero Title'}</h4>
                <p>${content.subtitle_en || 'Hero subtitle'}</p>
                <button class="btn btn-light btn-sm">${content.button_text_en || 'Call to Action'}</button>
            </div>`;
        case 'featured_products':
            return `<div class="products-preview p-3 border rounded">
                <h5>${content.title_en || 'Featured Products'}</h5>
                <div class="row">
                    <div class="col-3"><div class="bg-light rounded p-2">Product 1</div></div>
                    <div class="col-3"><div class="bg-light rounded p-2">Product 2</div></div>
                    <div class="col-3"><div class="bg-light rounded p-2">Product 3</div></div>
                    <div class="col-3"><div class="bg-light rounded p-2">Product 4</div></div>
                </div>
            </div>`;
        case 'testimonials':
            return `<div class="testimonials-preview p-3 border rounded">
                <h5>${content.title_en || 'Testimonials'}</h5>
                <div class="testimonial-item bg-light p-2 rounded">
                    <p class="mb-1">"Great product!"</p>
                    <small class="text-muted">- Customer Name</small>
                </div>
            </div>`;
        default:
            return `<div class="section-preview p-3 border rounded">
                <h6>${type.replace('_', ' ').toUpperCase()}</h6>
                <p class="text-muted mb-0">Section content preview</p>
            </div>`;
    }
}

function addQuickSection(type) {
    fetch('handle_section_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add_section&page_id=<?php echo $homepage_id; ?>&section_type=${type}&display_order=0`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function addFromTemplate(templateId) {
    fetch('handle_section_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add_from_template&page_id=<?php echo $homepage_id; ?>&template_id=${templateId}&display_order=0`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function saveNewSection() {
    const form = document.getElementById('addSectionForm');
    const formData = new FormData(form);
    formData.append('action', 'add_section');

    fetch('handle_section_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function saveTemplateSection() {
    const form = document.getElementById('addTemplateForm');
    const formData = new FormData(form);
    formData.append('action', 'add_from_template');

    fetch('handle_section_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function editSection(sectionId) {
    window.location.href = `page_builder.php?page_id=<?php echo $homepage_id; ?>&section_id=${sectionId}`;
}

function deleteSection(sectionId) {
    if (confirm('Are you sure you want to delete this section?')) {
        fetch('handle_section_actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_section&section_id=${sectionId}&page_id=<?php echo $homepage_id; ?>`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function duplicateSection(sectionId) {
    fetch('handle_section_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=duplicate_section&section_id=${sectionId}&page_id=<?php echo $homepage_id; ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function updateSectionOrder() {
    const sections = document.querySelectorAll('.section-item');
    const order = [];
    
    sections.forEach((section, index) => {
        order.push({
            id: section.dataset.sectionId,
            order: index
        });
    });

    fetch('handle_section_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'reorder_sections',
            page_id: <?php echo $homepage_id; ?>,
            section_orders: order
        })
    });
}

function previewHomepage() {
    window.open('/smartprozen/?preview=1', '_blank');
}

function publishHomepage() {
    fetch('handle_section_actions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=publish_page&page_id=<?php echo $homepage_id; ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Homepage published successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
