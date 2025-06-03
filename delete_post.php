<?php
include "config/db.php";

if (isset($_SESSION['username'])) {
    header("Location: /index.php");
    exit;
}

$id = $_GET['id'];
echo $id;

// Hapus file media
$media = "SELECT media FROM threads WHERE id='$id'";
$result = $conn->query($media);

if ($result) {
    unlink("assets/media/" . $result->fetch_assoc()['media']);
    
    // Hapus likes terkait
    $deleteLikes = "DELETE FROM likes WHERE id_thread='$id'";
    $conn->query($deleteLikes);

    // Hapus komentar terkait
    $deleteComments = "DELETE FROM comment WHERE id_thread='$id'";
    $conn->query($deleteComments);

    // Hapus thread
    $sql = "DELETE FROM threads WHERE id='$id'";
    $query = $conn->query($sql);
    
    if ($query) {
        header('Location: user_profile.php');
    } else {
        die("Gagal menghapus thread...");
    }
} else {
    die("Thread tidak ditemukan...");
}
?>
