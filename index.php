<?php include 'config/db.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="icon" href="./assets/Unitalk_logo2.png">
    <title>UniTalk - Home</title>
    <style>
        .font-lilita {
            font-family: 'Lilita One', cursive;
        }
    </style>
</head>

<body class="overflow-x-hidden font-lilita bg-gray-100 text-gray-700">

    <div class="content-wrapper">
        <ul class="list-group h-full m-0 p-0 snap-y snap-mandatory list-none">
            <div class="content ml-[350px] pt-10">
                <?php include 'thread.php'; ?>
            </div>
        </ul>
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

<style>
    @media (max-width: 768px) {
        .image-container img {
            max-width: 50px;
            width: fit-content !important;
            height: 50px;
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

        .container {
            margin-left: 0;
            padding: 10px;
            position: fixed;
            top: 100;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .container li {
            max-width: 390px;
            height: 100%;
        }

        .container .list-group-item img,
        .container .list-group-item video {
            width: 100%;
            min-width: 336.67px;
            height: auto;
            max-height: 500px;
        }

        .comment-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 400px;
            background-color: #3F3C3C;
            border: 1px solid #ffffff;
            border-radius: 15px;
            padding: 20px;
            z-index: 9999;
            overflow-y: auto;
        }

        .comment-modal {
            width: 95%;
        }

    }
</style>

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
</script>

</html>