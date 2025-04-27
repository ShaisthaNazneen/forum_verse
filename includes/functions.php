<?php

function getUserAvatar($conn, $username) {
    $query = "SELECT profile_image FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && !empty($user['profile_image']) && file_exists('images/avatars/' . $user['profile_image'])) {
        return 'images/avatars/' . $user['profile_image'];
    }

    return 'images/default-avatar.jpg';
}

function getLikeCount($conn, $thread_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS like_count FROM thread_likes WHERE thread_id = ?");
    $stmt->bind_param("i", $thread_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $data['like_count'] ?? 0;
}

function userHasLiked($conn, $thread_id, $user_id) {
    $stmt = $conn->prepare("SELECT 1 FROM thread_likes WHERE thread_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $thread_id, $user_id);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

function getCommentCount($conn, $thread_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS comment_count FROM comments WHERE thread_id = ?");
    $stmt->bind_param("i", $thread_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $data['comment_count'] ?? 0;
}
function getUserIdByUsername($conn, $username) {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user['user_id'] ?? null;
}


function getTagsForThread($conn, $thread_id) {
    $tags = [];
    $stmt = $conn->prepare("
        SELECT t.name 
        FROM thread_tags tt 
        JOIN tags t ON tt.tag_id = t.tag_id 
        WHERE tt.thread_id = ?
    ");
    $stmt->bind_param("i", $thread_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $tags[] = $row['name'];
    }
    return $tags;
}

?>
