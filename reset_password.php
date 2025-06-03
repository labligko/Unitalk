<?php
$message = '';

if (isset($_GET['token']) && isset($_POST['submit'])) {
    include "config/db.php";

    $token = $_GET['token'];
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "SELECT * FROM password_resets WHERE token = '$token'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $expiresAt = $row['expires_at'];

        if (strtotime($expiresAt) > time()) {
            $updatePassword = "UPDATE account SET password = '$newPassword' WHERE email = '$email'";
            $conn->query($updatePassword);

            $deleteToken = "DELETE FROM password_resets WHERE token = '$token'";
            $conn->query($deleteToken);

            $message = "Password berhasil direset. Silakan login.";
        } else {
            $deleteToken = "DELETE FROM password_resets WHERE token = '$token'";
            $conn->query($deleteToken);
            $message = "Token telah kadaluarsa.";
        }
    } else {
        $deleteToken = "DELETE FROM password_resets WHERE token = '$token'";
        $conn->query($deleteToken);
        $message = "Token tidak valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/Unitalk_logo2.png">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .font-lilita {
            font-family: 'Lilita One', cursive;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center font-lilita">
    <div class="bg-white shadow-lg rounded-xl w-full max-w-md p-8">
        <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Reset Password</h2>

        <?php if (!empty($message)) : ?>
            <div class="mb-4 text-center text-sm text-white px-4 py-2 rounded 
                <?= strpos($message, 'berhasil') !== false ? 'bg-green-500' : 'bg-red-500' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan Password Baru">
            </div>
            <div class="flex justify-center">
                <button type="submit" name="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    Reset
                </button>
            </div>
        </form>
    </div>
</body>

</html>