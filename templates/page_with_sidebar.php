<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-5">
                    <h1 class="card-title mb-4"><?php echo htmlspecialchars($page_title); ?></h1>
                    <div class="page-content">
                        <?php echo nl2br(htmlspecialchars(get_translated_text($page['content'], 'content'))); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">Sidebar</div>
                <div class="card-body">
                    <p>This is a sidebar. You can add widgets, navigation, or other content here.</p>
                </div>
            </div>
        </div>
    </div>
</div>