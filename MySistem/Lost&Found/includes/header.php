<?php
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lost & Found System - Find your lost items or help others find their belongings">
    <title><?php echo $page_title ?? 'Lost & Found System'; ?></title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo-kv">
                <img src="banner.png" alt="Lost & Found Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">🏠 Home</a></li>
                    <li><a href="lostItem.php">🔍 Lost Reported</a></li>
                    <li><a href="foundItem.php">📦 Found Items</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="userReports.php">📋 My Reports</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php if (isLoggedIn()): ?>
                <?php 
                $username = getCurrentUser();
                $profile = getUserProfile($conn, $username);
                ?>
                <div class="user-info-container">
                    <div class="user-info">
                        <img src="<?php echo e($profile['image']); ?>" alt="Profile" class="profile-img">
                        <div class="user-text">
                            <span class="user-display-name"><?php echo e($profile['display_name']); ?></span>
                            <span class="user-username">@<?php echo e($username); ?></span>
                        </div>
                    </div>
                    <div class="user-dropdown-up">
                        <a href="settings.php">⚙️ Settings</a>
                        <a href="logout.php">🚪 Log Out</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Content -->
        <div class="main">
            <header>
                <div class="log-sign">
                    <?php if (isLoggedIn()): ?>
                        <!-- User is logged in, no login/signup buttons needed -->
                    <?php else: ?>
                        <a href="login.php" class="btn-header">Login</a>
                        <a href="signup.php" class="btn-header btn-signup">Sign Up</a>
                    <?php endif; ?>
                </div>
            </header> 