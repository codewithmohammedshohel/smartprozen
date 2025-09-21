<?php
// Set a higher time limit for potentially long report generation
set_time_limit(300);

// Core application files - Using __DIR__ for robust pathing
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

// Authenticate and authorize admin
if (!is_admin_logged_in() || !has_permission('manage_reports')) {
    http_response_code(403); // Forbidden
    die("Access Denied.");
}

// --- Library Includes ---
// Use a single Composer autoload file from the project root
$composer_autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($composer_autoload)) {
    http_response_code(500);
    die("<strong>Fatal Error:</strong> Composer autoloader not found. Please run 'composer install' in your project root.");
}
require_once $composer_autoload;



// Use statements for included classes
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// --- Central Report Configurations ---
// To add a new report, just add an element to this array.
$report_configs = [
    'sales' => [
        'title' => 'Sales Report',
        'query' => 'SELECT id, user_id, total_amount, status, created_at FROM orders',
        'headers' => ['Order ID', 'User ID', 'Total Amount', 'Status', 'Created At'],
        'columns' => ['id', 'user_id', 'total_amount', 'status', 'created_at'],
        'date_column' => 'created_at'
    ],
    'users' => [
        'title' => 'Users Report',
        'query' => 'SELECT id, name, email, created_at FROM users',
        'headers' => ['User ID', 'Name', 'Email', 'Registered At'],
        'columns' => ['id', 'name', 'email', 'created_at'],
        'date_column' => 'created_at'
    ],
    'products' => [
        'title' => 'Product Inventory Report',
        'query' => 'SELECT id, name, price, stock_quantity, created_at FROM products',
        'headers' => ['Product ID', 'Name', 'Price', 'Stock', 'Created At'],
        'columns' => ['id', 'name', 'price', 'stock_quantity', 'created_at'],
        'date_column' => 'created_at'
    ],
    'coupons' => [
        'title' => 'Coupon Usage Report',
        'query' => 'SELECT id, code, discount_type, discount_value, usage_limit, used_count, expiry_date, created_at FROM coupons',
        'headers' => ['ID', 'Code', 'Type', 'Value', 'Limit', 'Used', 'Expiry', 'Created'],
        'columns' => ['id', 'code', 'discount_type', 'discount_value', 'usage_limit', 'used_count', 'expiry_date', 'created_at'],
        'date_column' => 'created_at'
    ],
    'activity_log' => [
        'title' => 'Activity Log Report',
        'query' => 'SELECT id, user_type, user_id, action, details, timestamp FROM activity_logs',
        'headers' => ['Log ID', 'User Type', 'User ID', 'Action', 'Details', 'Timestamp'],
        'columns' => ['id', 'user_type', 'user_id', 'action', 'details', 'timestamp'],
        'date_column' => 'timestamp'
    ]
];

// --- Reusable Functions ---

/**
 * Fetches data from the database based on the report configuration and date range.
 */
function fetch_report_data(mysqli $conn, string $base_query, string $date_column, ?string $start_date, ?string $end_date): array {
    $params = [];
    $param_types = '';
    $where_clauses = [];

    if ($start_date) {
        $where_clauses[] = "{$date_column} >= ?";
        $params[] = $start_date . ' 00:00:00';
        $param_types .= 's';
    }
    if ($end_date) {
        $where_clauses[] = "{$date_column} <= ?";
        $params[] = $end_date . ' 23:59:59';
        $param_types .= 's';
    }

    $query = $base_query;
    if (!empty($where_clauses)) {
        // Check if query already has a WHERE clause
        if (stripos($base_query, 'WHERE') === false) {
            $query .= " WHERE " . implode(' AND ', $where_clauses);
        } else {
            $query .= " AND " . implode(' AND ', $where_clauses);
        }
    }
    $query .= " ORDER BY {$date_column} DESC";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Query preparation failed: " . $conn->error);
        return [];
    }
    if ($param_types) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

/**
 * Generates and outputs a PDF report.
 */
function generate_pdf(string $title, array $headers, array $data, array $columns, string $filename): void {
    $pdf = new FPDF('L', 'mm', 'A4'); // Use Landscape for better table fitting
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 12, $title, 0, 1, 'C');
    $pdf->Ln(8);

    // Calculate dynamic column width
    $num_headers = count($headers);
    $column_width = $num_headers > 0 ? (277 / $num_headers) : 0; // 277mm is page width in landscape minus margins

    // Header
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(230, 230, 230);
    foreach ($headers as $header) {
        $pdf->Cell($column_width, 8, $header, 1, 0, 'C', true);
    }
    $pdf->Ln();

    // Data rows
    $pdf->SetFont('Arial', '', 8);
    foreach ($data as $row) {
        foreach ($columns as $column) {
            $pdf->Cell($column_width, 7, $row[$column] ?? '', 1);
        }
        $pdf->Ln();
    }

    $pdf->Output('D', $filename . '.pdf');
}

/**
 * Generates and outputs an Excel report.
 */
function generate_excel(string $title, array $headers, array $data, array $columns, string $filename): void {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle(substr($title, 0, 31));

    // Add headers and make them bold
    $sheet->fromArray($headers, NULL, 'A1');
    $header_style = ['font' => ['bold' => true]];
    $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($header_style);

    // Prepare data rows in the correct order
    $data_rows = [];
    foreach ($data as $row) {
        $ordered_row = [];
        foreach ($columns as $column_key) {
            $ordered_row[] = $row[$column_key] ?? '';
        }
        $data_rows[] = $ordered_row;
    }
    $sheet->fromArray($data_rows, NULL, 'A2');

    // Auto-size columns for readability
    foreach ($sheet->getColumnIterator() as $column) {
        $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
    }

    // Set HTTP headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
}

// --- Main Script Logic ---

try {
    // Get and validate POST data
    $report_type = $_POST['report_type'] ?? null;
    $format = $_POST['format'] ?? 'pdf';
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

    if (!$report_type || !isset($report_configs[$report_type])) {
        http_response_code(400); // Bad Request
        die("Invalid report type specified.");
    }

    $config = $report_configs[$report_type];

    // Fetch the data from the database
    $data = fetch_report_data($conn, $config['query'], $config['date_column'], $start_date, $end_date);

    $filename = "{$report_type}_report_" . date('Ymd_His');

    // Generate the report in the requested format
    if ($format === 'pdf') {
        generate_pdf($config['title'], $config['headers'], $data, $config['columns'], $filename);
    } elseif ($format === 'excel') {
        generate_excel($config['title'], $config['headers'], $data, $config['columns'], $filename);
    } else {
        http_response_code(400);
        die("Invalid format specified.");
    }

} catch (Exception $e) {
    error_log("Report generation failed: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    die("An unexpected error occurred while generating the report. Please check the server logs.");
}

exit; // Ensure no further output is sent

