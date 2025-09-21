<?php
require_once 'config.php';
require_once 'core/db.php';

echo "<pre>";

$column_name = 'code';
$table_name = 'payment_gateways';

// Check if the column already exists
$check_column_sql = "SHOW COLUMNS FROM `$table_name` LIKE '$column_name'";
$result = $conn->query($check_column_sql);

if ($result && $result->num_rows > 0) {
    echo "Column '$column_name' already exists in table '$table_name'. Skipping migration.\n";
} else {
    // Add the column
    $add_column_sql = "ALTER TABLE `$table_name` ADD COLUMN `$column_name` VARCHAR(50) UNIQUE AFTER `name`";
    if ($conn->query($add_column_sql) === TRUE) {
        echo "Column '$column_name' added successfully to table '$table_name'.\n";
    } else {
        echo "Error adding column '$column_name' to table '$table_name': " . $conn->error . "\n";
    }
}

echo "Migration script finished.\n";
echo "You can now delete this script.";
echo "</pre>";

?>