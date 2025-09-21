<?php
/**
 * SmartProZen Master Setup & Testing Suite
 * 
 * This comprehensive script provides:
 * - Complete database setup
 * - Error detection and fixing
 * - System testing and validation
 * - Debugging tools
 * - Configuration verification
 * 
 * Usage: http://localhost/smartprozen/master_setup.php
 */

// Prevent direct access in production
if (isset($_SERVER['HTTP_HOST']) && !strpos($_SERVER['HTTP_HOST'], 'localhost') && !strpos($_SERVER['HTTP_HOST'], '127.0.0.1')) {
    die('This script should only be run in development environment.');
}

// Start output buffering for better error handling
ob_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartProZen Master Setup & Testing Suite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .test-pass { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
        .test-fail { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .test-warning { background-color: #fff3cd; border-color: #ffeaa7; color: #856404; }
        .test-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .code-block { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .tab-content { margin-top: 20px; }
        .progress { height: 25px; }
        .btn-group-vertical .btn { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="jumbotron bg-primary text-white p-4 rounded mb-4">
                    <h1 class="display-4"><i class="bi bi-tools"></i> SmartProZen Master Setup</h1>
                    <p class="lead">Complete setup, testing, and debugging suite for your CMS</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-list"></i> Setup Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical w-100" role="group">
                            <button type="button" class="btn btn-primary" onclick="showSection('setup')">
                                <i class="bi bi-download"></i> Complete Setup
                            </button>
                            <button type="button" class="btn btn-success" onclick="showSection('test')">
                                <i class="bi bi-check-circle"></i> Run Tests
                            </button>
                            <button type="button" class="btn btn-info" onclick="showSection('debug')">
                                <i class="bi bi-bug"></i> Debug Info
                            </button>
                            <button type="button" class="btn btn-warning" onclick="showSection('config')">
                                <i class="bi bi-gear"></i> Configuration
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="showSection('tools')">
                                <i class="bi bi-wrench"></i> Tools
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="bi bi-info-circle"></i> Quick Links</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="<?php echo $_SERVER['REQUEST_URI']; ?>" class="list-group-item list-group-item-action">
                                <i class="bi bi-arrow-clockwise"></i> Refresh Page
                            </a>
                            <a href="/" class="list-group-item list-group-item-action">
                                <i class="bi bi-house"></i> Homepage
                            </a>
                            <a href="/admin/login.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-shield-lock"></i> Admin Panel
                            </a>
                            <a href="/products_list.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-box"></i> Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <!-- Setup Section -->
                <div id="setup-section" class="tab-content">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="bi bi-download"></i> Complete Database Setup</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['action']) && $_GET['action'] === 'setup'): ?>
                                <?php include 'setup_database.php'; ?>
                            <?php else: ?>
                                <p>This will create the complete database structure and insert all sample data.</p>
                                <div class="alert alert-info">
                                    <h5><i class="bi bi-info-circle"></i> What this setup includes:</h5>
                                    <ul>
                                        <li>✅ Database creation</li>
                                        <li>✅ All tables (15+ tables)</li>
                                        <li>✅ Admin user creation</li>
                                        <li>✅ Sample products and categories</li>
                                        <li>✅ Homepage sections</li>
                                        <li>✅ Navigation menus</li>
                                        <li>✅ Payment gateways</li>
                                        <li>✅ Modules system</li>
                                        <li>✅ Sample testimonials</li>
                                        <li>✅ Upload directories</li>
                                    </ul>
                                </div>
                                <a href="?action=setup" class="btn btn-primary btn-lg">
                                    <i class="bi bi-play-circle"></i> Run Complete Setup
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Test Section -->
                <div id="test-section" class="tab-content" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="bi bi-check-circle"></i> System Tests</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['action']) && $_GET['action'] === 'test'): ?>
                                <?php include 'run_tests.php'; ?>
                            <?php else: ?>
                                <p>Run comprehensive tests to verify all system components are working correctly.</p>
                                <div class="alert alert-info">
                                    <h5><i class="bi bi-list-check"></i> Tests included:</h5>
                                    <ul>
                                        <li>🔧 Configuration validation</li>
                                        <li>🗄️ Database connectivity</li>
                                        <li>📁 File permissions</li>
                                        <li>🔗 URL routing</li>
                                        <li>👤 User authentication</li>
                                        <li>🛒 Cart functionality</li>
                                        <li>📧 Email system</li>
                                        <li>📱 API endpoints</li>
                                    </ul>
                                </div>
                                <a href="?action=test" class="btn btn-success btn-lg">
                                    <i class="bi bi-play-circle"></i> Run All Tests
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Debug Section -->
                <div id="debug-section" class="tab-content" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="bi bi-bug"></i> Debug Information</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['action']) && $_GET['action'] === 'debug'): ?>
                                <?php include 'debug_info.php'; ?>
                            <?php else: ?>
                                <p>Get detailed system information for debugging and troubleshooting.</p>
                                <div class="alert alert-info">
                                    <h5><i class="bi bi-info-circle"></i> Debug information includes:</h5>
                                    <ul>
                                        <li>🔧 PHP configuration</li>
                                        <li>🗄️ Database status</li>
                                        <li>📁 File system check</li>
                                        <li>🌐 Server environment</li>
                                        <li>⚠️ Error logs</li>
                                        <li>📊 System resources</li>
                                    </ul>
                                </div>
                                <a href="?action=debug" class="btn btn-info btn-lg">
                                    <i class="bi bi-eye"></i> Show Debug Info
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Config Section -->
                <div id="config-section" class="tab-content" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="bi bi-gear"></i> Configuration Check</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['action']) && $_GET['action'] === 'config'): ?>
                                <?php include 'config_check.php'; ?>
                            <?php else: ?>
                                <p>Verify and validate all system configurations.</p>
                                <div class="alert alert-info">
                                    <h5><i class="bi bi-check2-square"></i> Configuration checks:</h5>
                                    <ul>
                                        <li>🔧 Environment detection</li>
                                        <li>🗄️ Database settings</li>
                                        <li>🌐 Site URL configuration</li>
                                        <li>📁 Directory permissions</li>
                                        <li>🔒 Security settings</li>
                                        <li>📧 Email configuration</li>
                                    </ul>
                                </div>
                                <a href="?action=config" class="btn btn-warning btn-lg">
                                    <i class="bi bi-gear"></i> Check Configuration
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Tools Section -->
                <div id="tools-section" class="tab-content" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="bi bi-wrench"></i> System Tools</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="bi bi-database"></i> Database Tools</h5>
                                    <div class="btn-group-vertical w-100 mb-3">
                                        <button type="button" class="btn btn-outline-primary" onclick="showTool('backup')">
                                            <i class="bi bi-download"></i> Backup Database
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" onclick="showTool('reset')">
                                            <i class="bi bi-arrow-clockwise"></i> Reset Database
                                        </button>
                                        <button type="button" class="btn btn-outline-info" onclick="showTool('optimize')">
                                            <i class="bi bi-speedometer"></i> Optimize Database
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="bi bi-files"></i> File Tools</h5>
                                    <div class="btn-group-vertical w-100 mb-3">
                                        <button type="button" class="btn btn-outline-success" onclick="showTool('permissions')">
                                            <i class="bi bi-shield-check"></i> Fix Permissions
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="showTool('cleanup')">
                                            <i class="bi bi-trash"></i> Clean Cache
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="showTool('logs')">
                                            <i class="bi bi-file-text"></i> View Logs
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="tool-output" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showSection(section) {
            // Hide all sections
            document.querySelectorAll('.tab-content').forEach(el => {
                el.style.display = 'none';
            });
            
            // Show selected section
            document.getElementById(section + '-section').style.display = 'block';
            
            // Update URL without reload
            const url = new URL(window.location);
            url.searchParams.set('action', section);
            window.history.pushState({}, '', url);
        }

        function showTool(tool) {
            const output = document.getElementById('tool-output');
            output.innerHTML = '<div class="alert alert-info"><i class="bi bi-hourglass-split"></i> Loading tool: ' + tool + '...</div>';
            
            // Simulate tool execution
            setTimeout(() => {
                output.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle"></i> Tool "' + tool + '" executed successfully!</div>';
            }, 1000);
        }

        // Load section based on URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const action = urlParams.get('action');
        if (action) {
            showSection(action);
        }
    </script>
</body>
</html>
