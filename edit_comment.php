<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$comment_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM comments WHERE comment_id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $comment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();

if (!$comment) {
    echo "You can only edit your own comments.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_content = htmlspecialchars(trim($_POST['content']));

    $update = $conn->prepare("UPDATE comments SET content = ? WHERE comment_id = ?");
    $update->bind_param("si", $updated_content, $comment_id);
    $update->execute();

    header("Location: thread.php?id=" . $comment['thread_id']);
    exit();
}
?>

<h2>Edit Comment</h2>
<form method="POST">
    <textarea name="content" rows="5" cols="50"><?= htmlspecialchars($comment['content']) ?></textarea><br>
    <button type="submit">Update</button>
</form>
