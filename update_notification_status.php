<?php
header('Content-Type: application/json');
session_start();
include 'config/db.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'mark_as_read') {
    $user_id = $_SESSION['id_account'];

    // Update status 'likes' berdasarkan thread milik user
    $query_likes = "
        UPDATE likes 
        JOIN threads ON likes.id_thread = threads.id
        SET likes.status = 'read'
        WHERE threads.id_account = $user_id
    ";

    // Update status 'comment' berdasarkan thread milik user
    $query_comment = "
        UPDATE comment 
        JOIN threads ON comment.id_thread = threads.id
        SET comment.status = 'read'
        WHERE threads.id_account = $user_id
    ";

    if (mysqli_query($conn, $query_likes)) {
        if (mysqli_query($conn, $query_comment)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error updating comment: ' . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error updating likes: ' . mysqli_error($conn)]);
    }
}

?>
