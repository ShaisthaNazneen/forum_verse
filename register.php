<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // User inputs
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $security_question = trim($_POST["security_question"]);
    $security_answer = trim($_POST["security_answer"]);

    // Default profile image
    $profile_image_new_name = 'default-avatar.jpg';

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $profile_image = $_FILES['profile_image'];
        $profile_image_name = $profile_image['name'];
        $profile_image_tmp = $profile_image['tmp_name'];
        $profile_image_size = $profile_image['size'];

        $ext = strtolower(pathinfo($profile_image_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $allowed_extensions) && $profile_image_size <= 5000000) {
            $profile_image_new_name = uniqid('avatar_', true) . '.' . $ext;
            $upload_path = 'images/avatars/' . $profile_image_new_name;

            if (!move_uploaded_file($profile_image_tmp, $upload_path)) {
                $message = "Failed to upload profile image.";
                include 'includes/footer.php';
                exit();
            }
        } else {
            $message = "Invalid image type or size (max 5MB).";
            include 'includes/footer.php';
            exit();
        }
    }

    // Insert user with security question/answer
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_image, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $password, $profile_image_new_name, $security_question, $security_answer);

    if ($stmt->execute()) {
        $message = "Registration successful! <a href='login.php'>Login here</a>";
    } else {
        $message = "Registration failed. Try again.";
    }
}
?>

<div class="container mt-5">
    <h2>Register</h2>
    <?php if ($message) echo "<div class='alert alert-info'>$message</div>"; ?>
    <form method="post" enctype="multipart/form-data" class="mt-4">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Security Question</label>
            <select name="security_question" class="form-control" required>
                <option value="">Select a security question</option>
                <option value="Your pet's name?">Your pet's name?</option>
                <option value="Your mother's maiden name?">Your mother's maiden name?</option>
                <option value="Your first school name?">Your first school name?</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Security Answer</label>
            <input type="text" name="security_answer" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Profile Picture</label>
            <input type="file" name="profile_image" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
