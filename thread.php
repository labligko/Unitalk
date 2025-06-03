<?php include 'config/db.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<style>
    .font-lilita {
        font-family: 'Lilita One', cursive;
    }


    .list-group-item img,
    .list-group-item video {
        max-height: 500px;
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

    .image-container {
        border: 1px solid #ffffff;
        border-radius: 50%;
        max-width: 50px;
        width: 50px !important;
        height: 50px;
    }


    @media screen and (max-width: 768px) {
        .comment-modal {
            margin: 0;
            width: 95%;
        }

        .list-group-item img,
        .list-group-item video {
            max-width: 100%;
            height: auto;
            max-height: 500px;
        }
    }
</style>

<?php
$username = $_SESSION['username'];
$sql_id = "SELECT id_account FROM account WHERE username = '$username'";
$result_id = $conn->query($sql_id);
$id = $result_id->fetch_assoc()['id_account'];
?>
<script src="https://cdn.jsdelivr.net/npm/twemoji-picker@2.0.4/dist/twemoji-picker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/twemoji-picker@2.0.4/dist/twemoji-picker.min.css">
<script src="https://cdn.tailwindcss.com"></script>

<div class="overflow-x-hidden bg-gray-100 text-gray-700 font-lilita">
    <ul class="list-group">
        <?php
        $posts = [];

        $sql_threads = "SELECT 'thread' AS tipe, threads.id_account, account.username, threads.captions AS pesan, threads.id, threads.tanggal, threads.media 
        FROM threads JOIN account ON threads.id_account = account.id_account WHERE threads.jenis = 'gambar' ORDER BY threads.tanggal DESC";
        $result_threads = $conn->query($sql_threads);

        if ($result_threads->num_rows > 0) {
            while ($row = $result_threads->fetch_assoc()) {
                $posts[] = $row;
            }
        }

        $sql_obrolan = "SELECT 'obrolan' AS tipe, threads.id_account, account.username, threads.captions, threads.captions AS pesan, threads.id, threads.tanggal
        FROM threads JOIN account ON threads.id_account = account.id_account WHERE threads.jenis = 'text' ORDER BY threads.tanggal DESC";
        $result_obrolan = $conn->query($sql_obrolan);

        if ($result_obrolan->num_rows > 0) {
            while ($row = $result_obrolan->fetch_assoc()) {
                $posts[] = $row;
            }
        }

        $sql_video = "SELECT 'video' AS tipe, threads.id_account, account.username, threads.captions AS pesan, threads.id, threads.tanggal, threads.media 
        FROM threads JOIN account ON threads.id_account = account.id_account WHERE threads.jenis = 'video' ORDER BY threads.tanggal DESC";
        $result_video = $conn->query($sql_video);

        if ($result_video->num_rows > 0) {
            while ($row = $result_video->fetch_assoc()) {
                $posts[] = $row;
            }
        }

        if (count($posts) > 0) {

            usort($posts, function ($a, $b) {
                return strtotime($b['tanggal']) - strtotime($a['tanggal']);
            });

            foreach ($posts as $post) {
                $sql_like = "SELECT COUNT(*) AS likes FROM likes WHERE id_thread = ?";
                $stmt_like = $conn->prepare($sql_like);
                $stmt_like->bind_param("i", $post['id']);
                $stmt_like->execute();
                $result_like = $stmt_like->get_result();
                $likes = $result_like->fetch_assoc()['likes'];

                $sql_check_like = "SELECT * FROM likes WHERE id_thread = {$post['id']} AND id_account = {$id}";
                $result_check_like = mysqli_query($conn, $sql_check_like);
                $liked = mysqli_num_rows($result_check_like) > 0;
                $heart_icon = $liked ? 'fa-solid' : 'fa-regular';

                $sql_comment_count = "SELECT COUNT(*) AS comments FROM comment WHERE id_thread = ?";
                $stmt_comment_count = $conn->prepare($sql_comment_count);
                $stmt_comment_count->bind_param("i", $post['id']);
                $stmt_comment_count->execute();
                $result_comment_count = $stmt_comment_count->get_result();
                $comments_count = $result_comment_count->fetch_assoc()['comments'];

                echo "<li class='list-group-item' id='post-{$post['id']}' style='width: 500px; padding-bottom: 5px; margin-bottom: 10px; background-color: white; border: 1px solid #364153; color: #364153; border-radius: 15px; scroll-snap-align: start;'>";
                echo "<figure class='figure' style='margin-bottom: 5px;'>";

                if ($post['tipe'] == 'thread') {
                    $media_path = 'assets/media/' . htmlspecialchars($post['media']);
                    echo "<img src='$media_path' class='figure-img img-fluid' alt='Gambar' style='width: 500px; height: auto; max-height: 500px; object-fit: cover; margin-bottom: 10px;border: 1px solid #364153; border-radius: 10px;'>";
                    echo "<figcaption class='figure-caption'>";
                    echo "<li style='list-style:none;'><hr class='dropdown-divider' style='color: #364153;'></li>";
                } elseif ($post['tipe'] == 'video') {
                    $media_path = 'assets/media/' . htmlspecialchars($post['media']);
                    echo "<video id='videoPlayer-{$post['id']}' width='470' height='auto' max-height='500px' autoplay muted loop playsinline style='object-fit: cover; margin-bottom: 10px; border: .5px solid #364153; border-radius: 10px;'>";
                    echo "<source src='$media_path' type='video/mp4'>";
                    echo "Browser Anda tidak mendukung pemutaran video.";
                    echo "</video>";
                    echo "<button id='muteButton-{$post['id']}' onclick='toggleMute()' style='position: absolute; bottom: 10px; right: 10px; background-color: transparent; border: none; cursor: pointer; color: #364153;'><i class='fa-solid fa-volume-xmark'></i></button>";
                    echo "<button id='unmuteButton-{$post['id']}' onclick='toggleMute()' style='position: absolute; bottom: 10px; right: 10px; background-color: transparent; border: none; cursor: pointer; color: #364153; display: none;'><i class='fa-solid fa-volume-high'></i></button>";
                    echo "<figcaption class='figure-caption'>";
                    echo "<li style='list-style:none;'><hr class='dropdown-divider' style='color: #364153; padding-bottom: 0px;'></li>";
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

                            // tampilkan reply-nya (nested langsung)
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

                echo "</div>
                    </div>
                </div>";

                echo "<h5 style='color: #364153; margin-left: 5px; opacity: 0.8;'><i>@" . htmlspecialchars($post['username'] ?? 'Unknown') . "</i></h5>";
                echo "<p style='color: #364153; font-size: 1rem; margin-left: 5px; margin-bottom: 3px; opacity: 0.8 ;'>" . htmlspecialchars($post['pesan'] ?? '') . "</p>";
                echo "<small style='color: #364153; opacity: 0.5;'>" . htmlspecialchars($post['tanggal'] ?? 'Tanggal tidak tersedia') . "</small>";

                if ($post['tipe'] == 'thread' || $post['tipe'] == 'video') {
                    echo "</figcaption></figure>";
                }

                echo "</li>";
            }
        } else {
            echo "<li class='list-group-item'>Tidak ada data.</li>";
        }
        ?>
    </ul>

    <div id="modalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 999;"></div>
</div>

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
</script>