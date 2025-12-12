<?php
	include('config.php');

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$username = mysqli_real_escape_string($conn, $_POST['username']);
		$password = $_POST['password'];
		$confirm_password = $_POST['confirm_password'];
		
		if ($password !== $confirm_password) {
			$error_message = "Passwords do not match!";
		} else {
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
			
			$check_query = "SELECT * FROM pengguna WHERE nm_pengguna = '$username'";
			$result = mysqli_query($conn, $check_query);
			
			if (mysqli_num_rows($result) > 0) {
				$error_message = "Username already exists!";
			} else {
				$current_date = date('Y-m-d H:i:s');
				$insert_query = "INSERT INTO pengguna (nm_pengguna, ps_pengguna, dn_pengguna, tarikh) VALUES ('$username', '$hashed_password', '$username', '$current_date')";
				
				if (mysqli_query($conn, $insert_query)) {
					header("Location: login.php");
					exit();
				} else {
					$error_message = "Error: " . mysqli_error($conn);
				}
			}
		}
	}
?>

<html>
    <head>
        <title>Sign Up</title>
        <style>
            body {
    background-color: #f4f6f8;
    margin: 0;
    font-family: Arial, sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.header {
    text-align: center;
    margin: 40px 0;
}

.header img {
    width: 300px;
    height: 70px;
    margin-bottom: 24px;
}

.container {
    width: 308px;
    margin: 0 auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

h1 {
    font-size: 24px;
    font-weight: 400;
    text-align: center;
    margin-bottom: 24px;
    color: #333333;
}

.form-group {
    margin-bottom: 16px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 14px;
    color: #333333;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 8px 12px;
    font-size: 14px;
    line-height: 20px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    background-color: #ffffff;
    color: #333333;
}

input[type="text"]:focus,
input[type="password"]:focus {
    border-color: #007BFF;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

input::placeholder {
    color: #999999;
}

.btn {
    width: 100%;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 16px;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #0056b3;
}

.links {
    text-align: center;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #e0e0e0;
}

.links a {
    color: #007BFF;
    text-decoration: none;
    font-size: 12px;
    margin: 0 8px;
}

.links a:hover {
    text-decoration: underline;
}

.error-message {
    color: #d93025;
    background-color: #fce8e6;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 16px;
    text-align: center;
    font-size: 14px;
}

        </style>
    </head>
    <body>
        <div class="header">
            <img src="banner.png">
        </div>
        <div class="container">
            <h1><b>Sign up to Lost & Found</b></h1>
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Choose a username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Create a password">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                </div>
                <button type="submit" class="btn">Create Account</button>
            </form>
            <div class="links">
                <a href="login.php">Already have an account? Login</a>
                <a href="index.php">Back to Home</a>
            </div>
        </div>
        <footer>
            <br><br><br>
        </footer>
    </body>
</html>
<?php
mysqli_close($conn);
?>