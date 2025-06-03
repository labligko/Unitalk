<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['id_account']) || !isset($_GET['receiver_id'])) exit();

$id_user = $_SESSION['id_account'];
$receiver_id = (int)$_GET['receiver_id'];

$query = "SELECT * FROM messages 
          WHERE (sender_id = ? AND receiver_id = ?) 
             OR (sender_id = ? AND receiver_id = ?)
          ORDER BY timestamp ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $id_user, $receiver_id, $receiver_id, $id_user);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $isMe = $row['sender_id'] == $id_user;
    $class = $isMe ? 'text-right text-blue-600' : 'text-left text-black ';
    echo "<p class='$class mb-1'>" . htmlspecialchars($row['message']) . "</p>";
}
?>
