<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);

    // Handle avatar upload
    if ($_FILES['avatar']['name']) {
        $avatar = basename($_FILES['avatar']['name']);
        $target = "images/avatars/" . $avatar;
        move_uploaded_file($_FILES['avatar']['tmp_name'], $target);

        $update = $conn->prepare("UPDATE users SET username = ?, profile_image = ? WHERE user_id = ?");
        $update->bind_param("ssi", $new_username, $avatar, $user_id);
    } else {
        $update = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
        $update->bind_param("si", $new_username, $user_id);
    }

    $update->execute();
    $_SESSION['username'] = $new_username;
    header("Location: index.php");
    exit();
}
?>

<h2>Edit Profile</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Username:</label><br>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>
    
    <label>Change Avatar:</label><br>
    <input type="file" name="avatar"><br><br>
    
    <button type="submit">Update Profile</button>
</form>
