<?php
file_put_contents('log_debug.txt', print_r($_POST, true), FILE_APPEND);
include 'config/db.php';

$id_account = isset($_POST['id_account']) ? intval($_POST['id_account']) : 0;
$id_thread = isset($_POST['id_thread']) ? intval($_POST['id_thread']) : 0;
$komentar = isset($_POST['komentar']) ? trim($_POST['komentar']) : "";
$parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : null; // Tambahan

if ($id_account === 0 || $id_thread === 0 || $komentar === "") {
    echo "Data tidak valid!";
    exit;
}

if ($parent_id) {
    $sql = "INSERT INTO comment (id_account, id_thread, komentar, parent_id, status) VALUES (?, ?, ?, ?, 'unread')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisi", $id_account, $id_thread, $komentar, $parent_id);
} else {
    $sql = "INSERT INTO comment (id_account, id_thread, komentar, status) VALUES (?, ?, ?, 'unread')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $id_account, $id_thread, $komentar);
}

if ($stmt) {
    if ($stmt->execute()) {
        $cooldown_seconds = 5;

        // Ambil waktu komentar terakhir user di thread ini
        $cek = $conn->prepare("SELECT waktu FROM comment WHERE id_account=? AND id_thread=? ORDER BY waktu DESC LIMIT 1");
        $cek->bind_param("ii", $id_account, $id_thread);
        $cek->execute();
        $cek->bind_result($waktu_terakhir);
        $ada = $cek->fetch();
        $cek->close();

        if ($ada) {
            $sekarang = time();
            $waktu_terakhir_unix = strtotime($waktu_terakhir);

            if (($sekarang - $waktu_terakhir_unix) < $cooldown_seconds) {
                echo "Tunggu dulu sebelum komentar lagi!";
                $conn->close();
                exit;
            }
        }

        echo "Komentar berhasil disimpan!";
    } else {
        echo "Gagal menyimpan komentar: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Gagal mempersiapkan query: " . $conn->error;
}

$conn->close();
?>


<!-- <?php
include 'config/db.php';

$id_account = isset($_POST['id_account']) ? intval($_POST['id_account']) : 0;
$id_thread = isset($_POST['id_thread']) ? intval($_POST['id_thread']) : 0;
$komentar = isset($_POST['komentar']) ? trim($_POST['komentar']) : "";

if ($id_account === 0 || $id_thread === 0 || $komentar === "") {
    echo "Data tidak valid!";
    exit;
}

$sql = "INSERT INTO comment (id_account, id_thread, komentar, status) VALUES (?, ?, ? , 'unread')";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("iis", $id_account, $id_thread, $komentar);
    if ($stmt->execute()) {
        echo "Komentar berhasil disimpan!";
    } else {    
        echo "Gagal menyimpan komentar: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Gagal mempersiapkan query: " . $conn->error;
}

$conn->close();

?> -->
