<?php
	include('config.php');
	session_start();

	// Check if user is logged in
	if (!isset($_SESSION['username'])) {
		header("Location: login.php");
		exit();
	}

	$username = $_SESSION['username'];

	// Fetch user info
	$user_query = mysqli_query($conn, "SELECT * FROM pengguna WHERE nm_pengguna = '" . mysqli_real_escape_string($conn, $username) . "'");
	$user = mysqli_fetch_assoc($user_query);

	$success = $error = '';
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Update display name only
		if (isset($_POST['display_name'])) {
			$display_name = mysqli_real_escape_string($conn, $_POST['display_name']);
			mysqli_query($conn, "UPDATE pengguna SET dn_pengguna = '$display_name' WHERE nm_pengguna = '$username'");
			$success = 'Display name updated successfully!';
			$user['dn_pengguna'] = $display_name;
		}

		// Update password (with hashing)
		if (!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
			$old_password = $_POST['old_password'];
			$new_password = $_POST['new_password'];
			// Check old password (hashed)
			$check = mysqli_query($conn, "SELECT password FROM pengguna WHERE nm_pengguna = '$username'");
			$row = mysqli_fetch_assoc($check);
			if ($row && password_verify($old_password, $row['password'])) {
				$new_hash = password_hash($new_password, PASSWORD_DEFAULT);
				mysqli_query($conn, "UPDATE pengguna SET password = '" . mysqli_real_escape_string($conn, $new_hash) . "' WHERE nm_pengguna = '$username'");
				$success = 'Password updated successfully!';
			} else {
				$error = 'Old password is incorrect. Please try again.';
			}
		}

		// Update profile picture
		if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
			$ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
			$target = 'uploads/profile_' . $username . '.' . $ext;

			// Delete old profile picture if exists and not default
			if (!empty($user['gambar']) && file_exists($user['gambar']) && $user['gambar'] !== 'uploads/noimg.png') {
				unlink($user['gambar']);
			}

			if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
				mysqli_query($conn, "UPDATE pengguna SET gambar = '" . mysqli_real_escape_string($conn, $target) . "' WHERE nm_pengguna = '$username'");
				$success = 'Profile picture updated successfully!';
				$user['gambar'] = $target;
			} else {
				$error = 'Failed to upload profile picture. Please try again.';
			}
		}

		// Delete account
		if (isset($_POST['delete_account']) && $_POST['delete_account'] === '1') {
			// Delete profile picture if exists
			if (!empty($user['gambar']) && file_exists($user['gambar']) && $user['gambar'] !== 'uploads/noimg.png') {
				unlink($user['gambar']);
			}
			mysqli_query($conn, "DELETE FROM pengguna WHERE nm_pengguna = '$username'");
			session_destroy();
			header("Location: index.php");
			exit();
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Settings - Lost & Found</title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Inter', sans-serif;
			background: #f7f9fc;
			min-height: 100vh;
			color: #333;
			line-height: 1.6;
		}

		.container {
			max-width: 1200px;
			margin: 0 auto;
			padding: 20px;
		}

		/* Header */
		.header {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(10px);
			border-radius: 20px;
			padding: 25px 30px;
			margin-bottom: 30px;
			box-shadow: 0 8px 32px #4a90e2;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		.header h1 {
			font-size: 28px;
			font-weight: 700;
			color: #4a90e2;
		}

		.header-actions {
			display: flex;
			gap: 15px;
		}

		.btn {
			padding: 12px 24px;
			border: none;
			border-radius: 12px;
			font-weight: 600;
			text-decoration: none;
			display: inline-flex;
			align-items: center;
			gap: 8px;
			transition: all 0.3s ease;
			cursor: pointer;
			font-size: 14px;
		}

		.btn-primary {
			background: #4a90e2;
			color: white;
		}

		.btn-primary:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 25px rgba(74, 144, 226, 0.4);
		}

		.btn-danger {
			background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
			color: white;
		}

		.btn-danger:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
		}

		/* Profile Overview */
		.profile-overview {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(10px);
			border-radius: 20px;
			padding: 30px;
			margin-bottom: 30px;
			box-shadow: 0 8px 32px #4a90e2;
			display: flex;
			align-items: center;
			gap: 25px;
		}

		.profile-avatar {
			position: relative;
			width: 120px;
			height: 120px;
		}

		.profile-avatar img {
			width: 100%;
			height: 100%;
			border-radius: 50%;
			object-fit: cover;
			border: 4px solid #fff;
			box-shadow: 0 8px 25px #4a90e2;
		}

		.profile-avatar::after {
			content: '';
			position: absolute;
			bottom: 5px;
			right: 5px;
			width: 25px;
			height: 25px;
			background: #28a745;
			border-radius: 50%;
			border: 3px solid #fff;
		}

		.profile-info h2 {
			font-size: 24px;
			font-weight: 700;
			margin-bottom: 8px;
			color: #333;
		}

		.profile-info .username {
			font-size: 16px;
			color: #4a90e2;
			font-weight: 600;
			margin-bottom: 5px;
		}

		.profile-info .display-name {
			font-size: 14px;
			color: #666;
		}

		/* Settings Grid */
		.settings-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
			gap: 30px;
			margin-bottom: 30px;
		}

		.settings-card {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(10px);
			border-radius: 20px;
			padding: 30px;
			box-shadow: 0 8px 32px #4a90e2;
			transition: transform 0.3s ease;
		}

		.settings-card:hover {
			transform: translateY(-5px);
		}

		.settings-card h3 {
			font-size: 20px;
			font-weight: 600;
			margin-bottom: 20px;
			color: #333;
			display: flex;
			align-items: center;
			gap: 12px;
		}

		.settings-card h3 i {
			color: #4a90e2;
			font-size: 18px;
		}

		/* Form Styles */
		.form-group {
			margin-bottom: 20px;
		}

		.form-group label {
			display: block;
			margin-bottom: 8px;
			font-weight: 500;
			color: #555;
			font-size: 14px;
		}

		.form-control {
			width: 100%;
			padding: 15px 18px;
			border: 2px solid #e1e5e9;
			border-radius: 12px;
			font-size: 14px;
			transition: all 0.3s ease;
			background: #fff;
		}

		.form-control:focus {
			outline: none;
			border-color: #4a90e2;
			box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
		}

		.form-control::placeholder {
			color: #999;
		}

		/* File Upload */
		.file-upload {
			position: relative;
			display: inline-block;
			cursor: pointer;
			width: 100%;
		}

		.file-upload input[type="file"] {
			position: absolute;
			opacity: 0;
			width: 100%;
			height: 100%;
			cursor: pointer;
		}

		.file-upload-label {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 10px;
			padding: 15px;
			border: 2px dashed #4a90e2;
			border-radius: 12px;
			background: rgba(74, 144, 226, 0.05);
			color: #4a90e2;
			font-weight: 500;
			transition: all 0.3s ease;
		}

		.file-upload:hover .file-upload-label {
			background: rgba(74, 144, 226, 0.1);
			border-color: #3f7fd6;
		}

		/* Password Strength */
		.password-strength {
			margin-top: 8px;
			font-size: 12px;
			font-weight: 500;
		}

		.strength-weak { color: #dc3545; }
		.strength-medium { color: #ffc107; }
		.strength-strong { color: #28a745; }

		/* Buttons */
		.btn-save {
			background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
			color: white;
			padding: 15px 30px;
			border: none;
			border-radius: 12px;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.3s ease;
			width: 100%;
			margin-top: 10px;
		}

		.btn-save:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
		}

		.btn-save:disabled {
			opacity: 0.6;
			cursor: not-allowed;
			transform: none;
		}

		.btn-delete {
			background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
			color: white;
			padding: 15px 30px;
			border: none;
			border-radius: 12px;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.3s ease;
			width: 100%;
			margin-top: 15px;
		}

		.btn-delete:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
		}

		/* Messages */
		.message {
			padding: 15px 20px;
			border-radius: 12px;
			margin-bottom: 20px;
			font-weight: 500;
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.message.success {
			background: rgba(40, 167, 69, 0.1);
			color: #155724;
			border: 1px solid rgba(40, 167, 69, 0.2);
		}

		.message.error {
			background: rgba(220, 53, 69, 0.1);
			color: #721c24;
			border: 1px solid rgba(220, 53, 69, 0.2);
		}

		.message .close-btn {
			margin-left: auto;
			background: none;
			border: none;
			font-size: 18px;
			cursor: pointer;
			color: inherit;
			opacity: 0.7;
			transition: opacity 0.3s ease;
		}

		.message .close-btn:hover {
			opacity: 1;
		}

		/* Modal */
		.modal {
			display: none;
			position: fixed;
			z-index: 1000;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.5);
			backdrop-filter: blur(5px);
			align-items: center;
			justify-content: center;
		}

		.modal-content {
			background: white;
			padding: 30px;
			border-radius: 20px;
			min-width: 400px;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
			animation: modalSlideIn 0.3s ease;
		}

		@keyframes modalSlideIn {
			from {
				opacity: 0;
				transform: translateY(-50px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.modal h3 {
			margin-bottom: 20px;
			color: #333;
			font-size: 18px;
		}

		.modal-checkbox {
			display: flex;
			align-items: center;
			gap: 10px;
			margin-bottom: 12px;
			padding: 10px;
			border-radius: 8px;
			transition: background 0.3s ease;
		}

		.modal-checkbox:hover {
			background: #f8f9fa;
		}

		.modal-checkbox input[type="checkbox"] {
			width: 18px;
			height: 18px;
			accent-color: #4a90e2;
		}

		.modal-actions {
			display: flex;
			gap: 15px;
			margin-top: 25px;
		}

		/* Responsive */
		@media (max-width: 768px) {
			.container {
				padding: 15px;
			}

			.header {
				flex-direction: column;
				gap: 20px;
				text-align: center;
			}

			.profile-overview {
				flex-direction: column;
				text-align: center;
			}

			.settings-grid {
				grid-template-columns: 1fr;
			}

			.modal-content {
				min-width: 90%;
				margin: 20px;
			}
		}

		/* Loading Spinner */
		.spinner {
			display: inline-block;
			width: 16px;
			height: 16px;
			border: 2px solid #ffffff;
			border-radius: 50%;
			border-top-color: transparent;
			animation: spin 1s ease-in-out infinite;
		}

		@keyframes spin {
			to { transform: rotate(360deg); }
		}

		/* Divider */
		.divider {
			height: 1px;
			background: linear-gradient(90deg, transparent, #e1e5e9, transparent);
			margin: 25px 0;
		}
	</style>
</head>
<body>
	<div class="container">
		<!-- Header -->
		<div class="header">
			<h1><i class="fas fa-cog"></i> Account Settings</h1>
			<div class="header-actions">
				<a href="index.php" class="btn btn-primary">
					<i class="fas fa-home"></i> Home
				</a>
				<a href="logout.php" class="btn btn-danger">
					<i class="fas fa-sign-out-alt"></i> Logout
				</a>
			</div>
		</div>

		<!-- Profile Overview -->
		<div class="profile-overview">
			<div class="profile-avatar">
				<img src="<?php echo htmlspecialchars(($user['gambar'] ?? 'uploads/noimg.png')) . '?v=' . time(); ?>" 
					 alt="Profile Picture" id="profilePicPreview">
			</div>
			<div class="profile-info">
				<h2><?php echo htmlspecialchars($user['dn_pengguna'] ?? 'User'); ?></h2>
				<div class="username">@<?php echo htmlspecialchars($username); ?></div>
				<div class="display-name">Member since <?php echo isset($user['tarikh']) ? date('d M Y', strtotime($user['tarikh'])) : date('M Y'); ?></div>
			</div>
		</div>

		<!-- Settings Grid -->
		<div class="settings-grid">
			<!-- Profile Settings -->
			<div class="settings-card">
				<h3><i class="fas fa-user-edit"></i> Profile Information</h3>
				
				<?php if ($success) echo '<div class="message success"><i class="fas fa-check-circle"></i>' . $success . '<button class="close-btn" onclick="this.parentElement.remove()">&times;</button></div>'; ?>
				<?php if ($error) echo '<div class="message error"><i class="fas fa-exclamation-triangle"></i>' . $error . '<button class="close-btn" onclick="this.parentElement.remove()">&times;</button></div>'; ?>

				<form method="POST" enctype="multipart/form-data" id="settingsForm">
					<div class="form-group">
						<label for="profile_pic">Profile Picture</label>
						<div class="file-upload">
							<input type="file" name="profile_pic" id="profile_pic" accept="image/*">
							<label for="profile_pic" class="file-upload-label">
								<i class="fas fa-cloud-upload-alt"></i>
								Choose a new profile picture
							</label>
						</div>
					</div>

					<div class="form-group">
						<label for="display_name">Display Name</label>
						<input type="text" name="display_name" id="display_name" class="form-control" 
							   value="<?php echo htmlspecialchars($user['dn_pengguna'] ?? ''); ?>" 
							   placeholder="Enter your display name">
					</div>

					<button type="submit" class="btn-save" id="saveBtn">
						<i class="fas fa-save"></i> Update Profile
						<span class="spinner" id="spinner" style="display:none;"></span>
					</button>
				</form>
			</div>

			<!-- Security Settings -->
			<div class="settings-card">
				<h3><i class="fas fa-shield-alt"></i> Security Settings</h3>
				
				<form method="POST" id="passwordForm">
					<div class="form-group">
						<label for="old_password">Current Password</label>
						<input type="password" name="old_password" id="old_password" class="form-control" 
							   placeholder="Enter your current password">
					</div>

					<div class="form-group">
						<label for="new_password">New Password</label>
						<input type="password" name="new_password" id="new_password" class="form-control" 
							   placeholder="Enter your new password" oninput="checkPasswordStrength()">
						<div id="password-strength" class="password-strength"></div>
					</div>

					<button type="submit" class="btn-save">
						<i class="fas fa-key"></i> Change Password
					</button>
				</form>

				<div class="divider"></div>

				<h3><i class="fas fa-trash-alt"></i> Danger Zone</h3>
				<p style="color: #666; margin-bottom: 20px; font-size: 14px;">
					Once you delete your account, there is no going back. Please be certain.
				</p>
				<button type="button" class="btn-delete" onclick="confirmDelete()">
					<i class="fas fa-exclamation-triangle"></i> Delete Account
				</button>
			</div>
		</div>
	</div>

	<!-- Delete Confirmation Modal -->
	<div id="deleteModal" class="modal">
		<div class="modal-content">
			<h3><i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i> Delete Account</h3>
			<p style="margin-bottom: 20px; color: #666;">
				Are you sure you want to delete your account? This action cannot be undone and all your data will be permanently lost.
			</p>
			<form method="POST">
				<input type="hidden" name="delete_account" value="1">
				<div class="modal-actions">
					<button type="submit" class="btn btn-danger">
						<i class="fas fa-trash"></i> Yes, Delete My Account
					</button>
					<button type="button" class="btn btn-primary" onclick="closeDeleteModal()">
						<i class="fas fa-times"></i> Cancel
					</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		// Live profile picture preview
		document.getElementById('profile_pic').addEventListener('change', function(e) {
			const [file] = this.files;
			if (file) {
				document.getElementById('profilePicPreview').src = URL.createObjectURL(file);
			}
		});

		// Password strength meter
		function checkPasswordStrength() {
			const pwd = document.getElementById('new_password').value;
			const strengthBox = document.getElementById('password-strength');
			let strength = 0;

			if (pwd.length >= 8) strength++;
			if (/[A-Z]/.test(pwd)) strength++;
			if (/[a-z]/.test(pwd)) strength++;
			if (/[0-9]/.test(pwd)) strength++;
			if (/[^A-Za-z0-9]/.test(pwd)) strength++;

			if (pwd.length === 0) {
				strengthBox.textContent = '';
				strengthBox.className = 'password-strength';
			} else if (strength <= 2) {
				strengthBox.textContent = 'Weak password';
				strengthBox.className = 'password-strength strength-weak';
			} else if (strength === 3 || strength === 4) {
				strengthBox.textContent = 'Medium strength';
				strengthBox.className = 'password-strength strength-medium';
			} else if (strength === 5) {
				strengthBox.textContent = 'Strong password';
				strengthBox.className = 'password-strength strength-strong';
			}
		}

		// Loading spinner on form submission
		document.getElementById('settingsForm').addEventListener('submit', function() {
			document.getElementById('saveBtn').disabled = true;
			document.getElementById('spinner').style.display = 'inline-block';
		});

		document.getElementById('passwordForm').addEventListener('submit', function() {
			const btn = this.querySelector('.btn-save');
			btn.disabled = true;
			btn.innerHTML = '<span class="spinner"></span> Updating...';
		});

		// Delete account confirmation
		function confirmDelete() {
			document.getElementById('deleteModal').style.display = 'flex';
		}

		function closeDeleteModal() {
			document.getElementById('deleteModal').style.display = 'none';
		}

		// Close modal when clicking outside
		document.getElementById('deleteModal').addEventListener('click', function(e) {
			if (e.target === this) {
				closeDeleteModal();
			}
		});

		// Auto-hide messages after 5 seconds
		setTimeout(function() {
			const messages = document.querySelectorAll('.message');
			messages.forEach(function(message) {
				message.style.opacity = '0';
				setTimeout(function() {
					message.remove();
				}, 300);
			});
		}, 5000);
	</script>
</body>
</html>

<?php
mysqli_close($conn);
?>
