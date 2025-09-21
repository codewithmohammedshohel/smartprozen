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
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Generate Reports</h1>
        </div>

        <div class="form-container">
            <p>Select a report type, format, and date range to download your data.</p>
            
            <form action="generate_report.php" method="POST" target="_blank">
                
                <div class="form-group">
                    <label for="report_type"><strong>1. Select Report Type:</strong></label>
                    <select name="report_type" id="report_type" required>
                        <option value="sales">Sales & Revenue Report</option>
                        <option value="customers">Customer List</option>
                        <option value="products">Product Inventory</option>
                        <option value="coupons">Coupon Usage Report</option>
                        <option value="activity_log">Admin Activity Log</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="format"><strong>2. Select Format:</strong></label>
                    <select name="format" id="format" required>
                        <option value="pdf">PDF Document</option>
                        <option value="excel">Excel Spreadsheet (XLSX)</option>
                    </select>
                </div>

                <div class="form-group-row">
                    <div class="form-group">
                        <label for="start_date"><strong>3. Start Date (Optional):</strong></label>
                        <input type="date" id="start_date" name="start_date">
                    </div>
                    <div class="form-group">
                        <label for="end_date"><strong>4. End Date (Optional):</strong></label>
                        <input type="date" id="end_date" name="end_date">
                    </div>
                </div>
                
                <button type="submit" class="btn">📊 Generate Report</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>