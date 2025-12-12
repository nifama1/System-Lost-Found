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

	// Handle item deletion
	if ($is_logged_in && isset($_POST['delete_item']) && isset($_POST['item_id'])) {
		$item_id = (int)$_POST['item_id'];
		$username = $_SESSION['username'];
		
		// Verify the item belongs to the user before deleting
		$delete_query = "DELETE FROM item WHERE id_item = ? AND un_pengguna = ?";
		$delete_stmt = mysqli_prepare($conn, $delete_query);
		mysqli_stmt_bind_param($delete_stmt, "is", $item_id, $username);
		
		if (mysqli_stmt_execute($delete_stmt)) {
			$delete_message = "Item deleted successfully!";
		} else {
			$delete_error = "Error deleting item.";
		}
		mysqli_stmt_close($delete_stmt);
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Lost & Found System - Find your lost items or help others find their belongings">
	<title>My Reports - Lost & Found</title>
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
				<h2>My Reports</h2>
				<?php if (isset($delete_message)): ?>
					<div class="success-message"><?php echo htmlspecialchars($delete_message); ?></div>
				<?php endif; ?>
				<?php if (isset($delete_error)): ?>
					<div class="error-message"><?php echo htmlspecialchars($delete_error); ?></div>
				<?php endif; ?>
			</section>
			
			<!-- User's Items Section -->
			<section class="recent-items"><br>
				<div class="items-grid" id="items-grid">
					<?php
					if ($is_logged_in) {
						// Fetch only items uploaded by the current user
						$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
						$items_per_page = 12;
						$offset = ($page - 1) * $items_per_page;
						
						$query = "SELECT id_item, nm_item, th_item, etc_item, img_item, status, un_pengguna, reward, no_pengguna 
								FROM item 
								WHERE un_pengguna = ?
								ORDER BY th_item DESC 
								LIMIT ? OFFSET ?";
						$stmt = mysqli_prepare($conn, $query);
						mysqli_stmt_bind_param($stmt, "sii", $username, $items_per_page, $offset);
						mysqli_stmt_execute($stmt);
						$result = mysqli_stmt_get_result($stmt);

						if ($result && mysqli_num_rows($result) > 0) {
							while ($row = mysqli_fetch_assoc($result)) {
								$image = !empty($row['img_item']) ? $row['img_item'] : 'images/default-item.png';
								?>
								<div class="item-card" onclick="showActionModal(<?php echo htmlspecialchars(json_encode([
									'id' => $row['id_item'],
									'name' => $row['nm_item'],
									'description' => $row['etc_item'],
									'image' => $image,
									'status' => $row['status'],
									'reward' => $row['reward'],
									'reporter' => $row['un_pengguna'],
									'date' => date('M d, Y', strtotime($row['th_item'])),
									'phone' => $row['no_pengguna']
								])); ?>)">
									<div class="item-image-container">
										<img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($row['nm_item']); ?>">
										<div class="item-overlay">
											<span class="view-details">Click to manage item</span>
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
							echo '<p class="no-items">You haven\'t reported any items yet.</p>';
						}
						mysqli_stmt_close($stmt);
					} else {
						echo '<p class="no-items">Please <a href="login.php">login</a> to view your reports.</p>';
					}
					?>
				</div>
				<div class="loading-spinner" id="loading-spinner">
					Loading more items...
				</div>
			</section>
		</div>
	</div>

	<!-- Action Modal for item management -->
	<div id="action-modal" class="action-modal">
		<div class="action-modal-content">
			<span class="action-modal-close" id="action-modal-close">&times;</span>
			<div class="action-modal-header">
				<h3>Manage Your Item</h3>
			</div>
			<div class="action-modal-body">
				<div class="action-item-preview">
					<img id="action-item-image" class="action-item-image" src="" alt="Item Image">
					<div class="action-item-info">
						<h4 id="action-item-name"></h4>
						<p id="action-item-description"></p>
						<div class="action-item-meta">
							<span id="action-item-status"></span>
							<span id="action-item-date"></span>
						</div>
						<div id="action-item-reward" class="action-item-reward"></div>
					</div>
				</div>
				<div class="action-buttons">
					<button class="btn-view-details" onclick="viewItemDetails()">
						<i class="fas fa-eye"></i> View Details
					</button>
					<button class="btn-edit-item" onclick="editItem()">
						<i class="fas fa-edit"></i> Edit Item
					</button>
					<button class="btn-delete-item" onclick="confirmDelete()">
						<i class="fas fa-trash"></i> Delete Item
					</button>
				</div>
			</div>
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

	<!-- Delete confirmation modal -->
	<div id="delete-modal" class="delete-modal">
		<div class="delete-modal-content">
			<h3>Confirm Delete</h3>
			<p>Are you sure you want to delete this item? This action cannot be undone.</p>
			<div class="delete-modal-buttons">
				<form method="POST" id="delete-form">
					<input type="hidden" name="item_id" id="delete-item-id">
					<input type="hidden" name="delete_item" value="1">
					<button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
					<button type="submit" class="btn-confirm-delete">Delete</button>
				</form>
			</div>
		</div>
	</div>

	<script>
		let currentItem = null;

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

		// Action modal functionality
		function showActionModal(item) {
			currentItem = item;
			const modal = document.getElementById('action-modal');
			document.getElementById('action-item-image').src = item.image;
			document.getElementById('action-item-name').textContent = item.name;
			document.getElementById('action-item-description').textContent = item.description;
			document.getElementById('action-item-status').textContent = `Status: ${item.status}`;
			document.getElementById('action-item-date').textContent = `Date: ${item.date}`;
			
			if (item.reward) {
				document.getElementById('action-item-reward').innerHTML = '<i class="fas fa-gift"></i> Reward: ' + item.reward;
				document.getElementById('action-item-reward').style.display = 'block';
			} else {
				document.getElementById('action-item-reward').style.display = 'none';
			}
			
			modal.style.display = 'block';
		}

		function viewItemDetails() {
			if (!currentItem) return;
			
			// Close action modal
			document.getElementById('action-modal').style.display = 'none';
			
			// Show details modal
			const modal = document.getElementById('item-modal');
			document.getElementById('modal-item-image').src = currentItem.image;
			document.getElementById('modal-item-name').textContent = currentItem.name;
			document.getElementById('modal-item-description').textContent = currentItem.description;
			document.getElementById('modal-item-status').textContent = `Status: ${currentItem.status}`;
			document.getElementById('modal-item-date').textContent = `Date: ${currentItem.date}`;
			document.getElementById('modal-item-reporter').textContent = currentItem.reporter ? `Reporter: ${currentItem.reporter}` : '';
			document.getElementById('modal-item-phone').textContent = currentItem.phone ? `Phone: ${currentItem.phone}` : '';
			
			if (currentItem.reward) {
				document.getElementById('modal-item-reward').innerHTML = '<i class="fas fa-gift"></i> Reward: ' + currentItem.reward;
				document.getElementById('modal-item-reward').style.display = 'block';
			} else {
				document.getElementById('modal-item-reward').style.display = 'none';
			}
			
			modal.style.display = 'block';
		}

		function editItem() {
			if (!currentItem) return;
			window.location.href = `edit.php?id=${encodeURIComponent(currentItem.id)}`;
		}

		function confirmDelete() {
			if (!currentItem) return;
			
			// Close action modal
			document.getElementById('action-modal').style.display = 'none';
			
			// Show delete confirmation modal
			document.getElementById('delete-item-id').value = currentItem.id;
			document.getElementById('delete-modal').style.display = 'block';
		}

		function closeDeleteModal() {
			document.getElementById('delete-modal').style.display = 'none';
		}

		// Close modals
		document.getElementById('action-modal-close').onclick = function() {
			document.getElementById('action-modal').style.display = 'none';
		};

		document.getElementById('item-modal-close').onclick = function() {
			document.getElementById('item-modal').style.display = 'none';
		};

		window.onclick = function(event) {
			const actionModal = document.getElementById('action-modal');
			const itemModal = document.getElementById('item-modal');
			const deleteModal = document.getElementById('delete-modal');
			
			if (event.target === actionModal) {
				actionModal.style.display = 'none';
			}
			if (event.target === itemModal) {
				itemModal.style.display = 'none';
			}
			if (event.target === deleteModal) {
				deleteModal.style.display = 'none';
			}
		};

		// Auto-hide success/error messages after 3 seconds
		setTimeout(function() {
			const successMessage = document.querySelector('.success-message');
			const errorMessage = document.querySelector('.error-message');
			
			if (successMessage) {
				successMessage.style.display = 'none';
			}
			if (errorMessage) {
				errorMessage.style.display = 'none';
			}
		}, 3000);
	</script>

	<style>
		/* Add Font Awesome for icons */
		@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

		.success-message {
			background-color: #d4edda;
			color: #155724;
			padding: 10px 15px;
			border-radius: 5px;
			margin: 10px 0;
			border: 1px solid #c3e6cb;
		}

		.error-message {
			background-color: #f8d7da;
			color: #721c24;
			padding: 10px 15px;
			border-radius: 5px;
			margin: 10px 0;
			border: 1px solid #f5c6cb;
		}

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

		/* Action Modal Styles */
		.action-modal {
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

		.action-modal-content {
			background: #fff;
			margin: 5% auto;
			padding: 30px 20px 20px 20px;
			border-radius: 12px;
			max-width: 500px;
			position: relative;
			box-shadow: 0 4px 24px rgba(0,0,0,0.2);
		}

		.action-modal-close {
			position: absolute;
			top: 10px;
			right: 20px;
			font-size: 2em;
			color: #888;
			cursor: pointer;
			font-weight: bold;
		}

		.action-modal-header h3 {
			margin: 0 0 20px 0;
			color: #333;
			text-align: center;
		}

		.action-item-preview {
			display: flex;
			gap: 15px;
			margin-bottom: 25px;
			align-items: flex-start;
		}

		.action-item-image {
			width: 100px;
			height: 100px;
			object-fit: cover;
			border-radius: 8px;
			flex-shrink: 0;
		}

		.action-item-info {
			flex: 1;
		}

		.action-item-info h4 {
			margin: 0 0 8px 0;
			color: #333;
		}

		.action-item-info p {
			margin: 0 0 10px 0;
			color: #666;
			font-size: 0.9em;
		}

		.action-item-meta {
			display: flex;
			flex-direction: column;
			gap: 5px;
			margin-bottom: 8px;
			font-size: 0.85em;
			color: #666;
		}

		.action-item-reward {
			background: #fff3e0;
			color: #e65100;
			padding: 4px 10px;
			border-radius: 12px;
			font-size: 0.85em;
			display: inline-block;
		}

		.action-buttons {
			display: flex;
			gap: 10px;
			justify-content: center;
		}

		.btn-view-details, .btn-edit-item, .btn-delete-item {
			padding: 12px 20px;
			border: none;
			border-radius: 8px;
			cursor: pointer;
			font-weight: 500;
			display: flex;
			align-items: center;
			gap: 8px;
			transition: all 0.2s;
		}

		.btn-view-details {
			background-color: #4a90e2;
			color: white;
		}

		.btn-view-details:hover {
			background-color: #357abd;
		}

		.btn-edit-item {
			background-color: #ffc107;
			color: #212529;
		}

		.btn-edit-item:hover {
			background-color: #e0a800;
		}

		.btn-delete-item {
			background-color: #dc3545;
			color: white;
		}

		.btn-delete-item:hover {
			background-color: #c82333;
		}

		/* Delete Modal Styles */
		.delete-modal {
			display: none;
			position: fixed;
			z-index: 1001;
			left: 0;
			top: 0;
			width: 100vw;
			height: 100vh;
			overflow: auto;
			background: rgba(0,0,0,0.5);
			align-items: center;
			justify-content: center;
		}

		.delete-modal-content {
			background: #fff;
			margin: 5% auto;
			padding: 30px;
			border-radius: 12px;
			max-width: 400px;
			text-align: center;
			box-shadow: 0 4px 24px rgba(0,0,0,0.2);
		}

		.delete-modal-content h3 {
			margin: 0 0 15px 0;
			color: #333;
		}

		.delete-modal-content p {
			margin: 0 0 25px 0;
			color: #666;
		}

		.delete-modal-buttons {
			display: flex;
			gap: 10px;
			justify-content: center;
		}

		.btn-cancel, .btn-confirm-delete {
			padding: 10px 20px;
			border: none;
			border-radius: 6px;
			cursor: pointer;
			font-weight: 500;
			transition: all 0.2s;
		}

		.btn-cancel {
			background-color: #6c757d;
			color: white;
		}

		.btn-cancel:hover {
			background-color: #5a6268;
		}

		.btn-confirm-delete {
			background-color: #dc3545;
			color: white;
		}

		.btn-confirm-delete:hover {
			background-color: #c82333;
		}

		/* Item Modal Styles */
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
        