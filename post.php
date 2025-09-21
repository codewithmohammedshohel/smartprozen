<?php
require_once 'config.php';
require_once 'core/functions.php'; // <-- FIX: Add missing include
require_once __DIR__ . '/includes/header.php';

if (!isset($_GET['slug'])) {
    http_response_code(404);
    $_SESSION['error_message'] = "Post not found.";
    header('Location: /smartprozen/index.php');
    exit;
}
$slug = $_GET['slug'];

$stmt = $conn->prepare("SELECT * FROM posts WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    http_response_code(404);
    $_SESSION['error_message'] = "Post not found.";
    header('Location: /smartprozen/index.php');
    exit;
}

$page_title = get_translated_text($post['title'], 'title');

// Fetch recent posts
$recent_posts_stmt = $conn->prepare("SELECT * FROM posts WHERE id != ? ORDER BY created_at DESC LIMIT 5");
$recent_posts_stmt->bind_param("i", $post['id']);
$recent_posts_stmt->execute();
$recent_posts = $recent_posts_stmt->get_result();

?>
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-5">
                    <header class="mb-4">
                        <h1 class="fw-bolder mb-1"><?php echo htmlspecialchars($page_title); ?></h1>
                        <div class="text-muted fst-italic mb-2">Posted on <?php echo date("F j, Y", strtotime($post['created_at'])); ?></div>
                    </header>
                    <section class="mb-5">
                        <p class="fs-5 mb-4"><?php echo nl2br(htmlspecialchars(get_translated_text($post['content'], 'content'))); ?></p>
                    </section>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">Recent Posts</div>
                <div class="list-group list-group-flush">
                    <?php while($recent_post = $recent_posts->fetch_assoc()): ?>
                        <a href="post.php?slug=<?php echo $recent_post['slug']; ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars(get_translated_text($recent_post['title'], 'title')); ?></h6>
                            </div>
                            <small class="text-muted"><?php echo date("F j, Y", strtotime($recent_post['created_at'])); ?></small>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>