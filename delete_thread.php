<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$thread_id = $_GET['id'] ?? null;
if (!$thread_id) {
    die("Thread ID missing.");
}

// Check ownership
$stmt = $conn->prepare("SELECT user_id FROM threads WHERE thread_id = ?");
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$result = $stmt->get_result();
$thread = $result->fetch_assoc();

if (!$thread || $thread['user_id'] != $_SESSION['user_id']) {
    die("Unauthorized action.");
}

// Delete thread
$delete = $conn->prepare("DELETE FROM threads WHERE thread_id = ?");
$delete->bind_param("i", $thread_id);
$delete->execute();

header("Location: index.php");
exit();
?>
