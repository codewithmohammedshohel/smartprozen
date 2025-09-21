<?php
require_once '../config.php';
header('Content-Type: application/json');

$response = [];

if (isset($_SESSION['cart'])) {
    $response = $_SESSION['cart'];
}

echo json_encode($response);
exit;
?>