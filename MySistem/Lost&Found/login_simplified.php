<?php
include('config.php');
include('includes/functions.php');
session_start();

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Admin authentication
    if ($username === '4w@n' && $password === 'UeH8@%Nish') {
        $_SESSION['username'] = $username;
        $_SESSION['display_name'] = 'Administrator';
        $_SESSION['user_type'] = 'admin';
        header("Location: admin.php");
        exit();
    }
    
    // Regular user authentication
    $query = "SELECT * FROM pengguna WHERE nm_pengguna = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
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
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Lost & Found</title>
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
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            margin-bottom: 16px;
        }

        .btn-login:hover {
            background-color: #357abd;
        }

        .signup-link {
            text-align: center;
            font-size: 14px;
            color: #666666;
        }

        .signup-link a {
            color: #4a90e2;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="banner.png" alt="Lost & Found Logo">
    </div>
    
    <div class="container">
        <h1>Login</h1>
        
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo e($error_message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Login</button>
        </form>
        
        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign up</a>
        </div>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?> 