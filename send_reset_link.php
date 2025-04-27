<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $update = "UPDATE users SET reset_token = '$token', token_expiry = '$expiry' WHERE email = '$email'";
        mysqli_query($conn, $update);

        $resetLink = "http://yourdomain.com/reset_password.php?token=$token";

        // Send the email
        $subject = "Password Reset Link";
        $message = "Click the following link to reset your password:\n\n$resetLink";
        $headers = "From: noreply@yourdomain.com";

        mail($email, $subject, $message, $headers);
        
        echo "If this email is registered, a reset link has been sent.";
    } else {
        echo "If this email is registered, a reset link has been sent.";
    }
}
?>
