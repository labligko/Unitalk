<?php
include 'config/db.php';
session_start();

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if ($password == $cpassword) {
        $sql = "SELECT * FROM `account` WHERE username='$username' OR email='$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 0) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $default_file = 'assets/media/profile/default/default_profile.jpg';
            $new_file_name = 'assets/media/profile/' . uniqid('profile_', true) . '.jpg';

            if (copy($default_file, $new_file_name)) {
                $foto_profil_path = $new_file_name;
                $sql = "INSERT INTO `account` (username, email, password, foto_profil, bio) VALUES ('$username', '$email', '$hashed_password', '$foto_profil_path', '')";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    echo '<script>
                        Swal.fire({
                        title: "Berhasil",
                        text: "Registrasi Berhasil, anda akan di arahkan ke halaman login dalam beberapa detik",
                        icon: "success"
                        });
                      </script>';
                    echo "<script>
                        setTimeout(function() {
                        window.location.href = 'login.php';
                        }, 1000);
                      </script>";
                } else {
                    echo "<div class='text-red-600 text-center mt-4'>Woops! Terjadi Kesalahan.</div>";
                }
            } else {
                echo "<div class='text-red-600 text-center mt-4'>Gagal mengunggah foto profil default.</div>";
            }
        } else {
            echo "<div class='text-red-600 text-center mt-4'>Woops! Username atau Email sudah terdaftar.</div>";
        }
    } else {
        echo "<div class='text-yellow-600 text-center mt-4'>Password Tidak Sesuai.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="./assets/Unitalk_logo2.png">
    <title>Register Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        .font-lilita {
            font-family: 'Lilita One', cursive;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100 font-lilita">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-3xl text-center text-blue-600 font-bold mb-6">Register</h2>
        <form method="POST" action="" class="space-y-4">
            <div class="relative">
                <i class="fa fa-user absolute left-3 top-3 text-gray-700"></i>
                <input type="text" name="username" placeholder="Username" required
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="relative">
                <i class="fa fa-envelope absolute left-3 top-3 text-gray-700"></i>
                <input type="email" name="email" placeholder="Email" required
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="relative">
                <i class="fa fa-lock absolute left-3 top-3 text-gray-700"></i>
                <input type="password" id="password" name="password" placeholder="Password" required
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fa fa-eye absolute right-3 top-3 text-gray-700 cursor-pointer" id="togglePassword"></i>
            </div>
            <div class="relative">
                <i class="fa fa-lock absolute left-3 top-3 text-gray-700"></i>
                <input type="password" id="cpassword" name="cpassword" placeholder="Confirm Password" required
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fa fa-eye absolute right-3 top-3 text-gray-700 cursor-pointer" id="toggleCPassword"></i>
            </div>
            <div class="flex flex-col items-center gap-2 mt-4">
                <button type="submit" name="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">Register</button>
                <p class="text-gray-700 text-sm">Sudah punya akun? 
                    <a href="login.php" class="text-blue-700 opacity-80 hover:underline">Login</a></p>
            </div>
        </form>
    </div>
</body>


<script>
    function setupToggleVisibility(inputId, toggleId) {
        const input = document.getElementById(inputId);
        const toggle = document.getElementById(toggleId);

        toggle.addEventListener("click", () => {
            const type = input.getAttribute("type") === "password" ? "text" : "password";
            input.setAttribute("type", type);
            toggle.classList.toggle("fa-eye");
            toggle.classList.toggle("fa-eye-slash");
        });
    }

    setupToggleVisibility("password", "togglePassword");
    setupToggleVisibility("cpassword", "toggleCPassword");
</script>
</html>
