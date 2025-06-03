<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['user_id']) && isset($data['friend_id'])) {
        $userId = $data['user_id'];
        $friendId = $data['friend_id'];

        $checkQuery = "SELECT * FROM friendlist WHERE user_id = ? AND friend_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $userId, $friendId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Permintaan sudah ada."]);
        } else {
            $insertQuery = "INSERT INTO friendlist (user_id, friend_id, status) VALUES (?, ?, 'following')";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ii", $userId, $friendId);

            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => "Gagal menambah pertemanan."]);
            }
        }
    } else {
        echo json_encode(["success" => false, "message" => "User belum login atau data tidak lengkap."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Metode tidak didukung."]);
}
?>