<?php
include 'config/db.php';
include 'includes/sidebar.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect jika belum login
    exit;
}

$username = $_GET['username'];
$id_account = "SELECT id_account FROM account WHERE username = '$username'";
$id_account = $conn->query($id_account)->fetch_assoc()['id_account'];

$sql = "SELECT account.* 
        FROM friendlist 
        INNER JOIN account 
        ON friendlist.friend_id = account.id_account 
        WHERE friendlist.user_id = '$id_account' AND friendlist.friend_id != '$id_account'";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #3F3C3C;
            color: #ffffff;
            font-family: 'Lilita One', sans-serif;
        }

        .list-container {
            max-width: 500px;
            background-color: #3F3C3C;
            border: 1px solid #ffffff;
            border-radius: 15px;
            padding: 20px;
        }

        .user-item {
            display: flex;
            align-items: center;
            border: 1px solid #B1AAFE;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .user-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid #ffffff;
        }

        .user-item h5,
        .user-item p {
            margin: 0;
        }

        .empty-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .font-lilita {
            font-family: 'Lilita One', cursive;
        }

        @media (max-width: 768px) {
            * {
                padding: 0;
                margin: 0;
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
                width: 100%;
                min-width: 35px;
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

            h3 {
                margin-top: 30%;
                margin-left: 20px;
            }

            .list-group {
                width: 95%;
                height: auto;
                padding: 15px;
            }

            .list-group ul {
                padding: 0;
                width: 100%;
            }

            .container {
                padding-top: 20px;
                margin: 0;
            }

            .luar {
                position: relative;
                max-width: 80%;
                margin: 0;
                margin-left: -10%;
            }

            .list-group {
                max-height: auto;
            }

            .list-group ul li {
                max-width: 90%;
                margin-left: 20px;
            }
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-700 font-lilita overflow-x-hidden">
    <div class="flex min-h-screen">

        <!-- Konten utama -->
        <div class="flex-grow flex justify-center items-center px-4">
            <div class="w-full max-w-[500px]">
                <div class="bg-white border-1 border-white shadow-lg rounded-[15px] py-2 relative overflow-y-auto w-full h-[500px]">
                    <h3 class="text-blue-600 text-xl mb-4 text-center">Mengikuti</h3>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $image_profile = "$row[foto_profil]";
                            $bio = strlen($row['bio']) > 50 ? substr($row['bio'], 0, 30) . "..." : $row['bio'];
                            echo "<ul class='list-none mb-0 px-4'>";
                            echo "<a href='search_profile.php?username={$row['username']}' class='no-underline text-gray-700'>";
                            echo "<li class='flex items-center border-1 border-gray-700 hover:bg-gray-400 rounded-[10px] p-2 mb-2'>";
                            echo "<img src='$image_profile' alt='Gambar' class='w-[50px] h-[50px] object-cover border border-white rounded-full'>";
                            echo "<div class='ml-4'>";
                            echo "<h5 class='text-gray-700 m-0'>@" . htmlspecialchars($row['username']) . "</h5>";
                            echo "<p class='text-gray-700 opacity-80 m-0 text-sm'>" . htmlspecialchars($bio) . "</p>";
                            echo "</div>";
                            echo "</li>";
                            echo "</a>";
                            echo "</ul>";
                        }
                    } else {
                        echo "<p class='absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center'>Tidak ada teman yang ditemukan.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div id="chat" class="animate__animated" style="display: none; position: fixed; bottom: 0px; right: 0px; z-index: 9999;">
        <?php include './chat_side.php'; ?>
        <!-- Tombol Close -->
        <button onclick="closeChat()" id="button-close"
            class="fixed top-5 right-5 bg-red-500 hover:bg-red-600 text-white p-2 rounded-full text-md hidden animate__animated">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Tombol Chat -->
    <button onclick="toggleChat()" id="button-chat"
        class="fixed bottom-5 right-5 bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-full text-2xl animate__animated">
        <i class="fa-regular fa-comments"></i>
    </button>
</body>

<script>
    function toggleChat() {
        const chat = document.getElementById("chat");
        const buttonChat = document.getElementById("button-chat");
        const buttonClose = document.getElementById("button-close");

        // Tampilkan chat dengan animasi
        chat.style.display = "block";
        chat.classList.add("animate__fadeInUp");

        // Sembunyikan tombol chat, tampilkan tombol close
        buttonChat.classList.add("animate__fadeOut");
        setTimeout(() => {
            buttonChat.classList.add("hidden");
            buttonChat.classList.remove("animate__fadeOut");

            buttonClose.classList.remove("hidden");
            buttonClose.classList.add("animate__fadeInRight");
        }, 600);
    }

    function closeChat() {
        const chat = document.getElementById("chat");
        const buttonChat = document.getElementById("button-chat");
        const buttonClose = document.getElementById("button-close");

        // Sembunyikan chat dengan animasi
        chat.classList.remove("animate__fadeInUp");
        chat.classList.add("animate__fadeOutDown");

        setTimeout(() => {
            chat.style.display = "none";
            chat.classList.remove("animate__fadeOutDown");
        }, 600);

        // Sembunyikan tombol close, tampilkan tombol chat
        buttonClose.classList.add("animate__fadeOut");
        setTimeout(() => {
            buttonClose.classList.add("hidden");
            buttonClose.classList.remove("animate__fadeOut");

            buttonChat.classList.remove("hidden");
            buttonChat.classList.add("animate__fadeIn");
        }, 300);
    }

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
</script>

</html>