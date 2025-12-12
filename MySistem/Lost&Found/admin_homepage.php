<?php
    include('config.php');
    session_start();

    // Check if user is logged in and is admin
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }

    // For now, we'll assume any logged-in user can access admin page
    // You can add admin role checking later
    $username = $_SESSION['username'];

    // Get statistics
    $stats = array();

    // Total users
    $query = "SELECT COUNT(*) as total_users FROM pengguna";
    $result = mysqli_query($conn, $query);
    $stats['total_users'] = mysqli_fetch_assoc($result)['total_users'];

    // Total items
    $query = "SELECT COUNT(*) as total_items FROM item";
    $result = mysqli_query($conn, $query);
    $stats['total_items'] = mysqli_fetch_assoc($result)['total_items'];

    // Found items
    $query = "SELECT COUNT(*) as found_items FROM item WHERE status = 'Found'";
    $result = mysqli_query($conn, $query);
    $stats['found_items'] = mysqli_fetch_assoc($result)['found_items'];

    // Lost items
    $query = "SELECT COUNT(*) as lost_items FROM item WHERE status = 'Lost'";
    $result = mysqli_query($conn, $query);
    $stats['lost_items'] = mysqli_fetch_assoc($result)['lost_items'];

    // Recent items (last 5)
    $query = "SELECT nm_item, th_item, status, nm_pengguna FROM item ORDER BY th_item DESC LIMIT 5";
    $recent_items = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Lost & Found System</title>
    <link rel="stylesheet" href="home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" onclick="toggleSidebar()">☰</button>

        <!-- Sidebar -->
        <div class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="logo-kv">
                <img src="banner.png">
                </div>
                <p>Lost & Found System</p>
            </div>
            
            <div class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Dashboard</div>
                    <a href="admin_homepage.php" class="nav-item active">
                        📊 Dashboard
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="admin_user.php" class="nav-item">
                        👥 User Management
                    </a>
                    <a href="admin_item.php" class="nav-item">
                        📦 Item Management
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    </a>
                    <a href="logout.php" class="nav-item">
                        🚪 Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="admin-main">
            <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <h1>🔧 Admin Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($username); ?>! Here's your system overview.</p>
        </div>

        <!-- Welcome Message -->
        <div class="welcome-message">
            <strong>📊 System Statistics</strong> - Monitor your Lost & Found system performance and user activity.
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon users-icon">👥</div>
                <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon items-icon">📦</div>
                <div class="stat-number"><?php echo $stats['total_items']; ?></div>
                <div class="stat-label">Total Items</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon found-icon">✅</div>
                <div class="stat-number"><?php echo $stats['found_items']; ?></div>
                <div class="stat-label">Items Found</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon lost-icon">🔍</div>
                <div class="stat-number"><?php echo $stats['lost_items']; ?></div>
                <div class="stat-label">Items Lost</div>
            </div>
        </div>

        <!-- Recent Items Section -->
        <div class="recent-section">
            <h2>📋 Recent Items</h2>
            <table class="recent-items-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Status</th>
                        <th>Reporter</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($recent_items) > 0): ?>
                        <?php while ($item = mysqli_fetch_assoc($recent_items)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['nm_item']); ?></strong></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($item['status']); ?>">
                                        <?php echo htmlspecialchars($item['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($item['nm_pengguna']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($item['th_item'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #666; padding: 20px;">
                                No items have been reported yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Admin Actions -->
        <div class="admin-actions">
            <a href="admin_item.php" class="admin-btn admin-btn-primary">📋 View All Reports</a>
        </div>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stat cards on load
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.6s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });

            // Add click effect to stat cards
            statCards.forEach(card => {
                card.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        });

        // Mobile sidebar toggle function
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('open');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('adminSidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
    </script>
</body>
</html>
