<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SmartProzen</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/admin.css">


</head>
<body class="admin-theme">
    <?php
    if (isset($_GET['action']) && $_GET['action'] === 'clear_cache') {
        if (clear_pwa_cache()) {
            $_SESSION['success_message'] = "PWA cache has been cleared. Users will receive the latest updates.";
        } else {
            $_SESSION['error_message'] = "Failed to clear PWA cache.";
        }
        header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
        exit;
    }
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="?action=clear_cache">
                            <i class="bi bi-arrow-clockwise"></i> Clear Cache
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="d-flex" id="wrapper">
    <?php require_once 'admin_sidebar.php'; ?>
