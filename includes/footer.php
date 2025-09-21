</div>
</main>
<footer class="bg-dark text-white py-5 mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                <?php 
                $business_name_setting = get_setting('business_name', $conn);
                $business_name_display = get_translated_text($business_name_setting, 'business_name') ?? 'SmartProZen';
                ?>
                <p class="mb-0">&copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($business_name_display); ?>. All Rights Reserved.</p>
                <?php 
                $footer_text_setting = get_setting('footer_text', $conn);
                $footer_text_display = get_translated_text($footer_text_setting, 'footer_text');
                if ($footer_text_display): ?>
                    <p class="small mt-2"><?php echo nl2br(htmlspecialchars($footer_text_display)); ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-center mb-3 mb-md-0">
                <h5 class="text-white mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo SITE_URL; ?>/page.php?slug=about-us" class="text-white text-decoration-none">About Us</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/contact.php" class="text-white text-decoration-none">Contact Us</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/page.php?slug=privacy-policy" class="text-white text-decoration-none">Privacy Policy</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/page.php?slug=terms-conditions" class="text-white text-decoration-none">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="col-md-4 text-center text-md-end">
                <h5 class="text-white mb-3">Follow Us</h5>
                <div class="social-icons">
                    <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-4"></i></a>
                    <a href="#" class="text-white me-3"><i class="bi bi-twitter fs-4"></i></a>
                    <a href="#" class="text-white me-3"><i class="bi bi-instagram fs-4"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-linkedin fs-4"></i></a>
                </div>
                <?php 
                $business_address_setting = get_setting('business_address', $conn);
                $business_address_display = get_translated_text($business_address_setting, 'business_address');
                if ($business_address_display): ?>
                    <p class="small mt-3 mb-0">Address: <?php echo nl2br(htmlspecialchars($business_address_display)); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>

<?php
// Include the floating WhatsApp icon
require_once 'whatsapp_icon.php';
?>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize the animation library
    AOS.init({
        duration: 800, // values from 0 to 3000, with step 50ms
        once: true,     // whether animation should happen only once - while scrolling down
    });
</script>

</body>
</html>