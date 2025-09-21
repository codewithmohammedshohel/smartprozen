<?php
// Customizable Footer Component
// This footer adapts based on theme settings and menu configuration

// Get theme settings
$theme_settings = [];
$settings_result = $conn->query("SELECT setting_key, setting_value FROM settings WHERE category = 'theme'");
while ($row = $settings_result->fetch_assoc()) {
    $theme_settings[$row['setting_key']] = $row['setting_value'];
}

// Get footer menu
$footer_menu = null;
$menu_result = $conn->query("SELECT menu_items FROM menus WHERE location = 'footer' AND is_active = 1 LIMIT 1");
if ($menu_result && $menu_result->num_rows > 0) {
    $footer_menu = json_decode($menu_result->fetch_assoc()['menu_items'], true);
}

// Get site settings
$site_name = get_setting('site_name', 'SmartProZen', $conn);
$site_description = get_setting('site_description', 'Smart Tech, Simplified Living', $conn);
$contact_email = get_setting('contact_email', 'info@smartprozen.com', $conn);
$contact_phone = get_setting('contact_phone', '+1 (555) 123-4567', $conn);
$contact_address = get_setting('contact_address', '123 Tech Street, Innovation City, IC 12345', $conn);

// Footer layout
$footer_layout = $theme_settings['footer_layout'] ?? 'default';
$primary_color = $theme_settings['primary_color'] ?? '#007bff';
?>

<footer class="footer-<?php echo $footer_layout; ?> bg-dark text-light">
    <div class="container">
        <?php if ($footer_layout === 'minimal'): ?>
            <!-- Minimal Footer -->
            <div class="row py-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo SITE_URL . get_setting('site_logo', '/uploads/logos/logo.png', $conn); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" height="30" class="me-2">
                        <span class="fw-bold"><?php echo htmlspecialchars($site_name); ?></span>
                    </div>
                    <p class="text-muted mb-0 mt-2"><?php echo htmlspecialchars($site_description ?? 'Smart Tech, Simplified Living'); ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. All rights reserved.</p>
                </div>
            </div>
        <?php else: ?>
            <!-- Default Footer -->
            <div class="row py-5">
                <!-- Company Info -->
                <div class="col-lg-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo SITE_URL . get_setting('site_logo', '/uploads/logos/logo.png', $conn); ?>" alt="<?php echo htmlspecialchars($site_name); ?>" height="40" class="me-2">
                        <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($site_name); ?></h5>
                    </div>
                    <p class="text-muted mb-3"><?php echo htmlspecialchars($site_description ?? 'Smart Tech, Simplified Living'); ?></p>
                    
                    <!-- Social Links -->
                    <div class="social-links">
                        <a href="#" class="text-light me-3" title="Facebook">
                            <i class="bi bi-facebook fs-5"></i>
                        </a>
                        <a href="#" class="text-light me-3" title="Twitter">
                            <i class="bi bi-twitter fs-5"></i>
                        </a>
                        <a href="#" class="text-light me-3" title="Instagram">
                            <i class="bi bi-instagram fs-5"></i>
                        </a>
                        <a href="#" class="text-light me-3" title="LinkedIn">
                            <i class="bi bi-linkedin fs-5"></i>
                        </a>
                        <a href="#" class="text-light" title="YouTube">
                            <i class="bi bi-youtube fs-5"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <?php if ($footer_menu && is_array($footer_menu)): ?>
                            <?php foreach ($footer_menu as $item): ?>
                                <li class="mb-2">
                                    <a href="<?php echo htmlspecialchars($item['url']); ?>" class="text-muted text-decoration-none">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Customer Service -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Customer Service</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/page/shipping" class="text-muted text-decoration-none">Shipping Info</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/page/returns" class="text-muted text-decoration-none">Returns</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/contact.php" class="text-muted text-decoration-none">Contact Us</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/page/faq" class="text-muted text-decoration-none">FAQ</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/page/support" class="text-muted text-decoration-none">Support</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Contact Information</h6>
                    <div class="contact-info">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt me-2" style="color: var(--primary-color);"></i>
                            <span class="text-muted"><?php echo htmlspecialchars($contact_address ?? '123 Tech Street, Innovation City, IC 12345'); ?></span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-telephone me-2" style="color: var(--primary-color);"></i>
                            <a href="tel:<?php echo htmlspecialchars($contact_phone ?? '+1 (555) 123-4567'); ?>" class="text-muted text-decoration-none">
                                <?php echo htmlspecialchars($contact_phone ?? '+1 (555) 123-4567'); ?>
                            </a>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-envelope me-2" style="color: var(--primary-color);"></i>
                            <a href="mailto:<?php echo htmlspecialchars($contact_email ?? 'info@smartprozen.com'); ?>" class="text-muted text-decoration-none">
                                <?php echo htmlspecialchars($contact_email ?? 'info@smartprozen.com'); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Newsletter Signup -->
                    <div class="newsletter-signup mt-4">
                        <h6 class="fw-bold mb-2">Newsletter</h6>
                        <p class="text-muted small mb-3">Subscribe to get updates and exclusive offers</p>
                        <form class="d-flex" action="<?php echo SITE_URL; ?>/newsletter/subscribe.php" method="POST">
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="Your email" required>
                            <button type="submit" class="btn btn-primary btn-sm ms-2">Subscribe</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-top border-secondary pt-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="text-muted mb-0">
                            &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_name); ?>. All rights reserved.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="payment-methods">
                            <span class="text-muted me-3">We Accept:</span>
                            <i class="bi bi-credit-card-2-front me-2" title="Credit Cards"></i>
                            <i class="bi bi-paypal me-2" title="PayPal"></i>
                            <i class="bi bi-apple me-2" title="Apple Pay"></i>
                            <i class="bi bi-google me-2" title="Google Pay"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="btn btn-primary position-fixed bottom-0 end-0 m-3 rounded-circle" style="display: none; z-index: 1000;">
    <i class="bi bi-arrow-up"></i>
</button>

<style>
.footer-default {
    background: #212529;
}

.footer-minimal {
    background: #212529;
    border-top: 1px solid #495057;
}

.social-links a {
    transition: color 0.3s ease;
}

.social-links a:hover {
    color: var(--primary-color) !important;
}

#backToTop {
    width: 50px;
    height: 50px;
    transition: all 0.3s ease;
}

#backToTop:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.contact-info a:hover {
    color: var(--primary-color) !important;
}

.payment-methods i {
    font-size: 1.2em;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.payment-methods i:hover {
    opacity: 1;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .footer-default .row {
        text-align: center;
    }
    
    .footer-default .col-md-6.text-md-end {
        text-align: center !important;
        margin-top: 20px;
    }
}
</style>

<script>
// Back to top functionality
document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('backToTop');
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.style.display = 'none';
        }
    });
    
    // Smooth scroll to top
    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});

// Newsletter signup
document.querySelectorAll('form[action*="newsletter"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const email = formData.get('email');
        
        if (!email) {
            alert('Please enter your email address.');
            return;
        }
        
        fetch('<?php echo SITE_URL; ?>/newsletter/subscribe.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Thank you for subscribing to our newsletter!');
                this.reset();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong. Please try again.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
        });
    });
});
</script>

<?php
// Include custom footer code if set
$footer_code = $theme_settings['footer_code'] ?? '';
if (!empty($footer_code)) {
    echo $footer_code;
}
?>
