<?php
include 'config/db.php';
include 'includes/sidebar.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect jika belum login
    exit;
}

$username = $conn->real_escape_string($_SESSION['username']);
$id_account = $conn->real_escape_string($_SESSION['id_account']);

$sql_threads = "SELECT threads.*, account.username, account.foto_profil 
                FROM threads 
                JOIN account ON threads.id_account = account.id_account 
                JOIN likes ON threads.id = likes.id_thread 
                WHERE likes.id_account = ? 
                ORDER BY threads.tanggal DESC";
$stmt_threads = $conn->prepare($sql_threads);
$stmt_threads->bind_param('s', $id_account);
$stmt_threads->execute();
$result_threads = $stmt_threads->get_result();
$posts = $result_threads->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liked</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .font-lilita {
            font-family: 'Lilita One', cursive;
        }

        /* .btn-comment-click {
            background-color: #3F3C3C;
            color: #ffffff;
        }

        .btn-comment-click:hover {
            background-color: #B1AAFE;
            color: #3F3C3C;
            border-radius: 5px;
        }

        .container li img,
        .container li video {
            max-height: 500px;
        }*/

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

        @media (max-width: 768px) {
            .comment-modal {
                margin: 0;
                width: 95%;
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
                position: absolute;
                top: 80px;
                left: -400;
            }

            .container li {
                max-width: 390px;
            }

            .container li img,
            .container li video {
                max-width: 100%;
                height: auto;
                max-height: 500px;
            }
        }
    </style>
</head>

<body class="overflow-x-hidden bg-gray-100 font-lilita text-gray-700">
    <div class="container mx-auto pt-10">
        <div class="bg-white border border-white shadow-lg flex flex-col items-center gap-4 rounded-[15px] py-2 w-[550px] mx-auto">
            <h3 class="text-blue-600 text-xl">Postingan disukai</h3>
            <?php
            if (count($posts) > 0) {
                foreach ($posts as $post) {
                    $id = $post['id'];
                    echo "<li class='list-none w-[500px] mb-2 bg-white border-1 border-gray-700 text-gray-700 rounded-[15px] p-2'>";

                    $sql_like = "SELECT COUNT(*) AS likes FROM likes WHERE id_thread = ?";
                    $stmt_like = $conn->prepare($sql_like);
                    $stmt_like->bind_param("i", $post['id']);
                    $stmt_like->execute();
                    $result_like = $stmt_like->get_result();
                    $likes = $result_like->fetch_assoc()['likes'];

                    $sql_check_like = "SELECT * FROM likes WHERE id_thread = {$post['id']} AND id_account = {$id_account}";
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
                        echo "<figure class='mb-2'>";
                        $image_path = 'assets/media/' . htmlspecialchars($post['media']);
                        echo "<img src='$image_path' class='w-full max-h-[500px] object-cover mb-2 border-1 border-gray-700 rounded-lg' alt='Gambar'>";
                        echo "<figcaption><hr class='border-gray-700 mb-2'></figcaption>";
                    } elseif ($post['jenis'] == 'video') {
                        echo "<figure class='mb-2 relative'>";
                        $video_path = 'assets/media/' . htmlspecialchars($post['media']);
                        echo "<video class='w-full h-auto max-h-[500px] object-cover mb-2 border border-gray-700 rounded-lg' autoplay muted loop playsinline><source src='$video_path' type='video/mp4'>Browser Anda tidak mendukung pemutaran video.</video>";
                        echo "<button id='muteButton-{$post['id']}' onclick='toggleMute()' class='absolute bottom-2 right-2 text-gray-700'><i class='fa-solid fa-volume-xmark'></i></button>";
                        echo "<button id='unmuteButton-{$post['id']}' onclick='toggleMute()' class='absolute bottom-2 right-2 text-gray-700 hidden'><i class='fa-solid fa-volume-high'></i></button>";
                        echo "<figcaption><hr class='border-gray-700 mb-2'></figcaption>";
                    } else {
                        echo "<div>";
                    }

                    echo "<div class='mb-1 mt-2 ml-2 text-gray-700'>";
                    echo "<a class='mr-2 text-xl cursor-pointer' onclick='likePost({$post['id']}, {$id})'><span id='like-count-{$post['id']}'>$likes</span><i id='like-icon-{$post['id']}' class='{$heart_icon} fa-heart text-red-600 ml-1'></i></a>";
                    echo "<a class='mr-2 text-xl cursor-pointer' onclick='openCommentModal({$post['id']})'><span id='comment-count-{$post['id']}'>$comments_count</span><i class='fa-regular fa-comment ml-1'></i></a>";

                    echo "<div id='commentModal-{$post['id']}' class='comment-modal'>
                            <div style='margin-bottom: 15px; position: relative;'>
                            <label for='newemail' style='color: #364153; font-size: 1.2rem;'>your comment</label>
                            <button type='button' onclick='closeModal({$post['id']})' class='close-btn'>
                                <i class='fa-solid fa-circle-xmark'></i>
                            </button>              
                            <div style='display: flex; flex-direction: row; align-items: center; margin-bottom: 15px; margin-top: 10px;'>
                                <input class='comment-input' name='input_comment' id='commentInput-{$post['id']}' style='padding: 10px; border: 1px solid #364153; border-radius: 5px; background-color: white; color: #364153;' type='text' placeholder='Comment here...' autocomplete='off' aria-label='comment'>
                                <button class='btn-comment-click hover:bg-blue-400 hover:text-white' style='padding: 15px 5px; border:none;' name='search' type='button' onclick='saveComment({$post['id']}, {$id})'>
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
                                                        <button onclick='saveComment({$post['id']}, {$id}, $id_comment)' class='btn-comment-click hover:bg-blue-400 hover:text-white' style='padding: 15px 5px; border:none;'>
                                                            <i class='fa-solid fa-pen'></i>
                                                        </button>
                                                    </div>";

                                // Tombol hapus jika user yang komen
                                if ($id_user_comment == $id) {
                                    echo "<button onclick='deleteComment({$post['id']}, $id_comment, {$id})' class='hover:text-red-600 absolute right-0' style='padding: 5px; border: none;'><i class='fa-solid fa-trash'></i></button>";
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
                                                            <button onclick='toggleReplyForm($reply_id)' class='hover:text-gray-500 text-sm'>Reply</button>
                                                            </div>
                                                            <div id='reply-form-$reply_id' style='display:none; margin-top:10px; width: 100%;'>
                                                            <input type='text' id='reply-input-$reply_id' style='width: 90%; padding: 10px; border: 1px solid #364153; border-radius: 5px;' value='@$reply_user ' />
                                                            <button onclick='saveComment({$post['id']}, {$id}, $reply_id)' class='hover:bg-blue-400 hover:text-white' style='padding: 10px; border:none;'>
                                                            <i class='fa-solid fa-pen'></i>
                                                            </button>
                                                            </div>";
                                        if ($reply_user_id == $id) {
                                            echo "<button onclick='deleteComment({$post['id']}, $reply_id, {$id})' class='absolute right-0 hover:text-red-600' style='padding: 5px; border: none;'><i class='fa-solid fa-trash'></i></button>";
                                        }
                                        echo "</div></div>
                                                </div>";
                                    }
                                }
                            }
                        }
                    }
                    echo "</div></div>";

                    echo "<h5 class='text-gray-700 ml-1 opacity-80'><i>@" . htmlspecialchars($post['username'] ?? 'Unknown') . "</i></h5>";
                    echo "<p class='text-gray-700 text-base ml-1 mb-1 opacity-80'>" . htmlspecialchars($post['captions'] ?? '') . "</p>";
                    echo "<small class='text-gray-700 opacity-50'>" . htmlspecialchars($post['tanggal'] ?? 'Tanggal tidak tersedia') . "</small>";

                    if (!empty($post['media'])) {
                        echo "</figcaption></figure>";
                    }

                    echo "</li>";
                }
            } else {
                echo "<div class='list-none w-[500px] p-2 mb-2 bg-white border-1 border-gray-700 text-gray-700 rounded-[15px] text-center'>Belum ada postingan yang di sukai.</div>";
            }
            ?>
            <div id="modalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 999;"></div>
        </div>
    </div>
</body>

<script>
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
        const form = document.getElementById(`reply-form-${commentId}`);
        form.style.display = form.style.display === "none" ? "block" : "none";
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