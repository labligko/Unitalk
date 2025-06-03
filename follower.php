<?php
include 'config/db.php';
include 'includes/sidebar.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$id_account = $_SESSION['id_account'];

$sql = "SELECT account.* 
        FROM friendlist 
        INNER JOIN account 
        ON friendlist.user_id = account.id_account 
        WHERE friendlist.friend_id = '$id_account' AND friendlist.user_id != '$id_account'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Followers</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #3F3C3C;
            color: #ffffff;
            font-family: 'Lilita One', sans-serif;
        }

        .font-lilita {
            font-family: 'Lilita One', cursive;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-700 font-lilita overflow-x-hidden">
    <div class="flex min-h-screen">
        <div class="flex-grow flex justify-center items-center px-4">
            <div class="w-full max-w-[500px]">
                <div class="bg-white border-1 border-white shadow-lg rounded-[15px] py-2 relative overflow-y-auto w-full h-[500px]">
                    <h3 class="text-blue-600 text-xl mb-4 text-center">Pengikut</h3>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $image_profile = $row['foto_profil'];
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
                        echo "<p class='absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center'>Belum ada yang mengikuti kamu.</p>";
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