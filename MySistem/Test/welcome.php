<?php
session_start();
if (!isset($_SESSION['nm_pengguna'])) {
    header("Location: index.php");
    exit();
}
$username = $_SESSION['nm_pengguna'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
