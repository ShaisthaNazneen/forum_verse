<?php
session_start(); // Ensure the session is started to access session variables
include 'includes/db.php';
include 'includes/header.php';

// Get the thread ID from the URL
$thread_id = $_GET['id'];

// Check if the thread exists
$query = "SELECT * FROM threads WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$thread_result = $stmt->get_result();
$thread = $thread_result->fetch_assoc();

// If no thread is found, show a 404 or error message
if (!$thread) {
    echo "Thread not found.";
    exit();
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    // Prepared statement to insert comment
    $stmt = $conn->prepare("INSERT INTO comments (thread_id, user_id, username, content) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $thread_id, $user_id, $username, $comment);
    $stmt->execute();
}

// Fetch comments for the thread
$comments_query = "SELECT * FROM comments WHERE thread_id = ? ORDER BY created_at ASC";
$stmt = $conn->prepare($comments_query);
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$comments_result = $stmt->get_result();
?>

<div class="card mb-4">
    <div class="card-body">
        <h3><?= htmlspecialchars($thread['title']) ?></h3>
        <p><?= nl2br(htmlspecialchars($thread['content'])) ?></p>
        <small class="text-muted">Posted by <?= htmlspecialchars($thread['username']) ?> on <?= $thread['created_at'] ?></small>
    </div>
</div>

<h5>Comments</h5>
<?php while ($c = mysqli_fetch_assoc($comments_result)): ?>
    <div class="border-bottom mb-2">
        <strong><?= htmlspecialchars($c['username']) ?>:</strong> <?= nl2br(htmlspecialchars($c['content'])) ?>
        <div class="text-muted small"><?= $c['created_at'] ?></div>
    </div>
<?php endwhile; ?>

<?php if (isset($_SESSION['user_id'])): ?>
    <form method="post" class="mt-4">
        <textarea name="comment" class="form-control" rows="3" required></textarea>
        <button class="btn btn-primary mt-2">Post Comment</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Login</a> to comment.</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
