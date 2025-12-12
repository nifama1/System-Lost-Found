<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
// Lost&Found/reportFound.php

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    include 'config.php';

    // Collect form data
    $nm_item = $_POST['nm_item'];
    $th_item = $_POST['th_item'];
    $etc_item = $_POST['etc_item'];
    $status = $_POST['status'];
    $reward = $_POST['reward'];
    $no_pengguna = $_POST['no_pengguna'];
    $nm_pengguna = isset($_SESSION['display_name']) ? $_SESSION['display_name'] : 'Unknown';
    $un_pengguna = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';

    // Handle image upload
    $img_item = null;
    if (isset($_FILES['img_item']) && $_FILES['img_item']['error'] == 0) {
        $target_dir = "uploads/";
        $img_item = $target_dir . time() . "_" . basename($_FILES["img_item"]["name"]);
        move_uploaded_file($_FILES["img_item"]["tmp_name"], $img_item);
    }

    // Insert into database
    $sql = "INSERT INTO item (nm_item, th_item, etc_item, img_item, status, reward, no_pengguna, nm_pengguna, un_pengguna)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $nm_item, $th_item, $etc_item, $img_item, $status, $reward, $no_pengguna, $nm_pengguna, $un_pengguna);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit();
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Found Item</title>
    <link rel="stylesheet" href="home.css">
    <style>
        body {
            background: #f4f6fb;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .report-container {
            max-width: 440px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            padding: 36px 32px 28px 32px;
        }
        h2 {
            text-align: center;
            color: #2d3a4a;
            margin-bottom: 28px;
            font-size: 2rem;
            letter-spacing: 1px;
        }
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #3a4a5d;
            font-weight: 600;
            font-size: 1rem;
        }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="file"],
        .form-group textarea {
            width: 100%;
            padding: 10px 12px 10px 38px;
            border: 1px solid #d1d9e6;
            border-radius: 7px;
            font-size: 1rem;
            background: #f9fafc;
            transition: border 0.2s;
        }
        .form-group input[type="text"]:focus,
        .form-group input[type="date"]:focus,
        .form-group textarea:focus {
            border: 1.5px solid #4a90e2;
            outline: none;
            background: #fff;
        }
        .form-group textarea {
            min-height: 70px;
            resize: vertical;
        }
        .form-group input[type="file"] {
            background: none;
            border: none;
            padding-left: 0;
        }
        .form-group .icon {
            position: absolute;
            left: 10px;
            top: 38px;
            font-size: 1.1em;
            color: #b0b8c9;
            pointer-events: none;
        }
        .helper-text {
            font-size: 0.92em;
            color: #7a869a;
            margin-top: 2px;
            margin-bottom: 0;
        }
        .success-message {
            background: #e6f9ed;
            color: #218838;
            border: 1px solid #b7ebc6;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }
        .success-message .checkmark {
            font-size: 1.3em;
        }
        input[type="submit"] {
            width: 100%;
            background: #4a90e2;
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 13px 0;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
            box-shadow: 0 2px 8px rgba(74,144,226,0.08);
        }
        input[type="submit"]:hover, input[type="submit"]:focus {
            background: #357abd;
        }
        .back-button {
            display: inline-block;
            background:#4a90e2;
            color: #fff;
            text-decoration: none;
            border-radius: 7px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 20px;
            transition: background 0.2s;
            box-shadow: 0 2px 8px rgba(108,117,125,0.08);
        }
        .back-button:hover {
            background: #357abd;
            color: #fff;
            text-decoration: none;
        }
        .button-container {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .button-container input[type="submit"] {
            flex: 1;
            margin-top: 0;
        }
        .button-container .back-button {
            flex: 0 0 auto;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <h2>Report a Found Item</h2>
        <?php if (isset($success) && $success): ?>
            <div class="success-message">
                <span class="checkmark">&#10003;</span>
                Item reported successfully!
            </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="form-group">
                <label>Tarikh Laporan</label>
                <span class="icon">&#128197;</span>
                <input type="date" value="<?php echo date('Y-m-d'); ?>" readonly required>
                <div class="helper-text">Tarikh laporan ini direkod secara automatik.</div>
            </div>
            <div class="form-group">
                <label>Item Name</label>
                <span class="icon">&#128230;</span>
                <input type="text" name="nm_item" placeholder="Contoh: Dompet, Kunci" required>
            </div>
            <div class="form-group">
                <label>Tarikh</label>
                <span class="icon">&#128197;</span>
                <input type="date" name="th_item" required>
                <div class="helper-text">Tarikh item dijumpai.</div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <span class="icon">&#128221;</span>
                <textarea name="etc_item" placeholder="Maklumat tambahan tentang item..."></textarea>
            </div>
            <div class="form-group">
                <label>Image</label>
                <span class="icon"></span>
                <input type="file" name="img_item" accept="image/*">
            </div>
            <div class="form-group">
                <label>Status</label>
                <span class="icon">&#128273;</span>
                <input type="text" name="status" value="Found" required readonly>
            </div>
            <div class="form-group">
                <label>Your Name</label>
                <span class="icon">&#128100;</span>
                <input type="text" name="nm_pengguna" value="<?php echo isset($_SESSION['display_name']) ? htmlspecialchars($_SESSION['display_name']) : 'Unknown'; ?>" readonly>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <span class="icon">&#128222;</span>
                <input type="text" name="no_pengguna" placeholder="+60 12 345 6789" required>
                <div class="helper-text">Nombor telefon untuk dihubungi.</div>
            </div>
            <div class="button-container">
                <a href="index.php" class="back-button">Back</a>
                <input type="submit" value="Report Item">
            </div>
        </form>
    </div>
</body>
</html>
