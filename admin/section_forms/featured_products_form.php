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

$selected_product_ids = $section_data['product_ids'] ?? [];

// Fetch all products for selection
$all_products = [];
$products_query = $conn->query("SELECT id, name FROM products ORDER BY name ASC");
if ($products_query) {
    while ($row = $products_query->fetch_assoc()) {
        $all_products[] = $row;
    }
}

?>

<form id="featuredProductsSectionForm">
    <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($section_id); ?>">
    <input type="hidden" name="page_id" value="<?php echo htmlspecialchars($page_id); ?>">
    <input type="hidden" name="section_type" value="featured_products">

    <div class="mb-3">
        <label for="productSelect" class="form-label">Select Products (Hold Ctrl/Cmd to select multiple)</label>
        <select class="form-select" id="productSelect" name="content_json[product_ids][]" multiple size="10">
            <?php foreach ($all_products as $product): ?>
                <option value="<?php echo $product['id']; ?>" <?php echo in_array($product['id'], $selected_product_ids) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars(get_translated_text($product['name'], 'name')); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</form>