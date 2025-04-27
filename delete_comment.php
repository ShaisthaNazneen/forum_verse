<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$comment_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$query = "SELECT c.thread_id, c.user_id AS comment_author, t.username AS thread_owner 
          FROM comments c 
          JOIN threads t ON c.thread_id = t.thread_id 
          WHERE c.comment_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();

if ($comment && ($comment['comment_author'] == $user_id || $comment['thread_owner'] == $_SESSION['username'])) {
    $delete = $conn->prepare("DELETE FROM comments WHERE comment_id = ?");
    $delete->bind_param("i", $comment_id);
    $delete->execute();

    header("Location: thread.php?id=" . $comment['thread_id']);
    exit();
} else {
    echo "Unauthorized to delete this comment.";
}
?>
