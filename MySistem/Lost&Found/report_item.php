<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include 'config.php';

    $nm_item     = $_POST['nm_item'] ?? '';
    $th_item     = $_POST['th_item'];
    $etc_item    = $_POST['etc_item'] ?? '';
    $status      = $_POST['status'] ?? 'Lost';
    $reward      = $_POST['reward'] ?? '';
    $no_pengguna = $_POST['no_pengguna'] ?? '';
    $nm_pengguna = $_SESSION['display_name'] ?? 'Unknown';
    $un_pengguna = $_SESSION['username'] ?? 'Unknown';

    // Image upload
    $img_item = null;
    if (isset($_FILES['img_item']) && $_FILES['img_item']['error'] === 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $safeName  = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES["img_item"]["name"]));
        $img_item  = $target_dir . time() . "_" . $safeName;
        move_uploaded_file($_FILES["img_item"]["tmp_name"], $img_item);
    }

    $sql = "INSERT INTO item (nm_item, th_item, etc_item, img_item, status, reward, no_pengguna, nm_pengguna, un_pengguna)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $nm_item, $th_item, $etc_item, $img_item, $status, $reward, $no_pengguna, $nm_pengguna, $un_pengguna);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit();
    } else {
        echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
    }
    $stmt->close();
    $conn->close();
}

// --- Defaults for first render so the HTML below can reuse the same classes as edit page ---
$item = [
    'nm_item'     => '',
    'etc_item'    => '',
    'status'      => 'Lost',   // default
    'reward'      => '',
    'no_pengguna' => '',
    'img_item'    => '',       // no image yet
    'id_item'     => null,
    'th_item'     => date('Y-m-d H:i:s'),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Report Item</title>
	<link rel="stylesheet" href="report.css" />
</head>
<body>
	<div class="container">
		<div class="header">
			<h1>Report Item</h1>
			<a href="index.php" class="back-btn">← Back to Homepage</a>
		</div>

		<form method="POST" enctype="multipart/form-data">
			<div class="form-grid">
				<!-- LEFT COLUMN -->
				<div>
					<div class="form-group">
						<label for="nm_item">Item Name</label>
						<input type="text" id="nm_item" name="nm_item"
                               value="<?php echo htmlspecialchars($item['nm_item']); ?>" required />
					</div>

					<div class="form-group">
						<label for="etc_item">Description</label>
						<textarea id="etc_item" name="etc_item"
                                  placeholder="Describe the item, where it was lost/found, etc."><?php
                            echo htmlspecialchars($item['etc_item']);
                        ?></textarea>
					</div>

					<div class="form-group">
						<label>Status</label>
						<div class="status-group">
							<label class="status-option <?php echo ($item['status']==='Lost') ? 'active' : ''; ?>">
								<input type="radio" name="status" value="Lost" <?php echo ($item['status']==='Lost') ? 'checked' : ''; ?> />
								Lost
							</label>
							<label class="status-option <?php echo ($item['status']==='Found') ? 'active' : ''; ?>">
								<input type="radio" name="status" value="Found" <?php echo ($item['status']==='Found') ? 'checked' : ''; ?> />
								Found
							</label>
						</div>
					</div>

					<div class="form-group">
						<label for="reward">Reward (optional)</label>
						<input type="text" id="reward" name="reward"
                               value="<?php echo htmlspecialchars((string)$item['reward']); ?>"
                               placeholder="e.g. RM50" />
					</div>

					<div class="form-group">
						<label for="nm_pengguna">Your Name</label>
						<input type="text" id="nm_pengguna" name="nm_pengguna"
                               value="<?php echo htmlspecialchars($_SESSION['display_name'] ?? ''); ?>"
                               readonly />
					</div>

					<div class="form-group">
    					<label for="no_pengguna">Phone Number</label>
    					<input type="text" id="no_pengguna" name="no_pengguna" value="+60 " required>
					</div>

					<script>
						document.addEventListener("DOMContentLoaded", function () {
    					const input = document.getElementById("no_pengguna");
    					const prefix = "+60 ";

    					// Ensure prefix is always there
    					input.addEventListener("input", () => {
        					if (!input.value.startsWith(prefix)) {
            					input.value = prefix;
        					}

       				 	// Remove non-numeric characters after prefix
        				input.value = prefix + input.value.slice(prefix.length);
    					});

    					// Prevent deleting prefix or typing before it
    					input.addEventListener("keydown", (e) => {
        					if (input.selectionStart < prefix.length && 
            					!["ArrowRight", "ArrowLeft", "Tab"].includes(e.key)) {
            					e.preventDefault();
            					input.setSelectionRange(prefix.length, prefix.length);
        					}
    					});

    					// Keep cursor after prefix
    					input.addEventListener("click", () => {
        					if (input.selectionStart < prefix.length) {
            					input.setSelectionRange(prefix.length, prefix.length);
        					}
    					});
					});
				</script>
				</div>

				<!-- RIGHT COLUMN -->
				<div>
					<div class="form-group">
						<label>Image</label>
						<div class="image-preview">
							<img src="<?php echo htmlspecialchars($item['img_item'] ?: 'noimg.png'); ?>"
                                 alt="Item image" id="preview" />
							<br />
							<label for="img_item" class="file-input">Choose New Image</label>
							<input type="file" id="img_item" name="img_item" accept="image/*" style="display:none;" />
						</div>
					</div>

					<div class="meta-info">
						<!-- For new report, there is no ID yet -->
						<div class="meta-item">
							<h4>Date:</h4> <input type="date" name="th_item" required>
						</div>
						<div class="meta-item">
							<h4>Report Date:<br></h4><strong><h2><?php echo date('d M, Y'); ?></h2></strong>
						</div>
					</div>
				</div>
			</div>

			<div class="actions">
				<a href="index.php" class="btn btn-secondary">Cancel</a>
				<button type="submit" class="btn btn-primary">Submit Report</button>
			</div>
		</form>
	</div>

	<script>
		// Toggle "Lost / Found" styled buttons
		const statusOptions = document.querySelectorAll('.status-option');
		statusOptions.forEach(option => {
			option.addEventListener('click', () => {
				statusOptions.forEach(opt => opt.classList.remove('active'));
				option.classList.add('active');
				const radio = option.querySelector('input[type="radio"]');
				if (radio) radio.checked = true;
			});
		});

		// Live image preview
		const fileInput = document.getElementById('img_item');
		const preview = document.getElementById('preview');
		if (fileInput && preview) {
			fileInput.addEventListener('change', (e) => {
				const file = e.target.files && e.target.files[0];
				if (!file) return;
				const reader = new FileReader();
				reader.onload = (ev) => { preview.src = ev.target.result; };
				reader.readAsDataURL(file);
			});
		}
	</script>
</body>
</html>
