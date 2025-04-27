<?php
session_start();
include 'includes/db.php';

if (isset($_POST['thread_id']) && isset($_SESSION['user_id'])) {
    $thread_id = (int)$_POST['thread_id'];
    $user_id = (int)$_SESSION['user_id'];

    // Check if the user has already liked the thread
    $check_like_query = "SELECT 1 FROM thread_likes WHERE thread_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_like_query);
    $stmt->bind_param('ii', $thread_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $alreadyLiked = $result->num_rows > 0;

    if ($alreadyLiked) {
        // Remove the like
        $delete_like_query = "DELETE FROM thread_likes WHERE thread_id = ? AND user_id = ?";
        $stmt = $conn->prepare($delete_like_query);
        $stmt->bind_param('ii', $thread_id, $user_id);
        $stmt->execute();
        $userHasLiked = false;
    } else {
        // Add the like
        $insert_like_query = "INSERT INTO thread_likes (thread_id, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_like_query);
        $stmt->bind_param('ii', $thread_id, $user_id);
        $stmt->execute();
        $userHasLiked = true;
    }

    // Get the updated like count
    $like_count_query = "SELECT COUNT(*) AS like_count FROM thread_likes WHERE thread_id = ?";
    $stmt = $conn->prepare($like_count_query);
    $stmt->bind_param('i', $thread_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $like_count = $data['like_count'];

    echo json_encode([
        'success' => true,
        'likeCount' => $like_count,
        'userHasLiked' => $userHasLiked
    ]);
}
?>
