<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | Forum</title>
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .forgot-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
            width: 400px;
            max-width: 90%;
            text-align: center;
        }

        .forgot-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .forgot-container input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            margin: 15px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        .forgot-container button {
            background-color: #ff7e5f;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background 0.3s ease;
        }

        .forgot-container button:hover {
            background-color: #eb6143;
        }

        .forgot-container a {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #0077cc;
        }

        .forgot-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <h2>Forgot Your Password?</h2>
        <form method="post" action="verify_security_answer.php">
            <input type="text" name="username" placeholder="Enter your username" required>
            <button type="submit">Next</button>
        </form>
        <a href="login.php">Back to Login</a>
    </div>
</body>
</html>
