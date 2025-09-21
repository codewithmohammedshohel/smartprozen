<!-- Detailed Footer -->
<footer class="footer-detailed bg-dark text-white py-5 mt-auto">
    <div class="container">
        <div class="row g-4">
            <!-- Column 1: Shop -->
            <div class="col-md-6 col-lg-3">
                <h5 class="fw-bold mb-3">Shop</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">All Products</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">Smart Home</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">Audio</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">Accessories</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">Gift Cards</a></li>
                </ul>
            </div>

            <!-- Column 2: Customer Service -->
            <div class="col-md-6 col-lg-3">
                <h5 class="fw-bold mb-3">Customer Service</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/smartprozen/contact.php" class="text-white text-decoration-none footer-link">Contact Us</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">FAQ</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">Shipping Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">Return Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">Track Your Order</a></li>
                </ul>
            </div>

            <!-- Column 3: About SmartProZen -->
            <div class="col-md-6 col-lg-3">
                <h5 class="fw-bold mb-3">About SmartProZen</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">Our Story</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">Blog</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">Careers</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none footer-link">Press</a></li>
                </ul>
            </div>

            <!-- Column 4: Stay Connected -->
            <div class="col-md-6 col-lg-3">
                <h5 class="fw-bold mb-3">Stay Connected</h5>
                <div class="social-icons d-flex gap-3 mb-4">
                    <a href="#" class="social-icon fs-4 text-white"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon fs-4 text-white"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon fs-4 text-white"><i class="bi bi-tiktok"></i></a>
                    <a href="#" class="social-icon fs-4 text-white"><i class="bi bi-twitter-x"></i></a>
                </div>
                <h5 class="fw-bold mb-3">Accepted Payments</h5>
                <div class="payment-icons d-flex gap-2">
                    <i class="bi bi-credit-card-2-front fs-2"></i>
                    <!-- Placeholder for actual icons -->
                    <img src="https://placehold.co/40x25/white/black?text=VISA" alt="Visa">
                    <img src="https://placehold.co/40x25/white/black?text=MC" alt="Mastercard">
                    <img src="https://placehold.co/40x25/white/black?text=PayPal" alt="PayPal">
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="text-center text-muted">
            <p>&copy; <?php echo date("Y"); ?> SmartProZen. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<?php
// Include the floating WhatsApp icon if enabled
if (get_setting('whatsapp_number', $conn)) {
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
            navigator.serviceWorker.register('/smartprozen/sw.js').catch(err => {
                console.error('ServiceWorker registration failed: ', err);
            });
        });
    }
</script>

<?php require_once __DIR__ . '/whatsapp_icon.php'; ?>
<?php require_once __DIR__ . '/admin_bar.php'; ?>
</body>
</html>
