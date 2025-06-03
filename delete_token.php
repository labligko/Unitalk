<?php
include "config/db.php";

$sql = "DELETE FROM password_resets WHERE expires_at <= NOW()";

if ($conn->query($sql) === TRUE) {
    echo "Token expired berhasil dihapus.";
    file_put_contents('cron_log.txt', date('Y-m-d H:i:s') . " - Tokens expired deleted\n", FILE_APPEND);
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>