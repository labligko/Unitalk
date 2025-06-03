<?php
include 'config/db.php';

$username = $_SESSION['username'];
$id_session = $_SESSION['id_account'];

// Ambil daftar teman
$stmt = $conn->prepare("
    SELECT account.id_account, account.username, account.foto_profil
    FROM friendlist
    JOIN account ON account.id_account = friendlist.friend_id
    WHERE friendlist.user_id = ? AND friendlist.status = 'following'
");
$stmt->bind_param("i", $id_session);
$stmt->execute();
$friends = $stmt->get_result();
?>

<!-- WRAPPER CHAT SIDEBAR -->
<div class="w-[400px] h-[600px] p-4 bg-white shadow rounded" >

    <!-- LIST TEMAN -->
    <div class="mb-4" id="friend-list">
        <h2 class="text-lg font-bold mb-2">Daftar Teman</h2>
        <ul>
            <?php while ($row = $friends->fetch_assoc()): ?>
                <li>
                    <button 
                        class="friend-btn flex items-center w-full text-left py-2 px-2 hover:bg-gray-100 rounded"
                        data-id="<?= $row['id_account']; ?>"
                        data-name="<?= htmlspecialchars($row['username']); ?>">
                        <img src="<?= $row['foto_profil']; ?>" class="w-6 h-6 rounded-full mr-2" alt="">
                        <?= htmlspecialchars($row['username']); ?>
                    </button>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- AREA CHAT -->
    <div id="chat-area" class="hidden fixed top-5 bg-white w-[360px] h-[600px] z-99">
        <div class="flex justify-between items-center p-2">
            <button onclick="closeChatsession()" class="text-sm bg-gray-600 text-white px-2 rounded hover:bg-gray-800">
                &larr;
            </button>
            <h3 class="text-sm font-semibold mb-2" id="chat-title">Chat</h3>
        </div>
        <div class="border-1 h-[450px] overflow-y-scroll mb-2 p-2 text-sm" id="chat-box">
            <!-- Isi chat -->
        </div>
        <form id="chat-form" class="flex">
            <input type="text" id="chat-input" class="flex-grow border rounded-l p-2 text-sm" placeholder="Tulis pesan...">
            <button type="submit" class="bg-blue-500 text-white px-4 rounded-r">Kirim</button>
        </form>
    </div>
</div>

<!-- SCRIPT CHAT -->
<script>
let receiverId = null;

document.querySelectorAll('.friend-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        receiverId = btn.dataset.id;
        const name = btn.dataset.name;

        document.getElementById('chat-area').classList.remove('hidden');
        document.getElementById('chat-title').textContent = "Chat dengan " + name;
        document.getElementById('friend-list').classList.add('hidden');

        loadChat();
    });
});

function loadChat() {
    if (!receiverId) return;
    fetch('chat_load.php?receiver_id=' + receiverId)
        .then(res => res.text())
        .then(data => {
            const chatBox = document.getElementById('chat-box');
            chatBox.innerHTML = data;
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    if (message === "" || !receiverId) return;

    fetch('chat.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `message=${encodeURIComponent(message)}&receiver_id=${receiverId}`
    }).then(() => {
        input.value = '';
        loadChat();
    });
});

setInterval(() => { if (receiverId) loadChat(); }, 2000);

closeChatsession = () => {
    document.getElementById('chat-area').classList.add('hidden');
    document.getElementById('friend-list').classList.remove('hidden');
    receiverId = null;
}

</script>
