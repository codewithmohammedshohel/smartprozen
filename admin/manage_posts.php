<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

if (!is_admin_logged_in() || !has_permission('manage_posts')) {
    header('Location: /smartprozen/admin/login.php');
    exit;
}

$action = $_GET['action'] ?? 'list';

require_once '../includes/admin_header.php';
?>
<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h1 class="h4 mb-0">Manage Blog Posts</h1>
                <?php if ($action !== 'list'): ?>
                    <a href="manage_posts.php" class="btn btn-secondary btn-sm">Back to List</a>
                <?php else: ?>
                    <a href="manage_posts.php?action=add" class="btn btn-primary btn-sm">Add New Post</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php show_flash_messages(); ?>

        <?php if ($action === 'list'): 
            $posts = $conn->query("SELECT p.*, a.username FROM posts p JOIN admin_users a ON p.author_id = a.id ORDER BY p.created_at DESC");
        ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-file-post-fill me-1"></i>
                    All Blog Posts
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($post = $posts->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                                    <td><?php echo htmlspecialchars($post['username']); ?></td>
                                    <td><?php echo date("M j, Y", strtotime($post['created_at'])); ?></td>
                                    <td>
                                        <a href="manage_posts.php?action=edit&id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                        <a href="handle_post.php?action=delete&id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php elseif ($action === 'add' || $action === 'edit'): 
            $edit_post = null;
            if ($action === 'edit') {
                $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
                $stmt->bind_param("i", $_GET['id']);
                $stmt->execute();
                $edit_post = $stmt->get_result()->fetch_assoc();
            }
        ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-pencil-square me-1"></i>
                    <?php echo ($action === 'edit') ? 'Edit Post' : 'Add New Post'; ?>
                </div>
                <div class="card-body">
                    <form action="handle_post.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php echo $edit_post['id'] ?? ''; ?>">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title:</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($edit_post['title'] ?? ''); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content:</label>
                            <textarea id="content" name="content" class="form-control tinymce" rows="10"><?php echo htmlspecialchars($edit_post['content'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Post</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>