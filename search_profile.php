<?php
include 'config/db.php';
include 'includes/sidebar.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect jika belum login
    exit;
}

$id_account = $_SESSION['id_account'];

$username = $conn->real_escape_string("$_GET[username]");
$id_search = $conn->prepare("SELECT id_account FROM account WHERE username = ?");
$id_search->bind_param("s", $username);
$id_search->execute();
$result_id = $id_search->get_result();
if ($result_id->num_rows > 0) {
    $row = $result_id->fetch_assoc();
    $id_friend = $row['id_account'];
}

$sql_profile = $conn->prepare("SELECT foto_profil FROM account WHERE username = ?");
$sql_profile->bind_param("s", $username);
$sql_profile->execute();
$result = $sql_profile->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $image_profile = htmlspecialchars($row['foto_profil']);
} else {
    $image_profile = "assets/media/profile/default_profile.jpg";
}

$sql_threads = "SELECT threads.*, account.username, account.foto_profil FROM threads JOIN account ON threads.id_account = account.id_account WHERE account.username = ? ORDER BY threads.tanggal DESC";
$stmt_threads = $conn->prepare($sql_threads);
$stmt_threads->bind_param('s', $username);
$stmt_threads->execute();
$result_threads = $stmt_threads->get_result();
$posts = $result_threads->fetch_all(MYSQLI_ASSOC);

$sql_bio = $conn->prepare("SELECT bio FROM account WHERE username = ?");
$sql_bio->bind_param("s", $username);
$sql_bio->execute();
$result = $sql_bio->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $bio = $row['bio'];
} else {
    $bio = "bio belum di atur";
}

$cekstatus = $conn->prepare("SELECT status FROM friendlist WHERE user_id = ? AND friend_id = ?");
$cekstatus->bind_param("ii", $_SESSION['id_account'], $id_friend);
$cekstatus->execute();
$result_status = $cekstatus->get_result();

if ($result_status->num_rows > 0) {
    $row = $result_status->fetch_assoc();
    $status = $row['status'];
} else {
    $status = "none";
}

// Query: Total Following (berapa orang yang dia follow)
$sql_total_following = "SELECT COUNT(*) AS total_following
FROM friendlist
WHERE user_id = $id_friend AND status = 'following'";

$result_total_following = $conn->query($sql_total_following);
if ($result_total_following && $row = $result_total_following->fetch_assoc()) {
    $total_following = $row['total_following'];
}

// Query: Total Follower (berapa orang yang follow dia)
$sql_total_follower = "SELECT COUNT(*) AS total_follower
FROM friendlist
WHERE friend_id = $id_friend AND status = 'following'";

$result_total_follower = $conn->query($sql_total_follower);
if ($result_total_follower && $row = $result_total_follower->fetch_assoc()) {
    $total_follower = $row['total_follower'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .font-lilita {
            font-family: 'Lilita One', cursive;
        }

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

        .comment-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            max-width: 100%;
            max-height: 100%;
            background-color: white;
            border: 1px solid #364153;
            border-radius: 15px;
            margin-left: 30px;
            padding: 20px;
            z-index: 1000;
            overflow-y: auto;
        }

        .comment-modal .close-btn {
            background-color: white;
            color: red;
            font-size: 1.5rem;
            position: absolute;
            padding: 0;
            top: 0;
            right: 0;
            font-weight: bold;
            border-radius: 10px;
            border: none;
            cursor: pointer;
        }

        .comment-modal .close-btn:hover {
            color: #364153;
            font-weight: bold;
        }

        .comment-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #364153;
            border-radius: 5px;
            background-color: white;
            color: #364153;
        }

        .btn-comment-click {
            padding: 10px 15px;
            border: none;
            background-color: white;
            color: #364153;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-comment-delete {
            padding: 10px 15px;
            border: none;
            background-color: white;
            color: #364153;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-top: -13%;
        }

        .btn-comment-delete:hover {
            color: red;
            border-radius: 25px;
        }

        li video {
            max-height: 500px;
        }

        @media (max-width: 768px) {
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

            .container {
                margin-left: 0;
                padding: 10px;
                position: fixed;
                top: 80px;
                left: -390px;
                width: 80%;
                height: 100%;
            }

            li img,
            li video {
                max-width: 100%;
                height: auto;
                max-height: 500px;
            }

            .comment-modal {
                margin: 0;
                width: 95%;
            }
        }
    </style>
</head>

<body class="overflow-x-hidden bg-gray-100 text-gray-700 font-lilita">
    <div style="margin-left: 385px; padding-top: 40px; position: relative;">
        <div class='profile-card shadow-xl' style='width: 500px; padding: 20px; margin-bottom: 10px; background: white;  border: 1px solid #364153; color: #364153; border-radius: 15px; position: relative;'>
            <div class="profile-header" style="display: flex; align-items: center; gap: 20px;">
                <!-- Profile Picture -->
                <img src="<?php echo htmlspecialchars($image_profile); ?>" alt="Gambar"
                    style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #364153; border-radius: 50%;">

                <!-- Profile Info -->
                <div class="profile-info" style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Username -->
                    <p style="font-weight: bold; font-size: 1.2rem; margin: 0;"><?php echo htmlspecialchars($username); ?></p>
                    <!-- Followers & Following -->
                    <div style="font-size: 1rem; color: #364153;">
                        <?php echo $total_follower; ?>
                        <a href="follower_search.php?username=<?php echo htmlspecialchars($username); ?>" style="text-decoration: none; color: #364153;"> Follower</a> &nbsp; • &nbsp;
                        <?php echo $total_following; ?>
                        <a href="following_search.php?username=<?php echo htmlspecialchars($username); ?>" style="text-decoration: none; color: #364153;"> Following</a>
                    </div>
                </div>

                <!-- Follow/Unfollow Buttons -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button class="add-button"
                        style="padding: 5px 15px; background-color: #4ade80; color: white; border: none; border-radius: 15px; cursor: pointer; <?= ($status === 'following') ? 'display: none;' : ''; ?>"
                        onclick='follow("<?= $id_account; ?>","<?= $id_friend; ?>", this)'>
                        Follow
                    </button>
                    <button class="unfriend-button"
                        style="padding: 5px 15px; background-color: transparent; color: #C10C1C; border: 1px solid #C10C1C; border-radius: 15px; cursor: pointer; <?= ($status === 'following') ? '' : 'display: none;'; ?>"
                        onclick='unFollow("<?= $id_account; ?>","<?= $id_friend; ?>", this)'>
                        Unfollow
                    </button>
                </div>

            </div>

            <div style='flex-grow: 1;'>
                <p id='bio-text' style='margin: 5px 0 10px;'>
                    <?php
                    $bioPreview = substr($bio, 0, 50);
                    echo htmlspecialchars($bioPreview);
                    ?>
                    <span id='more-text' style='display: none;'><?php echo htmlspecialchars(substr($bio, 50)); ?></span>
                    <br>
                    <?php if (strlen($bio) > 50): ?>
                        <button id='toggle-more' class="text-blue-300" style='background: none; border: none; cursor: pointer; padding: 0;'>Selengkapnya</button>
                    <?php endif; ?>
                </p>
            </div>
            <button onclick="share()" style="color: #364153; background:none; border:none; position: absolute; top: 10px; right: 10px; cursor: pointer;"><i class="fa-solid fa-share-nodes"></i></button>
        </div>

        <p class="text-md mb-2 ml-2 mt-4 font-bold">Postingan @<?php echo $username; ?></p>
        <?php
        if (count($posts) > 0) {
            foreach ($posts as $post) {
                $id = $post['id'];
                echo "<li class='list-group-item' id='post-{$post['id']}' style='width: 500px; padding-bottom: 5px; margin-bottom: 10px; background-color: white; border: 1px solid #364153; color: #364153; border-radius: 15px;'>";
                $sql_like = "SELECT COUNT(*) AS likes FROM likes WHERE id_thread = ?";
                $stmt_like = $conn->prepare($sql_like);
                $stmt_like->bind_param("i", $post['id']);
                $stmt_like->execute();
                $result_like = $stmt_like->get_result();
                $likes = $result_like->fetch_assoc()['likes'];

                $sql_check_like = "SELECT * FROM likes WHERE id_thread = {$post['id']} AND id_account = {$id_friend}";
                $result_check_like = mysqli_query($conn, $sql_check_like);
                $liked = mysqli_num_rows($result_check_like) > 0;

                $heart_icon = $liked ? 'fa-solid' : 'fa-regular';

                $sql_comment_count = "SELECT COUNT(*) AS comments FROM comment WHERE id_thread = ?";
                $stmt_comment_count = $conn->prepare($sql_comment_count);
                $stmt_comment_count->bind_param("i", $post['id']);
                $stmt_comment_count->execute();
                $result_comment_count = $stmt_comment_count->get_result();
                $comments_count = $result_comment_count->fetch_assoc()['comments'];

                if ($post['jenis'] == 'gambar') {
                    echo "<figure class='figure' style='margin-bottom: 5px;'>";
                    $image_path = 'assets/media/' . htmlspecialchars($post['media']);
                    echo "<img src='$image_path' class='figure-img img-fluid' alt='Gambar' style='width: 500px; height: auto; max-height: 500px; object-fit: cover; margin-bottom: 10px;border: 1px solid #364153; border-radius: 10px;'>";
                    echo "<figcaption class='figure-caption'>";
                    echo "<li style='list-style:none;'><hr class='dropdown-divider' style='color: #364153;'></li>";
                } elseif ($post['jenis'] == 'video') {
                    echo "<figure class='figure' style='margin-bottom: 5px;'>";
                    $video_path = 'assets/media/' . htmlspecialchars($post['media']);
                    echo "<video id='videoPlayer-{$post['id']}' width='470' height='auto' max-height='500px' autoplay muted loop playsinline style='object-fit: cover; margin-bottom: 10px; border: .5px solid #364153; border-radius: 10px;'>";
                    echo "<source src='$video_path' type='video/mp4'>";
                    echo "Browser Anda tidak mendukung pemutaran video.";
                    echo "</video>";
                    echo "<button id='muteButton-{$post['id']}' onclick='toggleMute()' style='position: absolute; bottom: 10px; right: 10px; background-color: transparent; border: none; cursor: pointer; color: #364153;'><i class='fa-solid fa-volume-xmark'></i></button>";
                    echo "<button id='unmuteButton-{$post['id']}' onclick='toggleMute()' style='position: absolute; bottom: 10px; right: 10px; background-color: transparent; border: none; cursor: pointer; color: #364153;display: none;'><i class='fa-solid fa-volume-high'></i></button>";
                    echo "<figcaption class='figure-caption'>";
                    echo "<li style='list-style:none;'><hr class='dropdown-divider' style='color: #364153; padding-bottom: 0px;'></li>";
                } else {
                    echo "<div>";
                }

                echo "<div style='margin-bottom: 3px; margin-top: 10px; margin-left: 10px; color: #364153 '>";
                echo "<a style='margin-right: 8px; font-size: 1.3rem; cursor: pointer;'onclick='likePost({$post['id']}, {$id})'>
                    <span id='like-count-{$post['id']}'>$likes</span>
                    <i id='like-icon-{$post['id']}' class='{$heart_icon} fa-heart text-red-600'></i>
                </a>";
                echo "<a style='margin-right: 8px; font-size: 1.3rem; cursor: pointer;' onclick='openCommentModal({$post['id']})'>
                        <span id='comment-count-{$post['id']}'>$comments_count</span>
                        <i class='fa-regular fa-comment'></i>
                    </a>";

                echo "<div id='commentModal-{$post['id']}' class='comment-modal'>
                    <div style='margin-bottom: 15px; position: relative;'>
                    <label for='newemail' style='color: #364153; font-size: 1.2rem;'>your comment</label>
                    <button type='button' onclick='closeModal({$post['id']})' class='close-btn'>
                        <i class='fa-solid fa-circle-xmark'></i>
                    </button>              
                    <div style='display: flex; flex-direction: row; align-items: center; margin-bottom: 15px; margin-top: 10px;'>
                        <input class='comment-input shadow-xl' name='input_comment' id='commentInput-{$post['id']}' style='padding: 10px; border: 1px solid #364153; border-radius: 5px; background-color: white; color: #364153;' type='text' placeholder='Comment here...' autocomplete='off' aria-label='comment'>
                        <button class='btn-comment-click hover:bg-blue-400 hover:text-white' style='padding: 15px 5px; border:none;' name='search' onclick='saveComment({$post['id']}, {$id_account})'>
                        <i class='fa-solid fa-pen'></i>
                        </button>
                    </div>";

                $sql_comment = "SELECT comment.*, account.username, account.foto_profil FROM comment JOIN account ON comment.id_account = account.id_account WHERE id_thread = '$post[id]' ORDER BY comment.waktu DESC";
                $result_comment = $conn->query($sql_comment);

                $all_comments = [];
                if ($result_comment->num_rows > 0) {
                    while ($row = $result_comment->fetch_assoc()) {
                        $all_comments[] = $row;
                    }

                    // Tampilkan comment induk
                    foreach ($all_comments as $comment) {
                        if ($comment['parent_id'] == null) {
                            $id_comment = $comment['id_comment'];
                            $username = htmlspecialchars($comment['username']);
                            $foto_profil = htmlspecialchars($comment['foto_profil']);
                            $comment_content = htmlspecialchars($comment['komentar']);
                            $tanggal = date('d F Y', strtotime($comment['waktu']));
                            $id_user_comment = $comment['id_account'];

                            echo "
                                    <div style='display: flex; flex-direction: row; align-items: flex-start; margin-bottom: 15px; margin-top: 10px;'>
                                        <div class='image-container' style='margin-right: 10px;'>
                                            <img src='$foto_profil' alt='Gambar' style='width: 50px; height: 50px; object-fit: cover; border: 1px solid #364153; border-radius: 50px;'>
                                        </div>
                                        <div class='desc' style='display: flex; flex-direction: column; align-items: flex-start; gap: 5px;'>
                                            <h6 style='color: #364153; margin: 0; opacity: 0.8;'><i>@$username</i></h6>
                                            <p style='color: #364153; font-size: 1rem; margin: 0;'>$comment_content</p>
                                            <div style='display: flex; align-items: start; gap: 5px;'>
                                                <p style='font-size: .8rem; color: #364153; opacity: 0.5;'>$tanggal</p>
                                                <button onclick='toggleReplyForm($id_comment)' class='hover:text-gray-500'>Reply</button>
                                            </div>
                                            
                                            <div id='reply-form-$id_comment' style='display:none; margin-top:10px; width: 100%;'>
                                                <input type='text' id='reply-input-$id_comment' style='width: 85%; padding: 10px; border: 1px solid #364153; border-radius: 5px;' value='@$username ' />
                                                <button onclick='saveComment({$post['id']}, {$id_account}, $id_comment)' class='btn-comment-click hover:bg-blue-400 hover:text-white' style='padding: 15px 5px; border:none;'>
                                                    <i class='fa-solid fa-pen'></i>
                                                </button>
                                            </div>";

                            // Tombol hapus jika user yang komen
                            if ($id_user_comment == $id_account) {
                                echo "<button onclick='deleteComment({$post['id']}, $id_comment, {$id_account})' class='hover:text-red-600 absolute right-0' style='padding: 5px; border: none;'><i class='fa-solid fa-trash'></i></button>";
                            }

                            echo "</div></div>";

                            // Sekarang tampilkan reply-nya (nested langsung)
                            foreach ($all_comments as $reply) {
                                if ($reply['parent_id'] == $id_comment) {
                                    $reply_id = $reply['id_comment'];
                                    $reply_user = htmlspecialchars($reply['username']);
                                    $reply_foto = htmlspecialchars($reply['foto_profil']);
                                    $reply_content = htmlspecialchars($reply['komentar']);
                                    $reply_tanggal = date('d F Y', strtotime($reply['waktu']));
                                    $reply_user_id = $reply['id_account'];

                                    echo "
                                            <div style='margin-left: 60px; margin-top: 10px; display: flex;'>
                                                <div style='margin-right: 10px;'>
                                                    <img src='$reply_foto' alt='Gambar' style='width: 40px; height: 40px; object-fit: cover; border-radius: 50%; border: 1px solid #364153;'>
                                                </div>
                                                <div style='max-width: 200px;'>
                                                    <h6 style='color: #364153; margin: 0; font-size: 0.9rem;'><i>@$reply_user</i></h6>
                                                    <p style='color: #364153; font-size: 1rem; margin: 0;'>$reply_content</p>
                                                    <div style='display: flex; flex-direction: row; align-items: center; gap: 5px;'>
                                                    <div style='display: flex; align-items: start; gap: 5px;'>
                                                    <p style='font-size: .75rem; color: #364153; opacity: 0.5;'>$reply_tanggal</p>
                                                    </div>";
                                    if ($reply_user_id == $id_account) {
                                        echo "<button onclick='deleteComment({$post['id']}, $reply_id, {$id_account})' class='absolute right-0 hover:text-red-600' style='padding: 5px; border: none;'><i class='fa-solid fa-trash'></i></button>";
                                    }
                                    echo "</div></div>
                                        </div>";
                                }
                            }
                        }
                    }
                }
                echo "</div>
                    </div>
                </div>";

                echo "<h5 style='color: #364153; margin-left: 5px; opacity: 0.8;'><i>@" . htmlspecialchars($post['username'] ?? 'Unknown') . "</i></h5>";
                echo "<p style='color: #364153; font-size: 1rem; margin-left: 5px; margin-bottom: 3px; opacity: 0.8 ;'>" . htmlspecialchars($post['captions'] ?? '') . "</p>";
                echo "<small style='color: #364153; opacity: 0.5;'>" . htmlspecialchars($post['tanggal'] ?? 'Tanggal tidak tersedia') . "</small>";

                if (!empty($post['media'])) {
                    echo "</figcaption></figure>";
                }

                echo "</li>";
            }
        } else {
            echo "<div class='list-group-item' style='width: 500px; padding: 10px; margin-bottom: 10px; background-color: white; border: 1px solid #364153; color: #364153; border-radius: 15px; text-align: center;'>";
            echo "Belum ada postingan.";
            echo "</div>";
        }
        ?>

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

        <div id="modalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 999;"></div>
    </div>
</body>

</html>


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

    document.getElementById('toggle-more')?.addEventListener('click', function() {
        const moreText = document.getElementById('more-text');
        const button = this;

        if (moreText.style.display === 'none') {
            moreText.style.display = 'inline';
            button.innerText = 'Sembunyikan';
        } else {
            moreText.style.display = 'none';
            button.innerText = 'Selengkapnya';
        }
    });

    function likePost(id, id_account) {
        const icon = document.getElementById(`like-icon-${id}`);
        const likeCountSpan = document.getElementById(`like-count-${id}`);

        const isLiked = icon.classList.contains('fa-solid');

        fetch('like_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: id,
                    id_account: id_account
                })
            })
            .then(data => {
                console.log("Response dari server:", data);

                if (!isLiked) {
                    console.log(JSON.stringify(data))

                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');
                    likeCountSpan.textContent = Number(likeCountSpan.textContent) + 1;
                    console.log("Klik berhasil, mengirim data:", {
                        id,
                        id_account
                    });
                } else {
                    console.log(JSON.stringify(data))

                    icon.classList.add('fa-regular');
                    icon.classList.remove('fa-solid');
                    likeCountSpan.textContent = Number(likeCountSpan.textContent) - 1;
                    console.log("Klik berhasil, mengirim data:", {
                        id,
                        id_account
                    });
                }
            })
            .catch(error => console.error(error));
    }

    function share() {
        const currentUrl = window.location.href.toString();
        console.log(currentUrl);

        // Periksa apakah Clipboard API tersedia
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(currentUrl)
                .then(() => {
                    alert('Link berhasil disalin ke clipboard!');
                })
                .catch(err => {
                    console.error('Gagal menyalin link:', err);
                    alert('Gagal menyalin link. Silakan coba lagi.');
                });
        } else {
            alert('Fitur salin otomatis tidak didukung di browser ini.');
            console.error('Clipboard API tidak tersedia.');
        }
    }

    function saveComment(postId, accountId, parentId = null) {
        const inputId = parentId ? `reply-input-${parentId}` : `commentInput-${postId}`;
        const commentInput = document.getElementById(inputId);
        const comment = commentInput.value.trim();
        const button = commentInput.nextElementSibling; // ambil tombol kirim
        console.log("Kepanggil:", postId, accountId, parentId, inputId, comment, button);

        if (comment === "") {
            commentInput.style.border = "1px solid red";
            commentInput.placeholder = "Komentar tidak boleh kosong!";
            commentInput.focus();
            return;
        }

        commentInput.style.border = "1px solid #ffffff";
        button.disabled = true; // ❗ Matikan tombol biar gak dobel kirim

        const params = new URLSearchParams();
        params.append("id_thread", postId);
        params.append("id_account", accountId);
        params.append("komentar", comment);
        if (parentId) {
            params.append("parent_id", parentId);
        }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "save_comment.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                button.disabled = false; // aktifkan lagi tombol
                if (xhr.status === 200) {
                    Swal.fire("Sukses!", "Komentar berhasil ditambahkan!", "success").then(() => {
                        location.reload();
                    });
                    commentInput.value = "";
                } else {
                    alert("Gagal menyimpan komentar. Silakan coba lagi.");
                }
            }
        };
        xhr.send(params.toString());
    }

    function toggleReplyForm(commentId) {
        const thisForm = document.getElementById(`reply-form-${commentId}`);
        const isVisible = thisForm && thisForm.style.display === 'block';

        // Tutup semua form reply
        const allReplyForms = document.querySelectorAll("[id^='reply-form-']");
        allReplyForms.forEach(form => form.style.display = 'none');

        // Kalau form yang diklik sebelumnya tertutup, buka dia
        if (!isVisible && thisForm) {
            thisForm.style.display = 'block';
        }
    }

    function closeAllCommentModals() {
        const allModals = document.querySelectorAll("[id^='commentModal-']");
        allModals.forEach(modal => {
            modal.style.display = 'none';
        });
    }

    function openCommentModal(postId) {
        closeAllCommentModals();
        const modal = document.getElementById(`commentModal-${postId}`);
        if (modal) {
            modal.style.display = 'block';
        }
    }

    function closeModal(postId) {
        const modal = document.getElementById(`commentModal-${postId}`);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    let currentMutedStatus = true;
    let currentMutedVideoId = null;

    function toggleMute() {
        const videos = document.querySelectorAll('video');
        const muteButtons = document.querySelectorAll('button[id^="muteButton"]');
        const unmuteButtons = document.querySelectorAll('button[id^="unmuteButton"]');

        currentMutedStatus = !currentMutedStatus;

        videos.forEach(video => {
            if (currentMutedStatus) {
                video.muted = true;
            } else {
                video.muted = false;
            }
        });

        unmuteButtons.forEach(button => {
            button.style.display = currentMutedStatus ? 'none' : 'inline';
        });
        muteButtons.forEach(button => {
            button.style.display = currentMutedStatus ? 'inline' : 'none';
        });
    }

    function isVideoInViewport(video) {
        const rect = video.getBoundingClientRect();
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;

        return rect.top >= 0 && rect.bottom <= windowHeight;
    }

    var observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            var video = entry.target;

            if (entry.isIntersecting) {
                video.play();

                if (!currentMutedStatus && video.muted) {
                    video.muted = false;
                }
            } else {
                video.pause();
                if (!currentMutedStatus) {
                    video.muted = true;
                }
            }
        });
    }, {
        threshold: 0.8
    });

    document.querySelectorAll('video').forEach(video => {
        observer.observe(video);
    });

    function follow(userId, friendId, btn) {
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
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const container = btn.parentElement;
                    container.querySelector('.add-button').style.display = 'none';
                    container.querySelector('.unfriend-button').style.display = 'block';
                    location.reload();
                } else {
                    alert("Gagal follow: " + data.message);
                }
            });
    }

    function unFollow(userId, friendId, btn) {
        Swal.fire({
            text: "Apakah kamu yakin?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, lanjutkan!",
            cancelButtonText: "Batal"
        }).then(result => {
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
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const container = btn.parentElement;
                            container.querySelector('.add-button').style.display = 'block';
                            container.querySelector('.unfriend-button').style.display = 'none';
                            location.reload();
                        } else {
                            alert("Gagal unfollow: " + data.message);
                        }
                    });
            }
        });
    }

    function deleteComment(postId, commentId, accountId) {
        Swal.fire({
            text: "Apakah kamu yakin?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya!",
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
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_comment.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onerror = function() {
                    console.error("Terjadi kesalahan saat mengirim request ke server.");
                };

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            console.log("Komentar berhasil dihapus!");
                            Swal.fire("Sukses!", "Komentar berhasil dihapus!", "success").then(() => {
                                location.reload();
                            });
                        } else {
                            console.error("Gagal menghapus komentar. Status: " + xhr.status);
                            Swal.fire("Gagal!", "Gagal menghapus komentar. Silakan coba lagi.", "error");
                        }
                    }
                };

                console.log("Mengirim data:", `id_thread=${postId}&id_comment=${commentId}&id_account=${accountId}`);
                xhr.send(`id_thread=${postId}&id_comment=${commentId}&id_account=${accountId}`);
            }
        });
    }
</script>