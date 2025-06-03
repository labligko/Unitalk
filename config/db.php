<?php
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "forum_db"
);

if (mysqli_connect_error()) {
    echo "koneksi gagal " . mysqli_connect_error();
}
