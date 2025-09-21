<?php
require_once "config.php";
require_once "core/db.php";
require_once "core/functions.php";

// Get page data from database
$slug = "terms-of-service";
$stmt = $conn->prepare("SELECT * FROM pages WHERE slug = ? AND is_published = 1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$page = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$page) {
    http_response_code(404);
    $page_title = "Page Not Found";
    include "includes/header.php";
    echo "<div class=\"container mt-5\"><div class=\"alert alert-danger\">The page you are looking for does not exist.</div></div>";
    include "includes/footer.php";
    exit;
}

$page_title = $page["title"];
$page_description = $page["meta_description"];

include "includes/header.php";
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1><?php echo htmlspecialchars($page["title"]); ?></h1>
            
            <?php
            // Fetch sections for this page
            $sections_stmt = $conn->prepare("SELECT * FROM page_sections WHERE page_id = ? ORDER BY display_order ASC");
            $sections_stmt->bind_param("i", $page["id"]);
            $sections_stmt->execute();
            $page_sections = $sections_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $sections_stmt->close();
            
            if (!empty($page_sections)) {
                foreach ($page_sections as $section) {
                    $section_data = json_decode($section["content_json"] ?? "{}", true) ?: [];
                    $section_type = $section["section_type"];
                    $section_template_path = __DIR__ . "/templates/sections/" . $section_type . ".php";
                    
                    if (file_exists($section_template_path)) {
                        include $section_template_path;
                    } else {
                        echo "<div class=\"container\"><div class=\"alert alert-warning\">Unknown section type: " . htmlspecialchars($section_type) . "</div></div>";
                    }
                }
            } else {
                // Show basic content if no sections
                $content = json_decode($page["content"], true);
                if (isset($content["en"])) {
                    echo "<div class=\"content\">";
                    echo "<p>" . htmlspecialchars($content["en"]) . "</p>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
