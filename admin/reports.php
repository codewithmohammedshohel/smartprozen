<?php
require_once '../config.php';
require_once '../core/db.php';
require_once '../core/functions.php';

// Authenticate and authorize admin
if (!is_admin_logged_in() || !has_permission('manage_reports')) { // Assumes 'manage_reports' permission
    header('Location: /smartprozen/admin/login.php');
    exit;
}

require_once '../includes/admin_header.php';
?>

<div class="dashboard-layout">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="container-fluid px-4">
    <h1 class="mt-4">Generate Reports</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Reports</li>
    </ol>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-file-earmark-bar-graph me-1"></i>
                Report Options
            </div>
            <div class="card-body">
                <p>Select a report type, format, and date range to download your data.</p>
                
                <form action="generate_report.php" method="POST" target="_blank">
                    
                    <div class="mb-3">
                        <label for="report_type" class="form-label"><strong>1. Select Report Type:</strong></label>
                        <select name="report_type" id="report_type" class="form-select" required>
                            <option value="sales">Sales & Revenue Report</option>
                            <option value="users">Customer List</option>
                            <option value="products">Product Inventory</option>
                            <option value="coupons">Coupon Usage Report</option>
                            <option value="activity_log">Admin Activity Log</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="format" class="form-label"><strong>2. Select Format:</strong></label>
                        <select name="format" id="format" class="form-select" required>
                            <option value="pdf">PDF Document</option>
                            <option value="excel">Excel Spreadsheet (XLSX)</option>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label"><strong>3. Start Date (Optional):</strong></label>
                            <input type="date" id="start_date" name="start_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label"><strong>4. End Date (Optional):</strong></label>
                            <input type="date" id="end_date" name="end_date" class="form-control">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="bi bi-bar-chart-fill me-2"></i> Generate Report</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>