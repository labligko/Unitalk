<?php
include 'config/db.php';
session_start();
error_reporting(0);

if (isset($_SESSION['username'])) {
    header("Location: /index.php");
    exit;
}

if (isset($_POST['submit'])) {
    $login_input = $_POST['login_input'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM account WHERE username='$login_input' OR email='$login_input'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['id_account'] = $row['id_account'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            header("Location: ./index.php?username=" . $_SESSION['username']);
            exit;
        } else {
            echo "<script>alert('password salah.')</script>";
        }
    } else {
        echo "<script>alert('buat akun terlebih dahulu.')</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/Unitalk_logo2.png">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .font-lilita {
            font-family: 'Lilita One', cursive;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen font-lilita">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-blue-600 text-3xl font-bold text-center mb-6">Login</h2>
        <form action="" method="POST" class="space-y-4">
            <div class="relative">
                <i class="fa fa-user absolute left-3 top-3 text-gray-700"></i>
                <input type="text" name="login_input" placeholder="Username atau Email"
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>
            <div class="mb-4 relative">
                <i class="fa fa-lock absolute left-3 top-3 text-gray-700"></i>
                <input type="password" id="password" name="password" placeholder="Password"
                    class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                <i class="fa fa-eye absolute right-3 top-3 text-gray-700 cursor-pointer" id="togglePassword"></i>
            </div>
            <div class="text-center mb-4">
                <a href="forgot_password.php" class="text-gray-700 opacity-70 hover:opacity-100 text-sm">Forgot Password?</a>
            </div>
            <div class="flex flex-col items-center">
                <button name="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    Log In
                </button>
                <p class="text-gray-700 mt-4 text-sm"> Don't have an account?
                    <a href="register.php" class="text-blue-700 opacity-80 hover:underline">Register Here</a>.
                </p>
            </div>
        </form>
    </div>
</body>

<script>
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");

    togglePassword.addEventListener("click", function () {
        // Toggle jenis input
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);

        // Toggle ikon mata
        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
    });
</script>

</html>