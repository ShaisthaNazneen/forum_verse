<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['reset_user'];
    $input_answer = mysqli_real_escape_string($conn, $_POST['security_answer']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if (strcasecmp($input_answer, $user['security_answer']) === 0) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = "UPDATE users SET password='$hashed_password' WHERE username='$username'";
            mysqli_query($conn, $update);
            session_destroy();
            $message = "✅ Password reset successful. <a href='login.php'>Login now</a>";
        } else {
            $message = "❌ Passwords do not match.";
        }
    } else {
        $message = "❌ Incorrect security answer.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }

        /* Form Styles */
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        p {
            font-size: 16px;
            margin-bottom: 10px;
            color: #555;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            transition: border 0.3s ease;
        }

        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            font-size: 16px;
            color: #333;
            text-align: center;
            margin-top: 20px;
        }

        .error {
            color: #e74c3c;
            font-size: 14px;
        }

        .success {
            color: #2ecc71;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Reset Your Password</h2>
            <form method="post" action="">
                <div class="form-group">
                    <p><strong>Security Question:</strong> <?php echo $_SESSION['reset_question']; ?></p>
                </div>
                <div class="form-group">
                    <input type="text" name="security_answer" placeholder="Your Answer" required>
                </div>
                <div class="form-group">
                    <input type="password" name="new_password" placeholder="New Password" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <button type="submit">Reset Password</button>
            </form>
            <?php if (isset($message)) { ?>
                <div class="message <?php echo strpos($message, '❌') !== false ? 'error' : 'success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
