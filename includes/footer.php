<?php include __DIR__ . '/customizable_footer.php'; ?>

<?php
// Include the floating WhatsApp icon if enabled
$whatsapp_enabled = false;
try {
    $whatsapp_number = get_setting('whatsapp_number', '', $conn);
    $whatsapp_enabled = !empty($whatsapp_number);
} catch (Exception $e) {
    $whatsapp_enabled = false;
}

if ($whatsapp_enabled) {
    require_once 'whatsapp_icon.php';
}
?>

<!-- Essential JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>

<!-- Custom & Performance Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50,
            });
        }
    });
</script>

<!-- PWA Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?php echo SITE_URL; ?>/sw.js').catch(err => {
                console.error('ServiceWorker registration failed: ', err);
            });
        });
    }
</script>

<?php require_once __DIR__ . '/whatsapp_icon.php'; ?>
<?php require_once __DIR__ . '/admin_bar.php'; ?>
</body>
</html>
