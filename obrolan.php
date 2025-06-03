<?php include 'config/db.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Upload Text</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="./assets/Unitalk_logo2.png">
    <style>
        .font-lilita {
            font-family: 'Lilita One', cursive;
        }

        @media screen and (max-width: 768px) {
            .container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .list-group-item {
                width: 100%;
                height: fit-content;
                background-color: #3F3C3C;
                border: 1px solid #ffffff;
                color: #ffffff;
                border-radius: 15px;
            }

            .btn-custom {
                background-color: #00BB00;
                color: #ffffff;
                font-weight: bold;
                border-radius: 10px;
            }

            .btn-custom:hover {
                background-color: #009900;
            }
        }
    </style>
</head>

<body class="bg-gray-100 text-white h-screen flex items-center justify-center font-lilita">
    <div class="w-4/5 flex flex-col items-center justify-center">
        <h3 class="text-blue-600 text-2xl mb-4">Upload Text</h3>
        <div class="w-2/3 bg-white shadow-lg rounded-[15px] p-4">
            <form action="upload.php" method="POST" enctype="multipart/form-data" class="flex flex-col w-full">
                <div class="mb-3 mt-1">
                    <h2 for="title" class="block text-gray-700 mb-2">What's on your mind</h2>
                    <textarea
                        name="caption"
                        class="w-full h-[300px] bg-white text-gray-700 border-2 border-gray-700 rounded-[15px] text-base p-2 resize-none"
                        placeholder="Write here...">
                    </textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        class="bg-red-600 hover:bg-red-800 text-white font-bold rounded-[10px] px-5 py-1"
                        onclick="window.history.back();">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="bg-[#00BB00] hover:bg-[#009900] text-white font-bold rounded-[10px] px-5 py-1">
                        Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>


<script>
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