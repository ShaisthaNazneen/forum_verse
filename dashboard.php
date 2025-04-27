<?php
session_start();
include("config.php"); // Database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch user info from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Welcome, <?php echo $user['username']; ?>!</h2>
        <div class="row mt-3">
            <div class="col-md-6">
                <h4>Your Profile</h4>
                <ul>
                    <li><strong>Username:</strong> <?php echo $user['username']; ?></li>
                    <li><strong>Email:</strong> <?php echo $user['email']; ?></li>
                    <li><strong>Member since:</strong> <?php echo date("F j, Y", strtotime($user['created_at'])); ?></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h4>Recent Posts</h4>
                <?php
                // Fetch recent posts made by the user
                $post_query = "SELECT * FROM posts WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 5";
                $post_result = mysqli_query($conn, $post_query);

                if (mysqli_num_rows($post_result) > 0) {
                    while ($post = mysqli_fetch_assoc($post_result)) {
                        echo "<div class='post mb-3'>";
                        echo "<h5>" . $post['title'] . "</h5>";
                        echo "<p>" . substr($post['content'], 0, 100) . "...</p>";
                        echo "<a href='post.php?id=" . $post['id'] . "'>Read more</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No recent posts found.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
