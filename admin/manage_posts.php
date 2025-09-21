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
        <div class="dashboard-header">
            <h1>Manage Blog Posts</h1>
            <?php if ($action !== 'list'): ?>
                <a href="manage_posts.php" class="btn btn-secondary">Back to List</a>
            <?php else: ?>
                <a href="manage_posts.php?action=add" class="btn">Add New Post</a>
            <?php endif; ?>
        </div>
        
        <?php show_flash_messages(); ?>

        <?php if ($action === 'list'): 
            $posts = $conn->query("SELECT p.*, a.username FROM posts p JOIN admin_users a ON p.author_id = a.id ORDER BY p.created_at DESC");
        ?>
            <div class="table-container">
                <table>
                    <tbody>
                        <?php while($post = $posts->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(get_translated_text($post['title_json'], 'title')); ?></td>
                            <td><?php echo htmlspecialchars($post['username']); ?></td>
                            <td><?php echo date("M j, Y", strtotime($post['created_at'])); ?></td>
                            <td>
                                <a href="manage_posts.php?action=edit&id=<?php echo $post['id']; ?>" class="btn btn-secondary">Edit</a>
                                </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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
            <div class="form-container">
                <form action="handle_post.php" method="POST"> <input type="hidden" name="post_id" value="<?php echo $edit_post['id'] ?? ''; ?>">
                    
                    <label>Title (English):</label>
                    <input type="text" name="title_en" value="<?php echo htmlspecialchars(get_translated_text($edit_post['title_json'] ?? '', 'title_en')); ?>" required>
                    
                    <label>Title (Bangla):</label>
                    <input type="text" name="title_bn" value="<?php echo htmlspecialchars(get_translated_text($edit_post['title_json'] ?? '', 'title_bn')); ?>" required>

                    <label>Content (English):</label>
                    <textarea name="content_en" class="tinymce"><?php echo htmlspecialchars(get_translated_text($edit_post['content_json'] ?? '', 'content_en')); ?></textarea>
                    
                    <label>Content (Bangla):</label>
                    <textarea name="content_bn" class="tinymce"><?php echo htmlspecialchars(get_translated_text($edit_post['content_json'] ?? '', 'content_bn')); ?></textarea>
                    
                    <button type="submit" class="btn">Save Post</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once '../includes/admin_footer.php'; ?>