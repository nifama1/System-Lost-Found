<?php
	session_start();
	if (!isset($_SESSION['username'])) {
		header('Location: login.php');
		exit();
	}

	require_once 'config.php';

	// Validate item id
	$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	if ($item_id <= 0) {
		http_response_code(400);
		echo '<p style="text-align:center;margin:40px auto;font-family:Segoe UI,Arial,sans-serif;color:#a33;">Invalid item ID.</p>';
		exit();
	}

	$current_username = $_SESSION['username'];
	$item = null;
	$error = '';
	$success = '';

	// Fetch item ensuring ownership
	$stmt = mysqli_prepare($conn, "SELECT id_item, nm_item, th_item, etc_item, img_item, status, reward, no_pengguna, nm_pengguna, un_pengguna FROM item WHERE id_item = ? AND un_pengguna = ? LIMIT 1");
	mysqli_stmt_bind_param($stmt, 'is', $item_id, $current_username);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	if ($result && mysqli_num_rows($result) === 1) {
		$item = mysqli_fetch_assoc($result);
	} else {
		http_response_code(404);
		echo '<p style="text-align:center;margin:40px auto;font-family:Segoe UI,Arial,sans-serif;color:#a33;">Item not found or you do not have permission to edit this item.</p>';
		exit();
	}
	mysqli_stmt_close($stmt);

			// Handle update submission
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$nm_item = trim($_POST['nm_item'] ?? '');
		$etc_item = trim($_POST['etc_item'] ?? '');
		$th_item = trim($_POST['th_item'] ?? '');
		$status = trim($_POST['status'] ?? '');
		$reward = trim($_POST['reward'] ?? '');
		$nm_pengguna = trim($_POST['nm_pengguna'] ?? ($item['nm_pengguna'] ?? ''));
		$no_pengguna = trim($_POST['no_pengguna'] ?? '');

		if ($status !== 'Lost' && $status !== 'Found') {
			$status = $item['status'];
		}

		// Prepare image (optional)
		$new_image_path = $item['img_item'];
		if (isset($_FILES['img_item']) && isset($_FILES['img_item']['tmp_name']) && is_uploaded_file($_FILES['img_item']['tmp_name'])) {
			$original_name = basename($_FILES['img_item']['name']);
			$target_dir = 'uploads/';
			if (!is_dir($target_dir)) {
				@mkdir($target_dir, 0777, true);
			}
			$sanitized_name = preg_replace('/[^A-Za-z0-9._-]/', '_', $original_name);
			$new_image_path = $target_dir . time() . '_' . $sanitized_name;
			if (!move_uploaded_file($_FILES['img_item']['tmp_name'], $new_image_path)) {
				$new_image_path = $item['img_item'];
			}
		}

		// Build update query (always keep ownership constraint)
		$update_sql = "UPDATE item SET nm_item = ?, etc_item = ?, status = ?, reward = ?, nm_pengguna = ?, no_pengguna = ?, img_item = ?, th_item = ? WHERE id_item = ? AND un_pengguna = ?";
		$update_stmt = mysqli_prepare($conn, $update_sql);
		if ($update_stmt) {
			mysqli_stmt_bind_param($update_stmt, 'ssssssssis', $nm_item, $etc_item, $status, $reward, $nm_pengguna, $no_pengguna, $new_image_path, $th_item, $item_id, $current_username);
			if (mysqli_stmt_execute($update_stmt)) {
				// Redirect to index.php after successful update
				header('Location: userReports.php');
				exit();
			} else {
				$error = 'Failed to update item.';
			}
			mysqli_stmt_close($update_stmt);
		} else {
			$error = 'Unable to prepare update statement.';
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Edit Item • Lost & Found</title>
	<link rel="stylesheet" href="report.css">
</head>
<body>
	<div class="container">
		<div class="header">
			<h1>Edit Item</h1>
			<a href="userReports.php" class="back-btn">← Back to Reports</a>
		</div>

		<?php if ($success): ?>
			<div class="alert success"><?php echo htmlspecialchars($success); ?></div>
		<?php endif; ?>
		<?php if ($error): ?>
			<div class="alert error"><?php echo htmlspecialchars($error); ?></div>
		<?php endif; ?>

		<form method="POST" enctype="multipart/form-data">
			<div class="form-grid">
				<div>
					<div class="form-group">
						<label for="nm_item">Item Name</label>
						<input type="text" id="nm_item" name="nm_item" value="<?php echo htmlspecialchars($item['nm_item']); ?>" required>
					</div>

					<div class="form-group">
						<label for="etc_item">Description</label>
						<textarea id="etc_item" name="etc_item" placeholder="Describe the item, where it was lost/found, etc."><?php echo htmlspecialchars($item['etc_item']); ?></textarea>
					</div>

					<div class="form-group">
						<label>Status</label>
						<div class="status-group">
							<label class="status-option <?php echo ($item['status']==='Lost') ? 'active' : ''; ?>">
								<input type="radio" name="status" value="Lost" <?php echo ($item['status']==='Lost') ? 'checked' : ''; ?>>
								Lost
							</label>
							<label class="status-option <?php echo ($item['status']==='Found') ? 'active' : ''; ?>">
								<input type="radio" name="status" value="Found" <?php echo ($item['status']==='Found') ? 'checked' : ''; ?>>
								Found
							</label>
						</div>
					</div>

					<div class="form-group">
						<label for="reward">Reward (optional)</label>
						<input type="text" id="reward" name="reward" value="<?php echo htmlspecialchars((string)$item['reward']); ?>" placeholder="e.g. RM50">
					</div>

					<div class="form-group">
						<label for="nm_pengguna">Your Name</label>
						<input type="text" id="nm_pengguna" name="nm_pengguna" value="<?php echo htmlspecialchars($item['nm_pengguna']); ?>">
					</div>

					<div class="form-group">
						<label for="no_pengguna">Phone Number</label>
						<input type="text" id="no_pengguna" name="no_pengguna" value="<?php echo htmlspecialchars($item['no_pengguna']); ?>" required>
					</div>
				</div>

				<div>
					<div class="form-group">
						<label>Image</label>
						<div class="image-preview">
							<img src="<?php echo htmlspecialchars($item['img_item'] ?: 'noimg.png'); ?>" alt="Item image" id="preview">
							<br>
							<label for="img_item" class="file-input">Choose New Image</label>
							<input type="file" id="img_item" name="img_item" accept="image/*" style="display:none;">
						</div>
					</div>

					<div class="meta-info">
						<div class="meta-item">
							<h4>Date:</h4> <input type="date" name="th_item" value="<?php echo htmlspecialchars($item['th_item']); ?>" required>
						</div>
						<div class="meta-item">
							<h4>Updated Date:<br></h4><strong><h2><?php echo date('d M, Y'); ?></h2></strong>
						</div>
					</div>
				</div>
			</div>

			<div class="actions">
				<a href="userReports.php" class="btn btn-secondary">Cancel</a>
				<button type="submit" class="btn btn-primary">Save Changes</button>
			</div>
		</form>
	</div>

	<script>
		// Status selection
		const statusOptions = document.querySelectorAll('.status-option');
		statusOptions.forEach(option => {
			option.addEventListener('click', () => {
				statusOptions.forEach(opt => opt.classList.remove('active'));
				option.classList.add('active');
				const radio = option.querySelector('input[type="radio"]');
				if (radio) radio.checked = true;
			});
		});

		// Image preview
		const fileInput = document.getElementById('img_item');
		const preview = document.getElementById('preview');
		if (fileInput && preview) {
			fileInput.addEventListener('change', (e) => {
				const file = e.target.files && e.target.files[0];
				if (!file) return;
				const reader = new FileReader();
				reader.onload = (ev) => {
					preview.src = ev.target.result;
				};
				reader.readAsDataURL(file);
			});
		}
	</script>
</body>
</html>


