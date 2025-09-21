<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="card-title text-center mb-4"><?php echo htmlspecialchars($page_title); ?></h1>
                    <div class="page-content">
                        <?php echo nl2br(htmlspecialchars(get_translated_text($page['content'], 'content'))); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>