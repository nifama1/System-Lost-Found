<?php
session_start();
require_once 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['nm_pengguna']; // edit yang kat sini ikut nama column dalam table pengguna ko
    $password = $_POST['ps_pengguna']; // yang lain-lain un tukar ah ikut nama column ko

    $sql = "SELECT * FROM pengguna WHERE nm_pengguna='$username'"; // nama table un tukar kalau lain
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        if ($password === $row['ps_pengguna']) {
            $_SESSION['nm_pengguna'] = $username;
            header("Location: welcome.php"); // tukar ah ni jadi index.php (kalau tu main la)
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak wujud!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            background:rgb(107, 110, 112);
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 550px;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 48px white;
            min-width: 220px;
            max-width: 260px;
        }
        h3 {
            text-align: center;
            margin-bottom: 15px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: 500;
            margin-bottom: 15px;
            font-size: 15px;
        }
        input[type="text"], input[type="password"] {
            padding: 0.4rem 0.7rem;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            font-size: 15px;
            background: #f9fafb;
            transition: border 0.2s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border: 1.5px solid #007bff;
            outline: none;
            background: #fff;
        }
        button[type="submit"] {
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        button[type="submit"]:hover {
            background: #0056b3;
        }
        .error-message {
            color: #d90429;
            background: #ffeaea;
            border: 1px solid #f5c2c7;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h3>Login</h3>
        <?php if (isset($error)) echo "<div class='error-message'>$error</div>"; ?>
        <form method="post" action="">
            <input type="text" id="nm_pengguna" name="nm_pengguna" placeholder="Username" required>
            <input type="password" id="ps_pengguna" name="ps_pengguna" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
