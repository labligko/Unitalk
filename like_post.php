<?php
include 'config/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'];
$id_account = $data['id_account'];

$sql_check = "SELECT * FROM likes WHERE id_thread = '$id' AND id_account = '$id_account'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) == 0) {
    $sql_like = "INSERT INTO likes (id_thread, id_account, status) VALUES ('$id', '$id_account', 'unread')";
    $sql_update_thread = "UPDATE thread SET likes = likes + 1 WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql_like) && mysqli_query($conn, $sql_update_thread)) {
        $sql_likes_count = "SELECT likes FROM thread WHERE id = '$id'";
        $result_likes_count = mysqli_query($conn, $sql_likes_count);
        $likes_count = mysqli_fetch_assoc($result_likes_count)['likes'];

        echo json_encode(["success" => true, "likes" => $likes_count]);
    } else {
        echo json_encode(["success" => false, "message" => "database_error"]);
    }
} else {
    $sql_unlike = "DELETE FROM likes WHERE id_thread = '$id' AND id_account = '$id_account'";
    $sql_update_thread = "UPDATE thread SET likes = likes - 1 WHERE id = '$id'";

    if (mysqli_query($conn, $sql_unlike) && mysqli_query($conn, $sql_update_thread)) {
        $sql_likes_count = "SELECT likes FROM thread WHERE id = '$id'";
        $result_likes_count = mysqli_query($conn, $sql_likes_count);
        $likes_count = mysqli_fetch_assoc($result_likes_count)['likes'];

        echo json_encode(["success" => true, "likes" => $likes_count]);
    } else {
        echo json_encode(["success" => false, "message" => "database_error"]);
    }
}
