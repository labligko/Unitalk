<?php
include 'config/db.php';
include 'includes/sidebar.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$id_account = $_SESSION['id_account'];

if (isset($_POST['submitpassword'])) {
    $current_password = $_POST['currentPassword'];
    $new_password = $_POST['newPassword'];
    $confirm_password = $_POST['confirmPassword'];

    $sql = "SELECT * FROM account WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {

                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE account SET password = '$new_password_hash' WHERE username = '$username'";
                if ($conn->query($update_sql)) {
                    echo "<script>alert('Password berhasil diubah!');</script>";
                } else {
                    echo "<script>alert('Gagal mengubah password!');</script>";
                }
            } else {
                echo "<script>alert('Password baru dan konfirmasi tidak cocok!');</script>";
            }
        } else {
            echo "<script>alert('Password saat ini salah!');</script>";
        }
    }
}

if (isset($_POST['submitemail'])) {
    $new_email = $_POST['email'];
    $update_sql = "UPDATE account SET email = '$new_email' WHERE username = '$username'";
    if ($conn->query($update_sql)) {
        echo "<script>alert('Email berhasil diubah!');</script>";
    } else {
        echo "<script>alert('Gagal mengubah email!');</script>";
    }
}

if (isset($_POST['conpassword'])) {
    $confirm_password = $_POST['pass'];

    $sql = "SELECT password FROM account WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        if (password_verify($confirm_password, $stored_password)) {
            $_SESSION['show_email_modal'] = true;
        } else {
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
        echo "<script>alert('Akun tidak ditemukan!');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/Unitalk_logo2.png">
    <title>Settings</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .custom-popup {
            border-radius: 20px;
            background-color: white;
            color: #155dfc;
            font-family: 'Lilita One';
            font-size: 14px;
            width: 300px;
            border: 2px solid #155dfc;
        }

        .custom-cancel-button {
            background-color: #f44336 !important;
            color: white !important;
            border-radius: 5px;
            padding: 5px 10px;
        }

        .custom-cancel-button:hover {
            background-color: #d32f2f !important;
        }

        .custom-confirm-button {
            background-color: #4CAF50 !important;
            color: white !important;
            border-radius: 5px;
            padding: 5px 10px;
        }

        .custom-confirm-button:hover {
            background-color: #45a049 !important;
        }

        .swal2-warning {
            color: #155dfc !important;
            border-color: #155dfc !important;
            font-size: 10px !important;
            padding: 0px;
        }

        .font-lilita {
            font-family: 'Lilita One', cursive;
        }
    </style>
</head>

<body class="overflow-x-hidden bg-gray-100 font-lilita">
    <div class="luar absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
        <div class="list-group w-[400px] h-[400px] bg-white shadow-lg rounded-4xl p-4 relative border-1">
            <h3 class="text-blue-600 text-xl mb-4">Settings</h3>
            <ul class="list-none p-0 m-0">
                <li class="mb-2 cursor-pointer" onclick="email()">
                    <a href="javascript:void(0);" class="flex items-center border-[2px] border-gray-700 rounded-[10px] p-2" style="text-decoration: none; color: #374151;">
                        <i class="fa-solid fa-envelope mr-2 text-gray-700"></i> Email
                    </a>
                </li>
                <li class="mb-2 cursor-pointer" onclick="password()">
                    <a href="javascript:void(0);" class="flex items-center border-[2px] border-gray-700 rounded-[10px] p-2" style="text-decoration: none; color: #374151;">
                        <i class="fa-solid fa-key mr-2 text-gray-700"></i> Password
                    </a>
                </li>
                <li class="mb-2">
                    <a href="liked.php?username=<?php echo $username; ?>" class="flex items-center border-[2px] border-gray-700 rounded-[10px] p-2" style="text-decoration: none; color: #374151;">
                        <i class="fa-solid fa-heart mr-2 text-gray-700"></i> Suka
                    </a>
                </li>
                <li>
                    <a href="following.php?username=<?php echo $username; ?>" class="flex items-center border-[2px] border-gray-700 rounded-[10px] p-2" style="text-decoration: none; color: #374151;">
                        <i class="fa-solid fa-user-friends mr-2 text-gray-700"></i> Mengikuti
                    </a>
                </li>
            </ul>
            <button type="submit" onclick="logout()" class="w-full bg-red-600 text-white py-2 px-4 relative top-[50px] hover:bg-red-700 transition" style="border-radius: 8px;">
                Log Out
            </button>
        </div>
    </div>

    <div id="emailModal" class="hidden fixed top-1/2 left-1/2 w-[400px] -translate-x-1/2 -translate-y-1/2 bg-white shadow-lg rounded-[15px] p-5 z-[1000]">
        <h4 class="text-center text-blue-600 text-lg font-bold">Change Email</h4>
        <form id="emailForm" method="POST" class="mt-4">
            <div class="mb-4">
                <label for="newemail" class="block text-gray-700 mb-1">New Email</label>
                <input type="email" id="newemail" name="email"
                    class="w-full p-2 border-1 border-gray-700 rounded bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" placeholder="Email">
            </div>
            <div class="flex justify-center gap-2">
                <button type="submit" name="submitemail"
                    class="bg-green-500 text-white font-bold rounded px-5 py-1.5 hover:bg-green-700 transition">Save</button>
                <button type="button" onclick="closeModalEmail()"
                    class="bg-red-500 text-white font-bold rounded px-5 py-1.5 hover:bg-red-700 transition">Cancel</button>
            </div>
        </form>
    </div>

    <div id="confirmModal" class="hidden fixed top-1/2 left-1/2 w-[400px] -translate-x-1/2 -translate-y-1/2 bg-white shadow-lg rounded-[15px] p-5 z-[1000]">
        <h4 class="text-center text-blue-600 text-lg font-bold">Confirm Password</h4>
        <form id="confirmForm" method="POST" class="mt-4">
            <div class="mb-4">
                <label for="conpass" class="block text-gray-700 mb-1">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="conpass" name="pass"
                        class="w-full p-2 pr-10 border border-gray-700 rounded bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
                        placeholder="Password">
                    <i class="fa fa-eye absolute right-3 top-3 text-gray-700 cursor-pointer" id="toggleConPass"></i>
                </div>
            </div>
            <div class="flex justify-center gap-2">
                <button type="submit" name="conpassword"
                    class="bg-green-500 text-white font-bold rounded px-5 py-1.5 hover:bg-green-700 transition">Confirm</button>
                <button type="button" onclick="closeModalconfirm()"
                    class="bg-red-500 text-white font-bold rounded px-5 py-1.5 hover:bg-red-700 transition">Cancel</button>
            </div>
        </form>
    </div>

    <div id="passwordModal" class="hidden fixed top-1/2 left-1/2 w-[400px] -translate-x-1/2 -translate-y-1/2 bg-white shadow-lg rounded-[15px] p-4 z-[1000]">
        <h4 class="text-center text-blue-600 text-lg font-bold">Change Password</h4>
        <form id="passwordForm" method="POST" class="mt-3">
            <!-- Current Password -->
            <div class="mb-2">
                <label for="currentPassword" class="block text-gray-700 mb-1">Current Password</label>
                <div class="relative">
                    <input type="password" id="currentPassword" name="currentPassword"
                        class="w-full p-2 pr-10 border border-gray-700 rounded bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
                        placeholder="Current Password">
                    <i class="fa fa-eye absolute right-3 top-3 text-gray-700 cursor-pointer" id="toggleCurrent"></i>
                </div>
            </div>

            <!-- New Password -->
            <div class="mb-2">
                <label for="newPassword" class="block text-gray-700 mb-1">New Password</label>
                <div class="relative">
                    <input type="password" id="newPassword" name="newPassword"
                        class="w-full p-2 pr-10 border border-gray-700 rounded bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
                        placeholder="New Password">
                    <i class="fa fa-eye absolute right-3 top-3 text-gray-700 cursor-pointer" id="toggleNew"></i>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="confirmPassword" class="block text-gray-700 mb-1">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="confirmPassword" name="confirmPassword"
                        class="w-full p-2 pr-10 border border-gray-700 rounded bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
                        placeholder="Confirm Password">
                    <i class="fa fa-eye absolute right-3 top-3 text-gray-700 cursor-pointer" id="toggleConfirm"></i>
                </div>
            </div>
            <div class="flex justify-center gap-2">
                <button type="submit" name="submitpassword"
                    class="bg-green-500 text-white font-bold rounded px-5 py-1.5 hover:bg-green-700 transition">Save</button>
                <button type="button" onclick="closeModal()"
                    class="bg-red-500 text-white font-bold rounded px-5 py-1.5 hover:bg-red-700 transition">Cancel</button>
            </div>
        </form>
    </div>

    <div id="chat" class="animate__animated" style="display: none; position: fixed; bottom: 0px; right: 0px; z-index: 9999;">
        <?php include './chat_side.php'; ?>
        <!-- Tombol Close -->
        <button onclick="closeChat()" id="button-close"
            class="fixed top-5 right-5 bg-red-500 hover:bg-red-600 text-white p-2 rounded-full text-md hidden animate__animated" style=" border-radius: calc(infinity * 1px);">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Tombol Chat -->
    <button onclick="toggleChat()" id="button-chat"
        class="fixed bottom-5 right-5 bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-full text-2xl animate__animated" style=" border-radius: calc(infinity * 1px);">
        <i class="fa-regular fa-comments"></i>
    </button>

    <div id="modalOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[999]"></div>

</body>

<style>
    #emailModal,
    #confirmModal,
    #passwordModal {
        width: 90%;
        max-width: 400px;
        margin: 20px;
    }

    #modalOverlay {
        width: 100%;
        height: 100%;
    }

    @media (max-width: 768px) {

        #emailModal,
        #confirmModal,
        #passwordModal {
            position: fixed;
            margin: 0;
            max-width: 90%;
        }

        h3 {
            margin-top: 30%;
            margin-left: 20px;
        }

        .list-group {
            width: 90%;
            height: auto;
            padding: 15px;
        }

        .list-group ul {
            padding: 0;
            width: 100%;
        }

        .container {
            padding-top: 20px;
        }

        .luar {
            position: relative;
            width: 100%;
        }

        .list-group button {
            width: 30%;
            padding: 10px;
            position: fixed;
            bottom: 0%;
            left: 50%;
            transform: translate(-50%, -50%);
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

    }
</style>

<script>
    function setupToggle(idInput, idIcon) {
        const input = document.getElementById(idInput);
        const icon = document.getElementById(idIcon);

        icon.addEventListener("click", () => {
            const type = input.getAttribute("type") === "password" ? "text" : "password";
            input.setAttribute("type", type);
            icon.classList.toggle("fa-eye");
            icon.classList.toggle("fa-eye-slash");
        });
    }

    setupToggle("currentPassword", "toggleCurrent");
    setupToggle("newPassword", "toggleNew");
    setupToggle("confirmPassword", "toggleConfirm");

    const conpass = document.getElementById("conpass");
    const toggleConPass = document.getElementById("toggleConPass");

    toggleConPass.addEventListener("click", () => {
        const type = conpass.getAttribute("type") === "password" ? "text" : "password";
        conpass.setAttribute("type", type);
        toggleConPass.classList.toggle("fa-eye");
        toggleConPass.classList.toggle("fa-eye-slash");
    });

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

    function logout() {
        Swal.fire({
            text: "Apakah kamu yakin?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, lanjutkan!",
            cancelButtonText: "Batal",
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#F44336",
            customClass: {
                popup: 'custom-popup',
                cancelButton: 'custom-cancel-button',
                confirmButton: 'custom-confirm-button'
            },
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "logout.php";
            }
        });
    }

    function follow(userId, friendId) {
        fetch('follow.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    friend_id: friendId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Permintaan pertemanan berhasil dikirim!");
                    document.getElementById('add-button').style.display = 'none';
                    document.getElementById('unfriend-button').style.display = 'block';
                } else {
                    console.error("Error:", data.message);
                    alert("Gagal mengirim permintaan: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Terjadi kesalahan. Coba lagi nanti.");
            });
    }

    function unFollow(userId, friendId) {
        Swal.fire({
            text: "Apakah kamu yakin?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, lanjutkan!",
            cancelButtonText: "Batal",
            confirmButtonColor: "#4CAF50",
            cancelButtonColor: "#F44336",
            customClass: {
                popup: 'custom-popup',
                cancelButton: 'custom-cancel-button',
                confirmButton: 'custom-confirm-button'
            },
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('unfollow.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            friend_id: friendId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('add-button').style.display = 'block';
                            document.getElementById('unfriend-button').style.display = 'none';
                        } else {
                            console.error("Error:", data.message);
                            alert("Gagal membatalkan permintaan: " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Terjadi kesalahan. Coba lagi nanti.");
                    });
            }
        });
    }

    function email() {
        document.getElementById('confirmModal').style.display = 'block';
        document.getElementById('modalOverlay').style.display = 'block';
    }

    function password() {
        document.getElementById('passwordModal').style.display = 'block';
        document.getElementById('modalOverlay').style.display = 'block';
    }

    function closeModalEmail() {
        document.getElementById('emailModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    }

    function closeModalconfirm() {
        document.getElementById('confirmModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    }

    function closeModal() {
        document.getElementById('passwordModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    }

    document.addEventListener("DOMContentLoaded", function() {
        <?php if (isset($_SESSION['show_email_modal']) && $_SESSION['show_email_modal']): ?>
            document.getElementById('emailModal').style.display = 'block';
            document.getElementById('modalOverlay').style.display = 'block';
            <?php unset($_SESSION['show_email_modal']);
            ?>
        <?php endif; ?>
    });

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