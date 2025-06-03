<?php
include "config/db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_thread']) && isset($_POST['id_comment'])) {
    $postId = intval($_POST['id_thread']);
    $commentId = intval($_POST['id_comment']);

    if (!isset($_SESSION['id_account'])) {
        echo "Anda harus login untuk menghapus komentar.";
        exit;
    }

    $accountId = $_SESSION['id_account'];

    // Periksa apakah data komentar valid
    $checkQuery = "SELECT * FROM comment WHERE id_comment = ? AND id_thread = ? AND id_account = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("iii", $commentId, $postId, $accountId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        echo "Komentar tidak ditemukan atau Anda tidak memiliki hak untuk menghapusnya.";
        exit;
    }

    // Hapus komentar balasan terlebih dahulu
    $deleteRepliesQuery = "DELETE FROM comment WHERE parent_id = ?";
    $deleteRepliesStmt = $conn->prepare($deleteRepliesQuery);
    $deleteRepliesStmt->bind_param("i", $commentId);
    $deleteRepliesStmt->execute();
    $deleteRepliesStmt->close();

    // Hapus komentar
    $deleteQuery = "DELETE FROM comment WHERE id_comment = ? AND id_thread = ? AND id_account = ?";
    $deleteStmt = $conn->prepare($deleteQuery);

    if (!$deleteStmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $deleteStmt->bind_param("iii", $commentId, $postId, $accountId);
    $deleteStmt->execute();

    if ($deleteStmt->affected_rows > 0) {
        echo "Komentar berhasil dihapus!";
    } else {
        echo "Gagal menghapus komentar. Silakan coba lagi.";
        error_log("Input Data: id_comment = $commentId, id_thread = $postId, id_account = $accountId");
    }

    $checkStmt->close();
    $deleteStmt->close();
} else {
    echo "Request tidak valid.";
}

$conn->close();
