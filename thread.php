<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

$thread_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($thread_id < 1) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Invalid thread ID.</div></div>";
    include 'includes/footer.php';
    exit();
}

// Fetch thread details
$query = "SELECT t.*, c.name AS category_name 
          FROM threads t
          LEFT JOIN categories c ON t.category_id = c.category_id
          WHERE t.thread_id = $thread_id";
$result = mysqli_query($conn, $query);
$thread = mysqli_fetch_assoc($result);
$thread_owner = $thread['username'];


if (!$thread) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Thread not found.</div></div>";
    include 'includes/footer.php';
    exit();
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    $insert = "INSERT INTO comments (thread_id, user_id, username, content)
               VALUES ($thread_id, $user_id, '$username', '$comment')";
    mysqli_query($conn, $insert);

    // Redirect to prevent form resubmission
    header("Location: thread.php?id=" . $thread_id);
    exit();
}
?>

<div class="container mt-4">
    <div class="card shadow-sm p-4 mb-4">
        <h3 class="mb-3"><?php echo htmlspecialchars($thread['title']); ?></h3>
        <p class="text-muted mb-2">
            <strong>Posted by:</strong> <?php echo htmlspecialchars($thread['username']); ?> |
            <strong>Category:</strong> <?php echo htmlspecialchars($thread['category_name']); ?> |
            <strong>Date:</strong> <?php echo date("d M Y, h:i A", strtotime($thread['created_at'])); ?>
        </p>
        <hr>
        <p class="lead"><?php echo nl2br(htmlspecialchars($thread['content'])); ?></p>
    </div>

    <h5 class="mb-4">💬 Comments</h5>

    <?php
    $comment_query = "SELECT c.*, u.profile_image FROM comments c 
                      LEFT JOIN users u ON c.user_id = u.user_id
                      WHERE c.thread_id = $thread_id ORDER BY c.created_at DESC";
    $comment_result = mysqli_query($conn, $comment_query);

    if (mysqli_num_rows($comment_result) > 0):
        while ($comment = mysqli_fetch_assoc($comment_result)):
            $profile_image = $comment['profile_image'] ?: 'default-avatar.jpg';
            $avatar_path = file_exists("images/avatars/" . $profile_image) ? "images/avatars/" . $profile_image : "images/default-avatar.jpg";
    ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <img src="<?php echo $avatar_path; ?>" alt="User Avatar" class="rounded-circle border" width="45" height="45">
                    <div class="ms-3">
                        <h6 class="mb-0"><?php echo htmlspecialchars($comment['username']); ?></h6>
                        <small class="text-muted"><?php echo date("d M Y, h:i A", strtotime($comment['created_at'])); ?></small>
                    </div>
                </div>
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                <!-- Action buttons: Edit / Delete -->
<?php if (isset($_SESSION['user_id'])): ?>
    <?php if ($_SESSION['user_id'] == $comment['user_id']): ?>
        <a href="edit_comment.php?id=<?= $comment['comment_id'] ?>" class="btn btn-sm btn-outline-primary me-2 mt-2">✏️ Edit</a>
    <?php endif; ?>
    <?php if ($_SESSION['user_id'] == $comment['user_id'] || $_SESSION['username'] == $thread_owner): ?>
        <a href="delete_comment.php?id=<?= $comment['comment_id'] ?>" class="btn btn-sm btn-outline-danger mt-2" onclick="return confirm('Are you sure you want to delete this comment?')">🗑️ Delete</a>
    <?php endif; ?>
<?php endif; ?>

            </div>
        </div>
    <?php endwhile; else: ?>
        <div class="alert alert-light border">No comments yet. Be the first to comment!</div>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">Add a comment</h6>
                <form method="post">
                    <div class="mb-3">
                        <textarea class="form-control" name="comment" rows="3" required placeholder="Write your comment..."></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Post Comment</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4">Please <a href="login.php">login</a> to post a comment.</div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
