<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Our Forum</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('images/back.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            text-align: center;
        }
        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .welcome-box {
            padding: 30px;
            border-radius: 12px;
        }
        h1 {
            font-size: 3rem;
        }
        a {
            margin: 10px;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.1rem;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="welcome-box">
            <h1>🌐 Welcome to the Forum</h1>
            <p>Join conversations, ask questions, and connect with others.</p>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <a href="forum.php">Browse Threads</a>
        </div>
    </div>
</body>
</html>
