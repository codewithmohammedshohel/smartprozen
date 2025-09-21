<?php
header('Content-Type: application/json');
require_once '../config.php';
require_once '../core/db.php';

// This is a simplified version. A real-world app would handle date ranges.
$query = "SELECT DATE(created_at) as date, SUM(total_amount) as total 
          FROM orders 
          WHERE created_at >= CURDATE() - INTERVAL 7 DAY AND status = 'Completed'
          GROUP BY DATE(created_at) 
          ORDER BY date ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$values = [];
// Create a date range for the last 7 days to ensure all days are present
$date_period = new DatePeriod(
     new DateTime('-6 days'),
     new DateInterval('P1D'),
     new DateTime('+1 day')
);

foreach($date_period as $date){
    $labels[$date->format('Y-m-d')] = $date->format('M j');
    $values[$date->format('Y-m-d')] = 0;
}

while ($row = $result->fetch_assoc()) {
    $values[$row['date']] = $row['total'];
}

echo json_encode(['labels' => array_values($labels), 'values' => array_values($values)]);
?>