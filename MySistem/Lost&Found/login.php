<?php
	include('config.php');
	session_start();

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$username = mysqli_real_escape_string($conn, $_POST['username']);
		$password = $_POST['password'];
		
		// Check for admin credentials first
		if ($username === 'admin' && $password === 'p4$$w0rd') {
			$_SESSION['username'] = $username;
			$_SESSION['display_name'] = 'Administrator';
			$_SESSION['user_type'] = 'admin';
			header("Location: admin_homepage.php");
			exit();
		}
		
		// Regular user authentication
		$query = "SELECT * FROM pengguna WHERE nm_pengguna = '$username'";
		$result = mysqli_query($conn, $query);
		
		if (mysqli_num_rows($result) == 1) {
			$user = mysqli_fetch_assoc($result);
			if (password_verify($password, $user['ps_pengguna'])) {
				$_SESSION['username'] = $username;
				$_SESSION['display_name'] = $user['dn_pengguna'];
				$_SESSION['user_type'] = 'user';
				header("Location: index.php");
				exit();
			} else {
				$error_message = "Invalid password!";
			}
		} else {
			$error_message = "Username not found!";
		}
	}
?>

<html>
    <head>
        <title>Login</title>
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
            <h1><b>Login to Lost & Found</b></h1>
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Enter your username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            <div class="links">
                <a href="signup.php">Create an account</a>
                <a href="index.php">Back to Home</a>
            </div>
        </div>
    </body>
</html>
<?php
mysqli_close($conn);
?>