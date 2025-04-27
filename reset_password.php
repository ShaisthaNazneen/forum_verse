<?php
include 'includes/db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $query = "SELECT * FROM users WHERE reset_token = '$token' AND token_expiry > NOW()";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $update = "UPDATE users SET password = '$new_password', reset_token = NULL, token_expiry = NULL WHERE reset_token = '$token'";
            mysqli_query($conn, $update);
            echo "Password has been updated. <a href='login.php'>Login</a>";
            exit();
        }
    } else {
        echo "Invalid or expired token.";
        exit();
    }
} else {
    echo "No token provided.";
    exit();
}
?>

<form method="post">
    <label>New Password:</label><br>
    <input type="password" name="password" required><br><br>
    <button type="submit">Reset Password</button>
</form>
