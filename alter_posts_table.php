<?php
require_once __DIR__ . '/core/db.php';

$sql = "ALTER TABLE `posts` ADD `meta_title` VARCHAR(255) DEFAULT NULL, ADD `meta_description` TEXT DEFAULT NULL;";

if ($conn->query($sql) === TRUE) {
    echo "Table posts altered successfully";
} else {
    echo "Error altering table: " . $conn->error;
}

$conn->close();
?>