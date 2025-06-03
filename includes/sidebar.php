<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<link rel="icon" href="./assets/Unitalk_logo2.png">
<!-- <link rel="stylesheet" href="includes/sidebar.css"> -->

<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

$username = $_SESSION['username'];
$id_session = $_SESSION['id_account'];

$sql_me = "SELECT friendlist.*, account.* FROM friendlist INNER JOIN account ON friendlist.friend_id = account.id_account WHERE friendlist.friend_id = $id_session";
$result_me = $conn->query($sql_me);

if ($result_me->num_rows > 0) {
  $my_account = $result_me->fetch_all(MYSQLI_ASSOC);
} else {
  error_log("No following found");
  $my_account = [];
}

$posts = [];
$sql = "SELECT 'thread' AS tipe, threads.id_account, account.username, threads.captions, account.foto_profil AS pesan, threads.id, 
    threads.tanggal, threads.media, threads.jenis FROM threads JOIN account ON threads.id_account = account.id_account
  WHERE threads.jenis IN ('gambar', 'text', 'video') ORDER BY threads.tanggal DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
  }
}

$sql_unread_count = "
  SELECT COUNT(*) AS unread_count FROM (
    SELECT likes.id_like FROM likes
    JOIN threads ON likes.id_thread = threads.id
    WHERE likes.status = 'unread' AND threads.id_account = '$id_session'

    UNION ALL

    SELECT comment.id_comment FROM comment
    JOIN threads ON comment.id_thread = threads.id
    WHERE comment.status = 'unread' AND threads.id_account = '$id_session'
  ) AS unread_notifications
";

$result_unread_count = mysqli_query($conn, $sql_unread_count);
$unread_count = 0;

if ($result_unread_count) {
  $row = mysqli_fetch_assoc($result_unread_count);
  $unread_count = $row['unread_count'];
}
?>


<div id="sidebar" class="font-lilita fixed h-full shadow-xl overflow-x-hidden bg-gray-600 text-white transition-all duration-300 w-[230px] [&.sidebar-collapsed]:w-[70px] [&.sidebar-collapsed]:px-1">
  <div class="pt-2 flex items-center gap-2">
    <img src="./assets/Unitalk_logo2.png" alt="logo" class="w-[40px] h-[30px] ml-2 cursor-pointer" id="logo" onclick="toggleSidebarSlide()">
    <img src="./assets/Unitalk_nama2.png" alt="title" class="w-[150px] h-[30px] cursor-pointer" id="sidebarTitle">
  </div>
  <hr class="border-white mb-6 ml-2.5 w-full" />
  <ul class="list-none p-0">
    <li class="mb-[15px]">
      <a class="px-3 py-2 hover:bg-[#464d54] text-white flex items-center p-[10px] rounded-[5px] transition-colors duration-300" style="text-decoration: none;" href="index.php?username=<?php echo $username; ?>">
        <i class="fas fa-home mr-2.5"></i>
        <span class="sidebar-text">Home</span>
      </a>
    </li>
    <li class="mb-[15px]">
      <a class="px-3 py-2 hover:bg-[#464d54] text-white flex items-center p-[10px] rounded-[5px] transition-colors duration-300" style="text-decoration: none;" id="searchButton" href="javascript:void(0);">
        <i class="fas fa-search mr-2.5"></i>
        <span class="sidebar-text">Search</span>
      </a>
    </li>
    <li class="mb-[15px]">
      <a class="px-3 py-2 hover:bg-[#464d54] relative text-white flex items-center p-[10px] rounded-[5px] transition-colors duration-300" style="text-decoration: none;" id="notificationButton" href="javascript:void(0);">
        <i class="fas fa-bell mr-2.5"></i>
        <span class="sidebar-text">Notification</span>
        <span id="notificationDot" class="absolute top-4 left-4.5 w-3 h-3 size-2 translate-x-1/2 -translate-y-1/2 bg-red-500 text-xs rounded-full px-1 <?php echo $unread_count == 0 ? 'hidden' : ''; ?>">
          <!-- <?php echo $unread_count; ?> -->
        </span>
      </a>
    </li>
    <li class="mb-[15px]">
      <a id="toggleSidebarButton" class="px-3 py-2 hover:bg-[#464d54] text-white flex items-center p-[10px] rounded-[5px] transition-colors duration-300 cursor-pointer" style="text-decoration: none;">
        <i class="fas fa-plus mr-[13px]"></i>
        <span class="sidebar-text">Upload</span>
      </a>
    </li>

    <!-- Popup Upload -->
    <div id="tambah" class="hidden fixed border-2 border-gray-700 bg-gray-400 text-[#364153] p-4 w-[250px] rounded-lg z-[1050]">
      <div class="flex justify-between items-center mb-2">
        <h5 class="m-0 text-base">Upload</h5>
        <button type="button" onclick="toggleSidebar()" class="text-3xl text-[#364153] bg-transparent border-none">&times;</button>
      </div>
      <ul class="list-none p-0 mt-[-10px]">
        <li>
          <hr class="border-[#B1AAFE]" />
        </li>
        <li class="mb-2">
          <a class="px-2 py-1 hover:bg-white flex items-center p-[10px] rounded-[5px] transition-colors duration-300" style="text-decoration:none; color: #364153;" href="obrolan.php?username=<?php echo $username; ?>">
            <i class="fa-solid fa-font mr-2.5"></i> Text
          </a>
        </li>
        <li>
          <a class="px-2 py-1 hover:bg-white flex items-center p-[10px] rounded-[5px] transition-colors duration-300" style="text-decoration:none; color: #364153;" href="gambar.php?username=<?php echo $username; ?>">
            <i class="fas fa-image mr-2.5"></i> Gambar atau Video
          </a>
        </li>
      </ul>
    </div>

    <li>
      <hr class="border-white w-full ml-2.5 my-4" />
    </li>

    <!-- Bagian bawah -->
    <div class="bot">
      <li class="mb-[15px]">
        <a class="px-3 py-2 hover:bg-[#464d54] text-white flex items-center p-[10px] rounded-[5px] transition-colors duration-300" style="text-decoration: none;" id="profile" href="user_profile.php?username=<?php echo $username; ?>">
          <i class="fas fa-user mr-2.5"></i>
          <span class="sidebar-text">Profile</span>
        </a>
      </li>
      <li class="mb-[15px]">
        <a class="px-3 py-2 hover:bg-[#464d54] text-white flex items-center p-[10px] rounded-[5px] transition-colors duration-300" style="text-decoration: none;" href="settings.php?username=<?php echo $username; ?>">
          <i class="fas fa-cog mr-2.5"></i>
          <span class="sidebar-text">Settings</span>
        </a>
      </li>
    </div>
  </ul>
</div>

<div id="searchPopup" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-2" style="width: 450px; background-color: #99a1af; border-radius: 10px; border-color: #364153;">
      <div class="modal-header">
        <h5 class="modal-title" style="color: #364153;">Search</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" style="color: #364153; background-color: transparent;">
          <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div style="display: flex; flex-direction: row; align-items: center; margin-bottom: 15px;">
          <input class="form-control" name="input_search" id="searchInput" style="border-color: #364153; opacity: 0.8; color: #364153;" type="text" placeholder="Search" autocomplete="off" aria-label="Search">
          <button class="btn btn-outline-success" style="border-color: #364153; padding: 10px; border:none;" name="search" onclick="showSearch()"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
        <div id="searchResults" style="display: none;"></div>
      </div>
    </div>
  </div>
</div>

<div id="notificationPopup" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content w-[500px] h-[550px] border-3 border-gray-700" style="background-color: #99a1af; border-radius: 10px;">
      <div class="modal-header pb-2">
        <h4 class="modal-title text-[#364153] m-0">Notification</h4>
        <button type="button" class="close text-gray-700 bg-transparent p-0 border-transparent" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="text-2xl">&times;</span>
        </button>
      </div>
      <div class="modal-body overflow-y-auto">
        <?php
        echo "<h5 class='text-[#364153]'>Following
              <span id='toggle-button-following' class='cursor-pointer absolute right-[30px]'><i class='fa-solid fa-arrow-turn-down'></i></span>
          </h5>";
        echo "<div id='following-list' class='block'>";
        foreach ($my_account as $row) {
          $user_id = $row['friend_id'];
          $friend_id = $row['user_id'];

          $check_status = "SELECT * FROM friendlist WHERE user_id = $user_id AND friend_id = $friend_id";
          $result_status = mysqli_query($conn, $check_status);
          $status = mysqli_num_rows($result_status) > 0 ? 'following' : 'not_following';

          $sql_friend_profile = "SELECT * FROM account WHERE id_account = $friend_id AND id_account != $id_session";
          $result_friend_profile = $conn->query($sql_friend_profile);

          if ($result_friend_profile->num_rows > 0) {
            $friend_profile = $result_friend_profile->fetch_all(MYSQLI_ASSOC);
            foreach ($friend_profile as $row) {
              $profile_picture = $row['foto_profil'];
              $profile_name = $row['username'];

              echo "
              <ul class='list-none mb-1.5 mt-2 flex justify-between'>
                <a href='search_profile.php?username=$profile_name' class='text-white' style='text-decoration: none;'>
                  <li class='flex items-center'>
                    <img src='$profile_picture' class='figure-img img-fluid w-[50px] h-[50px] object-cover mb-2 border border-white rounded-full' style='widht: 50px; height: 50px;'>
                    <div class='desc ml-2.5'>
                      <h5 class='text-white opacity-80'>@$profile_name</h5>
                      <h6 class='text-white opacity-80'>Mulai mengikuti anda</h6>
                    </div>
                  </li>
                </a>
                <button class='add-button w-[100px] bg-green-400 text-white hover:bg-green-600 border-none cursor-pointer' 
                    style='border-radius:10px; " . ($status === 'following' ? "display:none;" : "") . "' 
                    onclick='follow($user_id, $friend_id, this)'>
                    Follow
                </button>
                
                <button class='unfriend-button w-[100px] bg-red-400 text-white hover:bg-red-600 border-none cursor-pointer' 
                    style='border-radius:10px; " . ($status === 'following' ? "" : "display:none;") . "' 
                    onclick='unFollow($user_id, $friend_id, this)'>
                    Unfollow
                </button>
              </ul>
              <hr class='dropdown-divider text-white'>";
            }
          } else {
            echo "<h5 class='text-[#364153]'>No Following</h5>";
          }
        }
        echo "</div>";

        echo "<h5 class='text-[#364153] mt-4'>Like & Comment 
              <span id='toggle-button' class='cursor-pointer absolute right-[30px]'><i class='fa-solid fa-arrow-turn-down'></i></span>
          </h5>";
        echo "<div id='like-list' class='block'>";
        if (count($posts) > 0) {
          foreach ($posts as $post) {
            $post_id = $post['id'];
            $tipe = $post['jenis'];
            $media_path = isset($post['media']) ? 'assets/media/' . htmlspecialchars($post['media']) : '';

            $sql_check_like = "SELECT likes.*, account.username, account.foto_profil FROM threads JOIN likes ON threads.id = likes.id_thread 
                  JOIN account ON likes.id_account = account.id_account WHERE threads.id = $post_id AND threads.id_account = $id_session ORDER BY likes.waktu DESC";
            $result_check_like = mysqli_query($conn, $sql_check_like);

            $sql_comment = "SELECT comment.*, account.username, account.foto_profil FROM threads JOIN comment ON threads.id = comment.id_thread 
                  JOIN account ON comment.id_account = account.id_account WHERE threads.id = $post_id AND threads.id_account = $id_session ORDER BY comment.waktu DESC";
            $result_comment = mysqli_query($conn, $sql_comment);

            if ($result_check_like && $result_check_like->num_rows > 0) {
              while ($like_row = $result_check_like->fetch_assoc()) {
                $profile_name = $like_row['username'];
                $profile_picture = $like_row['foto_profil'];

                $profile_url = ($profile_name == $username) ? 'user_profile.php?username=' . $profile_name : 'search_profile.php?username=' . $profile_name;
                echo "
                      <ul class='list-none mb-1.5 flex justify-between mr-5'>
                          <a href='$profile_url' class='text-white' style='text-decoration: none;'>
                              <li class='flex items-center'>
                                  <img src='$profile_picture' class='figure-img img-fluid w-[50px] h-[50px] object-cover mb-2 border border-white rounded-full' style='widht: 50px; height: 50px;'>
                                  <div class='desc ml-2.5'>
                                      <h5 class='text-white opacity-80'>@$profile_name</h5>
                                      <h6 class='text-white opacity-80'>Menyukai postingan anda</h6>
                                  </div>
                              </li>
                          </a>";

                if ($tipe == 'thread' || $tipe == 'gambar') {
                  echo "<img src='$media_path' class='figure-img img-fluid w-[50px] h-[50px] object-cover mb-2 border border-white rounded-[10px]' style='widht: 50px; height: 50px;'>";
                } else if ($tipe == 'video') {
                  echo "<video width='50' height='50' autoplay muted loop playsinline class='object-cover mb-2 border border-white rounded-[10px]' style='widht: 50px; height: 50px;'>
                              <source src='$media_path' type='video/mp4'> Browser Anda tidak mendukung pemutaran video.</video>";
                } else if ($tipe == 'text') {
                  $post['captions'] = substr($post['captions'], 0, 5) . "...";
                  echo "<p class='text-white opacity-80 ml-2.5 mt-2.5 w-[50px]'>$post[captions]</p>";
                }
                echo "</ul><hr class='dropdown-divider text-white'>";
              }
            }

            if ($result_comment && $result_comment->num_rows > 0) {
              while ($comment_row = $result_comment->fetch_assoc()) {
                $profile_name = $comment_row['username'];
                $profile_picture = $comment_row['foto_profil'];

                $profile_url = ($profile_name == $username) ? 'user_profile.php?username=' . $profile_name : 'search_profile.php?username=' . $profile_name;
                echo "
                      <ul class='list-none mb-1.5 flex justify-between mr-5'>
                          <a href='$profile_url' class='text-white' style='text-decoration: none;'>
                          <li class='flex items-center'>
                                  <img src='$profile_picture' class='figure-img img-fluid w-[50px] h-[50px] object-cover mb-2 border border-white rounded-full' style='widht: 50px; height: 50px;'>
                                  <div class='desc ml-2.5'>
                                      <h5 class='text-white opacity-80'>@$profile_name</h5>
                                      <h6 class='text-white opacity-80'>Mengomentari postingan anda</h6>
                                  </div>
                              </li>
                          </a>";

                if ($tipe == 'thread' || $tipe == 'gambar') {
                  echo "<img src='$media_path' class='figure-img img-fluid w-[50px] h-[50px] object-cover mb-2 border border-white rounded-[10px]' style='widht: 50px; height: 50px;'>";
                } else if ($tipe == 'video') {
                  echo "<video width='50' height='50' autoplay loop playsinline class='object-cover mb-2 border border-white rounded-[10px]' style='widht: 50px; height: 50px;'>
                              <source src='$media_path' type='video/mp4'> Browser Anda tidak mendukung pemutaran video.</video>";
                } else if ($tipe == 'text') {
                  $post['captions'] = substr($post['captions'], 0, 5) . "...";
                  echo "<p class='text-white opacity-80 ml-2.5 mt-2.5 w-[50px]'>$post[captions]</p>";
                }
                echo "</ul><hr class='dropdown-divider text-white'>";
              }
            }
          }
        } else {
          echo "No posts found.";
        }
        echo "</div>";
        ?>
      </div>
    </div>
  </div>
</div>

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

  #notificationButton .dot {
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

  #notificationButton .dot.hidden {
    display: none;
  }
</style>

<script>
  function toggleSidebarSlide() {
    const sidebar = document.getElementById("sidebar");
    const title = document.getElementById("sidebarTitle");
    const texts = document.querySelectorAll(".sidebar-text");
    const links = document.querySelectorAll("#sidebar ul li a");
    const logo = document.getElementById("logo");

    sidebar.classList.toggle("w-[230px]");
    sidebar.classList.toggle("w-[70px]");
    sidebar.classList.toggle("px-3");
    sidebar.classList.toggle("px-1");
    sidebar.classList.toggle("items-start");
    sidebar.classList.toggle("items-center");

    logo.classList.toggle("ml-[-5px]")

    title.classList.toggle("hidden");

    texts.forEach(text => {
      text.classList.toggle("hidden");
    });

    links.forEach(link => {
      link.classList.toggle("justify-start");
      link.classList.toggle("justify-center");
      link.classList.toggle("px-3");
      link.classList.toggle("px-0");
    });
  }


  let friendStatus = "<?php echo $status; ?>";
  console.log(friendStatus);
  document.addEventListener('DOMContentLoaded', function() {
    if (friendStatus === "following") {
      document.getElementById('add-button').style.display = 'none';
      document.getElementById('unfriend-button').style.display = 'block';
    } else {
      document.getElementById('add-button').style.display = 'block';
      document.getElementById('unfriend-button').style.display = 'none';
    }
  });

  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('toggle-button').addEventListener('click', function() {
      var list = document.getElementById('like-list');
      var button = document.getElementById('toggle-button');

      if (list.style.display === 'block') {
        list.style.display = 'none';
        button.innerHTML = '<i class="fa-solid fa-arrow-turn-up"></i>';
      } else {
        list.style.display = 'block';
        button.innerHTML = '<i class="fa-solid fa-arrow-turn-down"></i>';
      }
    });
  });

  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('toggle-button-following').addEventListener('click', function() {
      var list = document.getElementById('following-list');
      var button = document.getElementById('toggle-button-following');

      if (list.style.display === 'block') {
        list.style.display = 'none';
        button.innerHTML = '<i class="fa-solid fa-arrow-turn-up"></i>';
      } else {
        list.style.display = 'block';
        button.innerHTML = '<i class="fa-solid fa-arrow-turn-down"></i>';
      }
    });
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
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log("Permintaan pertemanan berhasil dikirim!");
          const container = btn.parentElement;
          container.querySelector('.add-button').style.display = 'none';
          container.querySelector('.unfriend-button').style.display = 'block';
        } else {
          alert("Gagal: " + data.message);
        }
      })
      .catch(error => {
        alert("Terjadi kesalahan.");
      });
  }

  function unFollow(userId, friendId, btn) {
    Swal.fire({
      text: "Apakah kamu yakin?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, lanjutkan!",
      cancelButtonText: "Batal"
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
              console.log("Berhasil unfollow!");
              const container = btn.parentElement;
              container.querySelector('.add-button').style.display = 'block';
              container.querySelector('.unfriend-button').style.display = 'none';
            } else {
              alert("Gagal: " + data.message);
            }
          })
          .catch(error => {
            alert("Terjadi kesalahan.");
          });
      }
    });
  }

    document.getElementById('notificationButton').addEventListener('click', () => {

      document.getElementById('notificationDot').classList.add('hidden');

      const xhr = new XMLHttpRequest();
      xhr.open("POST", "update_notification_status.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            console.log(xhr.responseText);
            try {
              const response = JSON.parse(xhr.responseText);
              if (response.success) {
                console.log('Notifikasi berhasil diperbarui.');
              } else {
                console.error('Error dari server:', response.message || response.error);
              }
            } catch (e) {
              console.error('Error parsing JSON:', e);
            }
          } else {
            console.error('HTTP Error:', xhr.status);
          }
        }
      };
      xhr.send('action=mark_as_read');
    });

  document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('notificationButton').addEventListener('click', () => {
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
});

  let hasNewNotifications = true;
</script>

<script src="includes/sidebar.js"></script>
<script src="bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>