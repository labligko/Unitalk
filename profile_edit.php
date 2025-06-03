<?php
include 'config/db.php';
include 'includes/sidebar.php';

$id = $_SESSION['id_account'];

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect jika belum login
    exit;
}

$username = $conn->real_escape_string($_SESSION['username']);

$sql_profile = $conn->prepare("SELECT foto_profil, bio FROM account WHERE username = ?");
$sql_profile->bind_param("s", $username);
$sql_profile->execute();
$result = $sql_profile->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $image_profile = htmlspecialchars($row['foto_profil']);
} else {
    $image_profile = "assets/media/profile/default/default_profile.jpg";
}

if (isset($_POST['submit'])) {
    $bio_input = trim($_POST['bio']);
    $username_input = trim($_POST['username']);

    // Pastikan username saat ini diambil dari sesi
    $current_username = $conn->real_escape_string($_SESSION['username']);

    // Mulai membangun query update secara dinamis
    $fields_to_update = [];

    // Cek apakah input username tidak kosong
    if (!empty($username_input)) {
        $update_username = $conn->real_escape_string($username_input);
        $fields_to_update[] = "username = '$update_username'";
    } else {
        $update_username = $current_username; // Tetap gunakan username lama jika input kosong
    }

    // Cek apakah input bio tidak kosong
    if (!empty($bio_input)) {
        $bio = $conn->real_escape_string($bio_input);
        $fields_to_update[] = "bio = '$bio'";
    }

    // Jika ada field yang perlu diperbarui
    if (!empty($fields_to_update)) {
        $sql_bio = "UPDATE account SET " . implode(', ', $fields_to_update) . " WHERE username = '$current_username'";

        if ($conn->query($sql_bio)) {
            // Update sesi jika username diubah
            if (!empty($username_input)) {
                $_SESSION['username'] = $update_username;
            }
            echo "<script>alert('Profil berhasil diubah!'); window.location.href='user_profile.php';</script>";
        } else {
            echo "<script>alert('Gagal mengubah profil: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Tidak ada perubahan yang dilakukan!');</script>";
    }
}

$query_path = "SELECT foto_profil FROM account WHERE id_account = '$id'";
$result = mysqli_query($conn, $query_path);

ob_start();

if (isset($_POST['submitpicture'])) {

    $ekstensi_image = array('png', 'jpg', 'jpeg');
    $file = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '';
    $file_tmp = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';
    $ext = $file ? pathinfo($file, PATHINFO_EXTENSION) : '';

    if ($file != '') {
        if (in_array($ext, $ekstensi_image)) {
            $max_size = 2097152;
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/unitalk/assets/media/profile/';
            $rand = uniqid();

            if ($_FILES['file']['size'] < $max_size) {
                $file_name = $rand . '_' . $file;
                $path = 'assets/media/profile/' . $file_name;

                // Coba pindahkan file
                if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                    $username = $_SESSION['username'];
                    unlink($image_profile);

                    // Update database dengan path file baru
                    $query = "UPDATE account SET foto_profil = '$path' WHERE username = '$username'";
                    if (mysqli_query($conn, $query)) {
                        echo "<script>alert('Foto berhasil diperbarui!'); window.location.href='profile_edit.php?username=$username';</script>";
                    } else {
                        echo "<script>alert('Gagal menyimpan foto: " . $conn->error . "'); window.location.href='profile_edit.php?username=$username';</script>";
                    }
                } else {
                    echo "<script>alert('Gagal memindahkan foto: " . $conn->error . "'); </script>";
                }
            } else {
                echo "<script>alert('Ukuran file terlalu besar!'); window.location.href='profile_edit.php?username=$username';</script>";
            }
        } else {
            echo "<script>alert('Format file tidak didukung!'); window.location.href='profile_edit.php?username=$username';</script>";
        }
    } else {
        // Tidak ada file yang diunggah, hanya update path ke default image tanpa menghapus file
        $username = $_SESSION['username'];
        $default_path = 'assets/media/profile/default_profile.jpg';

        // Update database untuk mengatur foto ke default image
        $query = "UPDATE account SET foto_profil = '$default_path' WHERE username = '$username'";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Foto berhasil diperbarui ke default!'); window.location.href='profile_edit.php?username=$username';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui foto: " . $conn->error . "'); window.location.href='profile_edit.php?username=$username';</script>";
        }
    }
}


ob_end_flush();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="overflow-x-hidden bg-gray-100 text-gray-700 font-lilita">
    <div class="container ml-[400px] pt-10">
        <div class="profile-card list-none relative w-[500px] p-4 mb-2.5 bg-white shadow-lg border border-gray-700 text-gray-700 rounded-[15px]">
            <h3 class="text-blue-600 text-xl ml-2 pb-2">Edit Profile</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="profile-header flex items-center mb-3">
                    <img src="<?php echo htmlspecialchars($image_profile); ?>" alt="Gambar" class="w-[200px] h-[200px] object-cover border-2 border-gray-700 rounded-full">
                    <div class="profile-name ml-5">
                        <p class="font-bold text-[1.2rem] m-0"><?php echo htmlspecialchars($username); ?></p>
                        <a href="#" onclick="changePicture()" class="absolute top-57 left-26 pt-2.5 pl-3 w-10 h-10 bg-white text-gray-700 no-underline border-none rounded-full cursor-pointer">
                            <i class="fa-solid fa-pen mr-1.5"></i>
                        </a>
                    </div>
                </div>
                <div class="mb-2.5">
                    <label for="username">Change Username</label>
                    <input type="text" name="username" id="username" placeholder="<?php echo htmlspecialchars($username); ?>" autocomplete="off"
                        class="w-full p-2.5 bg-transparent border-1 border-gray-700 text-gray-700 rounded-[10px]">
                </div>
                <div class="mb-3">
                    <label for="bio">Bio</label>
                    <textarea name="bio" id="bio" rows="5" placeholder="<?php echo htmlspecialchars($row['bio']); ?>"
                        class="w-full p-2.5 overflow-y-hidden bg-transparent border-1 border-gray-700 text-gray-700 rounded-[10px]"></textarea>
                </div>
                <div class="flex justify-between">
                    <button type="submit" name="submit"
                        class="bg-[#00BB00] text-white font-bold rounded-[10px] px-5 py-1.5">Save</button>
                    <button type="button" onclick="window.location.href='user_profile.php?<?php echo $username; ?>'"
                        class="bg-[#BB0000] text-white font-bold rounded-[10px] px-5 py-1.5">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="pictureModal"
        class="hidden fixed top-30 left-115 w-[400px] bg-white shadow-lg border-1 border-gray-700 rounded-[15px] p-5 z-[1000]">
        <h4 class="text-blue-600 text-center mb-2.5 text-lg font-bold">Change Picture</h4>
        <form id="uploadForm" method="POST" enctype="multipart/form-data">
            <div class="mb-4 bg-transparent">
                <input class="form-control rounded-[15px]" type="file" name="file" id="formFile">
            </div>
            <div class="text-right">
                <button type="submit" name="submitpicture"
                    class="bg-[#00BB00] text-white font-bold rounded-[10px] px-5 py-1.5 mr-2.5">Save</button>
                <button type="button" onclick="closeModal()"
                    class="bg-[#BB0000] text-white font-bold rounded-[10px] px-5 py-1.5">Cancel</button>
            </div>
        </form>
    </div>

    <div id="modalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 999;"></div>

</body>


<style>
    .font-lilita {
        font-family: 'Lilita One', cursive;
    }

    @media screen and (max-width: 768px) {
        .container {
            position: fixed;
            top: 80px;
            left: -400px;
            margin-left: 0;
        }

        .profile-card {
            max-width: 370px;
            height: auto;
            background-color: #3F3C3C;
        }

        .sidebar {
            width: 100%;
            height: auto;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #333;
            z-index: 9999;
            padding: 0;
            border-bottom: #ffffff 1px solid;
        }

        .sidebar .text-title {
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 20px;
            font-weight: bold;
        }

        .sidebar ul {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: space-between;
            align-items: center;
            margin: 0;
            padding: 0;
            list-style: none;
            overflow-x: auto;
        }

        .sidebar li {
            flex: 0 1 auto;
            text-align: center;
            padding: 5px;
        }

        .sidebar a {
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            color: white;
            font-size: 20px;
            padding: 0px;
        }

        .sidebar a span.text {
            display: none;
        }

        .sidebar a:hover {
            background-color: #444;
            border-radius: 5px;
        }

        .sidebar a i {
            font-size: 20px;
        }

        .sidebar .bot {
            display: flex;
            flex-direction: row;
            position: fixed;
            right: -10;
        }

        .sidebar #tambah {
            position: fixed;
            top: 15%;
            right: 0;
            padding: 0;
            width: 100%;
        }

        .sidebar #tambah .list-unstyled {
            list-style: none;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .modal-content {
            position: fixed;
            margin-top: 80px;
            left: 50%;
            transform: translate(-50%);
            width: 100%;
        }

        .dropdown-divider {
            display: none;
        }

        .sidebar #notificationButton .dot {
            height: 20px;
            width: 20px;
            background-color: red;
            border-radius: 50%;
            display: inline-block;
            position: absolute;
            top: 5px;
            right: 5px;
            text-align: center;
        }

        .sidebar #notificationButton .dot.hidden {
            display: none;
        }

        .sidebar .modal {
            position: fixed;
            top: 30;
        }
    }
</style>

</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mediaElements = document.querySelectorAll('audio, video');
        mediaElements.forEach(media => {
            media.muted = true;
            media.pause();
        });

        const observer = new MutationObserver((mutations) => {
            mutations.forEach(mutation => {
                if (mutation.addedNodes) {
                    mutation.addedNodes.forEach(node => {
                        if (node.tagName === 'AUDIO' || node.tagName === 'VIDEO') {
                            node.muted = true;
                            node.pause();
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });

    function changePicture() {
        document.getElementById('pictureModal').style.display = 'block';
        document.getElementById('modalOverlay').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('pictureModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    }

    document.getElementById('submitpicture').addEventListener('click', function() {
        const form = document.getElementById('uploadForm');
        const formData = new FormData(form);

        fetch('profile_edit.php', {
                method: 'POST',
                body: formData,
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === 'success') {
                    closeModal();
                } else {
                    alert(data.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengunggah.');
            });
    });
</script>