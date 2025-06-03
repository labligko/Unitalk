<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['id_account']) || !isset($_POST['receiver_id'])) exit();

$sender_id = $_SESSION['id_account'];
$receiver_id = (int)$_POST['receiver_id'];
$message = trim($_POST['message']);

if ($message !== "") {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    $stmt->execute();
}
?>
