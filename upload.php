<?php
include 'config/db.php';

session_start();

$id = $_SESSION['id_account'];

$rand = rand();

$ekstensi_video = array('mp4', 'avi', 'mov', 'mkv');
$ekstensi_image = array('png', 'jpg', 'jpeg', 'gif');

$caption = $_POST['caption'];
$file = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '';
$file_tmp = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';
$ext = $file ? pathinfo($file, PATHINFO_EXTENSION) : '';

if ($file != '') {
    if (in_array($ext, $ekstensi_video)) {
        $tipe = 'video';
        $max_size = 268435456;
        $upload_dir = 'assets/media/';
        $converted_file_name = 'converted_' . $file;

        $command_ffmpeg = "ffmpeg -i $file_tmp -c:v libx264 -c:a aac -strict experimental $upload_dir$converted_file_name";
        exec($command_ffmpeg);
        $file = $converted_file_name;

        if ($_FILES['file']['size'] < $max_size) {
            $file_name = $rand . '_' . $file;
            move_uploaded_file($file_tmp, $upload_dir . $file_name);

            mysqli_query($conn, "INSERT INTO threads (media, captions, id_account, jenis) VALUES ('$file_name', '$caption', '$id', '$tipe')") or die(mysqli_error($conn));

            $post_id = $_GET['id'];
            header("Location: index.php?id=$post_id&alert=berhasil");
            exit;
        } else {
            header("location:index.php?alert=gagal_ukuran");
            exit;
        }
    } elseif (in_array($ext, $ekstensi_image)) {
        $tipe = 'gambar';
        $max_size = 52428800;
        $upload_dir = 'assets/media/';

        if ($_FILES['file']['size'] < $max_size) {
            $file_name = $rand . '_' . $file;
            move_uploaded_file($file_tmp, $upload_dir . $file_name);

            mysqli_query($conn, "INSERT INTO threads (media, captions, id_account, jenis) VALUES ('$file_name', '$caption', '$id', '$tipe')") or die(mysqli_error($conn));

            $post_id = $_GET['id'];
            header("Location: index.php?id=$post_id&alert=berhasil");
            exit;
        } else {
            header("location:index.php?alert=gagal_ukuran");
            exit;
        }
    } else {
        header("location:index.php?alert=gagal_ekstensi");
        exit;
    }
} else {
    if ($caption == '') {
        header("location:index.php?alert=gagal_kosong");
        exit;
    } else {
        mysqli_query($conn, "INSERT INTO threads (media, captions, id_account, jenis) VALUES ( '', '$caption', '$id', 'text')") or die(mysqli_error($conn));

        $post_id = $_GET['id'];
        header("Location: index.php?id=$post_id&alert=berhasil");
        exit;
    }
}
