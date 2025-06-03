<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';

if (isset($_POST['submit'])) {
    include "config/db.php";

    $email = $_POST['email'];
    $query = "SELECT * FROM account WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(50));
        date_default_timezone_set('Asia/Jakarta');
        $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        $insertToken = "INSERT INTO password_resets (email, token, expires_at) VALUES ('$email', '$token', '$expires')";
        $conn->query($insertToken);

        $resetLink = "http://localhost/unitalk/reset_password.php?token=$token";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'alfthoriq0@gmail.com';
            $mail->Password = 'tlblssuqppywrgqr';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('althoriq0@gmail.com', 'UniTalk');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Password';
            $mail->Body    = "Klik link ini untuk reset password: <a href='$resetLink'>$resetLink</a>
            <br><br><p>Link ini akan kadaluarsa dalam 5 menit</p>";

            $mail->send();

            echo "<script>
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Link reset password telah dikirim ke email Anda.'
                    });
                }, 100);
            </script>";
        } catch (Exception $e) {
            echo "<script>
                setTimeout(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Pesan gagal dikirim. Error: " . $mail->ErrorInfo . "'
                    });
                }, 100);
            </script>";
        }
    } else {
        echo "<script>
            setTimeout(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Email tidak ditemukan.'
                });
            }, 100);
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/Unitalk_logo2.png">
    <title>Lupa Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .font-lilita {
            font-family: 'Lilita One', cursive;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center font-lilita">
    <div class="bg-white shadow-lg rounded-xl w-full max-w-md p-8">
        <button onclick="window.history.back()" class="mb-4 text-sm bg-gray-600 text-white px-2 py-1 rounded hover:bg-gray-800">
            &larr; Kembali
        </button>
        <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Lupa Password</h2>
        <form action="" method="POST">
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2" for="email">Email Anda</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="contoh@email.com">
            </div>
            <div class="flex justify-center">
                <button type="submit" name="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    Kirim Link Reset
                </button>
            </div>
        </form>
    </div>
</body>

</html>