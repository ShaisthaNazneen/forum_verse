<?php
session_start();
include 'includes/db.php';

if (isset($_POST['reaction']) && isset($_POST['thread_id']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $thread_id = $_POST['thread_id'];
    $reaction = $_POST['reaction'];

    // Check if user already reacted with the same reaction
    $checkReaction = mysqli_query($conn, "SELECT * FROM thread_reactions WHERE thread_id = '$thread_id' AND user_id = '$user_id' AND reaction_type = '$reaction'");
    
    if (mysqli_num_rows($checkReaction) == 0) {
        // Add the new reaction
        $insertReaction = mysqli_query($conn, "INSERT INTO thread_reactions (thread_id, user_id, reaction_type) VALUES ('$thread_id', '$user_id', '$reaction')");
        
        if ($insertReaction) {
            // Get new like and love counts
            $likeCount = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM thread_reactions WHERE thread_id = '$thread_id' AND reaction_type = 'like'"));
            $loveCount = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM thread_reactions WHERE thread_id = '$thread_id' AND reaction_type = 'love'"));

            // Return the updated counts
            echo json_encode(['likeCount' => $likeCount, 'loveCount' => $loveCount]);
        }
    }
}
?>
