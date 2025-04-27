<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location: create_thread.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User does not exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Forum</title>
    <style>
        body {
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
            width: 360px;
            text-align: center;
        }
        .login-container img.logo {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 90%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }
        .password-wrapper {
            position: relative;
            display: inline-block;
            width: 90%;
        }
        .password-wrapper input {
            width: 100%;
        }
        .password-wrapper i {
            position: absolute;
            right: 12px;
            top: 14px;
            cursor: pointer;
            color: #666;
        }
        .login-container button {
            background-color: #0077cc;
            color: white;
            padding: 12px 20px;
            margin-top: 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background 0.3s ease;
        }
        .login-container button:hover {
            background-color: #005fa3;
        }
        .error {
            color: red;
            margin-top: 15px;
        }
        .links {
            margin-top: 15px;
        }
        .links a {
            text-decoration: none;
            color: #0077cc;
            margin: 0 8px;
            font-size: 14px;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .google-login-btn {
            background-color: #4285f4;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background 0.3s ease;
            margin-top: 20px;
        }
        .google-login-btn:hover {
            background-color: #357ae8;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="images/forum-logo.png" alt="Forum Logo" class="logo">
        <h2>Login to Forum</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>

            <div class="password-wrapper">
                <input type="password" name="password" placeholder="Password" id="passwordInput" required>
                <i class="toggle-password" onclick="togglePassword()">👁️</i>
            </div>

            <button type="submit">Login</button>
        </form>
        
        <div class="links">
            <a href="register.php">Sign up</a> |
            <a href="forgot_password.php">Forgot password?</a>
        </div>

        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

        <!-- Google Login Button -->
        <!-- Replace YOUR_GOOGLE_CLIENT_ID with your actual ID -->
<!-- Client ID from your message: 463908010912-a4nlc49bek2mp1hdffb92ij0tq9mob8a.apps.googleusercontent.com -->
<a href="https://accounts.google.com/o/oauth2/v2/auth?client_id=463908010912-a4nlc49bek2mp1hdffb92ij0tq9mob8a.apps.googleusercontent.com&redirect_uri=http://localhost/forum/google_callback.php&response_type=code&scope=email%20profile&access_type=online&include_granted_scopes=true&prompt=select_account">
    <button type="button" class="google-login-btn">Login with Google</button>
</a>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById("passwordInput");
            const eye = document.querySelector(".toggle-password");
            if (input.type === "password") {
                input.type = "text";
                eye.textContent = "👁️‍🗨️";
            } else {
                input.type = "password";
                eye.textContent = "👁️";
            }
        }
    </script>
</body>
</html>
