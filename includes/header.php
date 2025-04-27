<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forum App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Forum</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Links for browsing threads -->
                    <li class="nav-item">
                        <a class="nav-link" href="forum.php">Browse Threads</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Links for logged-in users -->
                        <li class="nav-item">
                            <a class="nav-link" href="create_thread.php">Create Thread</a>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link text-white">Hi, <?= $_SESSION['username'] ?></span>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="btn btn-sm btn-light">Logout</a>
                        </li>
                    <?php else: ?>
                        <!-- Links for non-logged-in users -->
                        <li class="nav-item">
                            <a href="login.php" class="btn btn-sm btn-light me-2">Login</a>
                        </li>
                        <li class="nav-item">
                            <a href="register.php" class="btn btn-sm btn-success">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container">
