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

    // Handle delete user action
    $delete_success = '';
    $delete_error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
        $targetUser = trim($_POST['delete_user']);
        if ($targetUser === $username || strtolower($targetUser) === 'admin') {
            $delete_error = 'Cannot delete the current user or admin account.';
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM pengguna WHERE nm_pengguna = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $targetUser);
                if (mysqli_stmt_execute($stmt)) {
                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        $delete_success = 'User ' . htmlspecialchars($targetUser) . ' has been terminated.';
                    } else {
                        $delete_error = 'User not found or could not be deleted.';
                    }
                } else {
                    $delete_error = 'Failed to delete user.';
                }
                mysqli_stmt_close($stmt);
            } else {
                $delete_error = 'Failed to prepare delete statement.';
            }
        }
    }

    // Fetch users
    $users_result = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY nm_pengguna ASC");
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
                    <a href="admin_homepage.php" class="nav-item">
                        📊 Dashboard
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="admin_user.php" class="nav-item active">
                        👥 User Management
                    </a>
                    <a href="admin_item.php" class="nav-item">
                        📦 Item Management
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">System</div>
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
            <h1>👥 User Management</h1>
            <p>Welcome back, <?php echo htmlspecialchars($username); ?>! Manage your users below.</p>
        </div>

        <?php if (!empty($delete_success)): ?>
        <div class="welcome-message" style="background: #e8f5e9; border-left-color: #4CAF50;">
            <?php echo $delete_success; ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($delete_error)): ?>
        <div class="welcome-message" style="background: #ffebee; border-left-color: #F44336;">
            <?php echo $delete_error; ?>
        </div>
        <?php endif; ?>

        <div class="recent-section">
            <h2>📋 Users</h2>
            <table class="recent-items-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Display Name</th>
                        <th>Profile</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users_result && mysqli_num_rows($users_result) > 0): ?>
                        <?php while ($user_row = mysqli_fetch_assoc($users_result)): ?>
                            <?php
                                $displayId = isset($user_row['id_pengguna']) ? $user_row['id_pengguna'] : (isset($user_row['id']) ? $user_row['id'] : '—');
                                $userName = $user_row['nm_pengguna'] ?? '';
                                $displayName = $user_row['dn_pengguna'] ?? '';
                                $profileImage = !empty($user_row['gambar']) ? $user_row['gambar'] : 'noimg.png';
                                $isProtected = strtolower($userName) === 'admin' || $userName === $username;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($displayId); ?></td>
                                <td><strong><?php echo htmlspecialchars($userName); ?></strong></td>
                                <td><?php echo htmlspecialchars($displayName); ?></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="profile" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                                </td>
                                <td>
                                    <form method="post" action="" onsubmit="return confirm('Delete user &quot;<?php echo htmlspecialchars($userName); ?>&quot;? This cannot be undone.');" style="display:inline;">
                                        <input type="hidden" name="delete_user" value="<?php echo htmlspecialchars($userName); ?>">
                                        <button type="submit" class="admin-btn admin-btn-secondary" <?php echo $isProtected ? 'disabled' : ''; ?>>Terminate</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #666; padding: 20px;">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
