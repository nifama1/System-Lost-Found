<?php
	include('config.php');
	session_start();

	$is_logged_in = isset($_SESSION['username']);
	$profile_image = 'images/default-profile.png';
	$display_name = '';

	if ($is_logged_in) {
		$username = $_SESSION['username'];
		// Use prepared statement to prevent SQL injection
		$query = "SELECT gambar, dn_pengguna FROM pengguna WHERE nm_pengguna = ?";
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, "s", $username);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		
		if ($result && $row = mysqli_fetch_assoc($result)) {
			if (!empty($row['gambar'])) $profile_image = $row['gambar'];
			$display_name = !empty($row['dn_pengguna']) ? $row['dn_pengguna'] : $username;
		}
		mysqli_stmt_close($stmt);
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Lost & Found System - Find your lost items or help others find their belongings">
	<title>Lost & Found Homepage</title>
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
					<?php if ($is_logged_in): ?>
						<li><a href="userReports.php">📋 My Reports</a></li>
					<?php endif; ?>
				</ul>
			</nav>
			<?php if ($is_logged_in): ?>
				<div class="user-info-container">
					<div class="user-info">
						<img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile" class="profile-img">
						<div class="user-text">
							<span class="user-display-name"><?php echo htmlspecialchars($display_name); ?></span>
							<span class="user-username">@<?php echo htmlspecialchars($username); ?></span>
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
        			<?php if ($is_logged_in): ?>
            			
        			<?php else: ?>
            			<a href="login.php" class="btn-header">Login</a>
            			<a href="signup.php" class="btn-header btn-signup">Sign Up</a>
        			<?php endif; ?>
    			</div>
			</header>
			<section class="hero">
				<h2>Lost Items Reported</h2>
			</section>
			
			<!-- Lost Items Section -->
			<section class="recent-items"><br>
				<div class="items-grid" id="items-grid">
					<?php
					// Fetch lost items from the database
					$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
					$items_per_page = 12;
					$offset = ($page - 1) * $items_per_page;
					
					$query = "SELECT nm_item, th_item, etc_item, img_item, status, nm_pengguna, reward, no_pengguna 
							FROM item 
							WHERE status = 'Lost'
							ORDER BY th_item DESC 
							LIMIT ? OFFSET ?";
					$stmt = mysqli_prepare($conn, $query);
					mysqli_stmt_bind_param($stmt, "ii", $items_per_page, $offset);
					mysqli_stmt_execute($stmt);
					$result = mysqli_stmt_get_result($stmt);

					if ($result && mysqli_num_rows($result) > 0) {
						while ($row = mysqli_fetch_assoc($result)) {
							$image = !empty($row['img_item']) ? $row['img_item'] : 'images/default-item.png';
							?>
							<div class="item-card" onclick="showItemDetails(<?php echo htmlspecialchars(json_encode([
								'name' => $row['nm_item'],
								'description' => $row['etc_item'],
								'image' => $image,
								'status' => $row['status'],
								'reward' => $row['reward'],
								'reporter' => $row['nm_pengguna'],
								'date' => date('M d, Y', strtotime($row['th_item'])),
								'phone' => $row['no_pengguna']
							])); ?>)">
								<div class="item-image-container">
									<img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($row['nm_item']); ?>">
									<div class="item-overlay">
										<span class="view-details">Click to view details</span>
									</div>
								</div>
								<div class="item-info">
									<h4><?php echo htmlspecialchars($row['nm_item']); ?></h4>
									<div class="item-details">
										<span class="item-status <?php echo strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
										<span class="item-date"><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($row['th_item'])); ?></span>
									</div>
								</div>
							</div>
							<?php
						}
					} else {
						echo '<p class="no-items">No lost items have been reported yet.</p>';
					}
					mysqli_stmt_close($stmt);
					?>
				</div>
				<div class="loading-spinner" id="loading-spinner">
					Loading more items...
				</div>
			</section>
		</div>
	</div>

	<!-- Modal for item details -->
	<div id="item-modal" class="item-modal">
		<div class="item-modal-content">
			<span class="item-modal-close" id="item-modal-close">&times;</span>
			<img id="modal-item-image" class="modal-item-image" src="" alt="Item Image">
			<h3 id="modal-item-name"></h3>
			<p id="modal-item-description"></p>
			<div class="modal-item-meta">
				<span id="modal-item-status"></span>
				<span id="modal-item-date"></span>
				<span id="modal-item-reporter"></span>
				<span id="modal-item-phone"></span>
			</div>
			<div id="modal-item-reward" class="modal-item-reward"></div>
		</div>
	</div>

	<script>
		// User dropdown functionality
		const userInfo = document.querySelector('.user-info');
		const userDropdownUp = document.querySelector('.user-dropdown-up');

		if (userInfo && userDropdownUp) {
			userInfo.addEventListener('click', () => {
				userDropdownUp.classList.toggle('show');
			});

			document.addEventListener('click', (event) => {
				if (!userInfo.contains(event.target) && !userDropdownUp.contains(event.target)) {
					userDropdownUp.classList.remove('show');
				}
			});
		}

		// Infinite scrolling functionality
		let currentPage = 1;
		let isLoading = false;
		const itemsGrid = document.getElementById('items-grid');
		const loadingSpinner = document.getElementById('loading-spinner');

		async function loadMoreItems() {
			if (isLoading) return;
			
			isLoading = true;
			loadingSpinner.classList.add('active');
			
			try {
				const response = await fetch(`load_more_lost_items.php?page=${currentPage + 1}`);
				const data = await response.text();
				
				if (data.trim()) {
					itemsGrid.insertAdjacentHTML('beforeend', data);
					currentPage++;
				}
			} catch (error) {
				console.error('Error loading more items:', error);
			} finally {
				isLoading = false;
				loadingSpinner.classList.remove('active');
			}
		}

		// Intersection Observer for infinite scrolling
		const observer = new IntersectionObserver((entries) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					loadMoreItems();
				}
			});
		}, {
			rootMargin: '100px'
		});

		observer.observe(loadingSpinner);

		// Modal logic for item details
		function showItemDetails(item) {
			const modal = document.getElementById('item-modal');
			document.getElementById('modal-item-image').src = item.image;
			document.getElementById('modal-item-name').textContent = item.name;
			document.getElementById('modal-item-description').textContent = item.description;
			document.getElementById('modal-item-status').textContent = `Status: ${item.status}`;
			document.getElementById('modal-item-date').textContent = `Date: ${item.date}`;
			document.getElementById('modal-item-reporter').textContent = item.reporter ? `Reporter: ${item.reporter}` : '';
			document.getElementById('modal-item-phone').textContent = item.phone ? `Phone: ${item.phone}` : '';
			if (item.reward) {
				document.getElementById('modal-item-reward').innerHTML = '<i class="fas fa-gift"></i> Reward: ' + item.reward;
				document.getElementById('modal-item-reward').style.display = 'block';
			} else {
				document.getElementById('modal-item-reward').style.display = 'none';
			}
			modal.style.display = 'block';
		}
		document.getElementById('item-modal-close').onclick = function() {
			document.getElementById('item-modal').style.display = 'none';
		};
		window.onclick = function(event) {
			const modal = document.getElementById('item-modal');
			if (event.target === modal) {
				modal.style.display = 'none';
			}
		};
	</script>

	<style>
		/* Add Font Awesome for icons */
		@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

		.item-card {
			cursor: pointer;
			transition: transform 0.2s, box-shadow 0.2s;
			background: white;
			border-radius: 12px;
			overflow: hidden;
			box-shadow: 0 1px 2px #4a90e2;
		}

		.item-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 20px 22px #4a90e2;
		}

		.item-image-container {
			position: relative;
			overflow: hidden;
			aspect-ratio: 1;
		}

		.item-image-container img {
			width: 100%;
			height: 100%;
			object-fit: cover;
			transition: transform 0.3s;
		}

		.item-overlay {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: rgba(0,0,0,0.5);
			display: flex;
			align-items: center;
			justify-content: center;
			opacity: 0;
			transition: opacity 0.3s;
		}

		.item-card:hover .item-overlay {
			opacity: 1;
		}

		.view-details {
			color: white;
			font-weight: 500;
			text-align: center;
			padding: 8px 16px;
			border: 2px solid white;
			border-radius: 20px;
		}

		.item-info {
			padding: 15px;
		}

		.item-info h4 {
			margin: 0 0 10px 0;
			font-size: 1.1em;
			color: #333;
		}

		.item-details {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 8px;
		}

		.item-status {
			padding: 4px 12px;
			border-radius: 15px;
			font-size: 0.85em;
			font-weight: 500;
		}

		.item-status.lost {
			background-color: #ffebee;
			color: #d32f2f;
		}

		.item-status.found {
			background-color: #e8f5e9;
			color: #2e7d32;
		}

		.item-date {
			font-size: 0.85em;
			color: #666;
		}

		.item-reward-preview {
			background-color: #fff3e0;
			color: #e65100;
			padding: 4px 12px;
			border-radius: 15px;
			font-size: 0.85em;
			display: inline-flex;
			align-items: center;
			gap: 5px;
		}

		.item-modal {
			display: none;
			position: fixed;
			z-index: 1000;
			left: 0;
			top: 0;
			width: 100vw;
			height: 100vh;
			overflow: auto;
			background: rgba(0,0,0,0.5);
			align-items: center;
			justify-content: center;
		}
		.item-modal-content {
			background: #fff;
			margin: 5% auto;
			padding: 30px 20px 20px 20px;
			border-radius: 12px;
			max-width: 400px;
			position: relative;
			box-shadow: 0 4px 24px rgba(0,0,0,0.2);
			text-align: center;
		}
		.item-modal-close {
			position: absolute;
			top: 10px;
			right: 20px;
			font-size: 2em;
			color: #888;
			cursor: pointer;
			font-weight: bold;
		}
		.modal-item-image {
			width: 100%;
			max-width: 250px;
			height: auto;
			border-radius: 10px;
			margin-bottom: 15px;
		}
		#modal-item-name {
			margin: 10px 0 5px 0;
			font-size: 1.3em;
			color: #333;
		}
		#modal-item-description {
			margin-bottom: 15px;
			color: #555;
		}
		.modal-item-meta {
			display: flex;
			flex-direction: column;
			gap: 5px;
			margin-bottom: 10px;
			font-size: 0.95em;
			color: #666;
		}
		.modal-item-reward {
			background: #fff3e0;
			color: #e65100;
			padding: 6px 14px;
			border-radius: 15px;
			display: inline-block;
			margin-top: 10px;
			font-size: 1em;
		}
	</style>
</body>
</html>

<?php mysqli_close($conn); ?>
