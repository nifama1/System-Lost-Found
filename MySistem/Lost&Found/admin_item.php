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

    // Handle delete item action
    $delete_success = '';
    $delete_error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
        $itemId = intval($_POST['delete_item']);
        $stmt = mysqli_prepare($conn, "DELETE FROM item WHERE id_item = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $itemId);
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $delete_success = 'Item has been deleted.';
                } else {
                    $delete_error = 'Item not found or could not be deleted.';
                }
            } else {
                $delete_error = 'Failed to delete item.';
            }
            mysqli_stmt_close($stmt);
        } else {
            $delete_error = 'Failed to prepare delete statement.';
        }
    }

    // Fetch items
    $items_result = mysqli_query($conn, "SELECT id_item, nm_item, th_item, etc_item, img_item, status, reward, no_pengguna, nm_pengguna, un_pengguna FROM item ORDER BY th_item DESC");
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
                    <a href="admin_user.php" class="nav-item">
                        👥 User Management
                    </a>
                    <a href="admin_item.php" class="nav-item active">
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
            <h1>📦 Item Management</h1>
            <p>Welcome back, <?php echo htmlspecialchars($username); ?>! Review and manage reported items.</p>
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
            <h2>📋 Items</h2>
            <table class="recent-items-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Reporter</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($items_result && mysqli_num_rows($items_result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($items_result)): ?>
                            <?php $img = !empty($row['img_item']) ? $row['img_item'] : 'noimg.png'; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id_item']); ?></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($img); ?>" data-full="<?php echo htmlspecialchars($img); ?>" class="item-thumb" alt="item" style="width:50px;height:50px;border-radius:8px;object-fit:cover;cursor:pointer;">
                                </td>
                                <td><strong><?php echo htmlspecialchars($row['nm_item']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['nm_pengguna']); ?></td>
                                <td>
                                    <button type="button"
                                            class="admin-btn admin-btn-primary detail-btn"
                                            style="margin-right:8px;"
                                            data-id="<?php echo htmlspecialchars($row['id_item']); ?>"
                                            data-name="<?php echo htmlspecialchars($row['nm_item']); ?>"
                                            data-status="<?php echo htmlspecialchars($row['status']); ?>"
                                            data-reporter="<?php echo htmlspecialchars($row['nm_pengguna']); ?>"
                                            data-username="<?php echo htmlspecialchars($row['un_pengguna']); ?>"
                                            data-date="<?php echo htmlspecialchars(date('M d, Y', strtotime($row['th_item']))); ?>"
                                            data-description="<?php echo htmlspecialchars($row['etc_item']); ?>"
                                            data-reward="<?php echo htmlspecialchars($row['reward']); ?>"
                                            data-phone="<?php echo htmlspecialchars($row['no_pengguna']); ?>"
                                            data-image="<?php echo htmlspecialchars($img); ?>">
                                        Detail
                                    </button>
                                    <form method="post" action="" onsubmit="return confirm('Delete item #<?php echo htmlspecialchars($row['id_item']); ?>? This cannot be undone.');" style="display:inline;">
                                        <input type="hidden" name="delete_item" value="<?php echo htmlspecialchars($row['id_item']); ?>">
                                        <button type="submit" class="admin-btn admin-btn-secondary">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #666; padding: 20px;">No items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
            </div>
        </div>
    </div>

    <script>
        // Modal for item details
        (function() {
            const modalHtml = `
                <div id="itemDetailModal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
                    <div style="background:#fff; border-radius:12px; max-width:800px; width:90%; padding:20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: relative;">
                        <button id="closeItemModal" style="position:absolute; top:10px; right:10px; background:#f5f6f8; border:1px solid #e0e0e0; border-radius:6px; padding:6px 10px; cursor:pointer;">Close</button>
                        <div style="display:flex; gap:16px; align-items:flex-start;">
                            <div style="flex:1;">
                                <h3 id="detailName" style="margin: 0 0 8px 0;">Item</h3>
                                <div style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
                                    <span id="detailStatus" class="status-badge">Status</span>
                                    <span><strong>Reporter:</strong> <span id="detailReporter"></span></span>
                                    <span><strong>Username:</strong> <span id="detailUsername"></span></span>
                                    <span><strong>Date:</strong> <span id="detailDate"></span></span>
                                </div>
                                <div style="margin-bottom:8px;"><strong>Description:</strong> <div id="detailDescription" style="margin-top:4px; white-space:pre-wrap;"></div></div>
                                <div style="display:flex; gap:16px;">
                                    <span><strong>Reward:</strong> <span id="detailReward"></span></span>
                                    <span><strong>Phone:</strong> <span id="detailPhone"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

            document.addEventListener('DOMContentLoaded', function() {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = modalHtml;
                document.body.appendChild(wrapper.firstElementChild);

                const modal = document.getElementById('itemDetailModal');
                const closeBtn = document.getElementById('closeItemModal');
                const nameEl = document.getElementById('detailName');
                const statusEl = document.getElementById('detailStatus');
                const reporterEl = document.getElementById('detailReporter');
                const usernameEl = document.getElementById('detailUsername');
                const dateEl = document.getElementById('detailDate');
                const descEl = document.getElementById('detailDescription');
                const rewardEl = document.getElementById('detailReward');
                const phoneEl = document.getElementById('detailPhone');

                function openModal(data) {
                    nameEl.textContent = data.name || '';
                    statusEl.textContent = data.status || '';
                    statusEl.className = 'status-badge status-' + (data.status ? data.status.toLowerCase() : '');
                    reporterEl.textContent = data.reporter || '';
                    usernameEl.textContent = data.username || '';
                    dateEl.textContent = data.date || '';
                    descEl.textContent = data.description || '';
                    rewardEl.textContent = data.reward || '';
                    phoneEl.textContent = data.phone || '';
                    modal.style.display = 'flex';
                }

                function closeModal() {
                    modal.style.display = 'none';
                }

                document.querySelectorAll('.detail-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const data = {
                            id: this.dataset.id,
                            image: this.dataset.image,
                            name: this.dataset.name,
                            status: this.dataset.status,
                            reporter: this.dataset.reporter,
                            username: this.dataset.username,
                            date: this.dataset.date,
                            description: this.dataset.description,
                            reward: this.dataset.reward,
                            phone: this.dataset.phone
                        };
                        openModal(data);
                    });
                });

                closeBtn.addEventListener('click', closeModal);
                modal.addEventListener('click', function(e){
                    if (e.target === modal) closeModal();
                });
            });
        })();
        // Add some interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Lightbox for thumbnails
            const lightboxHtml = `
                <div id="imageLightbox" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:2100; align-items:center; justify-content:center;">
                    <img id="lightboxImg" src="" alt="full" style="max-width:95%; max-height:95%; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.5);" />
                </div>`;
            const wrap2 = document.createElement('div');
            wrap2.innerHTML = lightboxHtml;
            document.body.appendChild(wrap2.firstElementChild);
            const lb = document.getElementById('imageLightbox');
            const lbImg = document.getElementById('lightboxImg');

            document.querySelectorAll('.item-thumb').forEach(img => {
                img.addEventListener('click', function(){
                    const src = this.getAttribute('data-full') || this.src;
                    lbImg.src = src;
                    lb.style.display = 'flex';
                });
            });
            lb.addEventListener('click', function(){ lb.style.display = 'none'; });

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
