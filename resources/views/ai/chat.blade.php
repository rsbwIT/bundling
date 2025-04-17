<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Dr. AI</title>
    <style>
        .chat-container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            /* Ganti dengan warna latar belakang yang diinginkan */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 80vh;
        }

        .chat-box {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
            flex-grow: 1;
            overflow-y: auto;
            padding-right: 5px;
            background-color: #f1f1f1;
            /* Ganti dengan warna latar belakang kotak chat */
        }

        .bubble {
            padding: 10px 15px;
            border-radius: 20px;
            margin: 5px 0;
            max-width: 80%;
        }

        .user {
            background: #007bff;
            /* Ganti warna balon pengguna (user) */
            color: white;
            align-self: flex-end;
        }

        .bot {
            background: #e4e6eb;
            /* Ganti warna balon bot */
            color: black;
            align-self: flex-start;
        }

        .input-area {
            display: flex;
        }

        input[type="text"] {
            flex: 1;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 20px;
            border: none;
            background: #007bff;
            /* Ganti warna tombol */
            color: white;
            border-radius: 20px;
            margin-left: 10px;
            cursor: pointer;
        }

        button:disabled {
            background: #aaa;
            /* Ganti warna tombol yang dinonaktifkan */
        }

        .floating-btn {
            position: fixed;
            right: 20px;
            bottom: 20px;
            width: 50px;
            height: 50px;
            background: #007bff;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            text-decoration: none;
            font-size: 22px;
            transition: background 0.3s;
            z-index: 1000;
        }

        .floating-btn:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>

    <div class="chat-container">
        <h2 style="text-align: center; font-weight: 600;">Ngobrol Bareng Dr. AI 🤖</h2>
        <div id="chatBox" class="chat-box"></div>

        <div class="input-area">
            <input type="text" id="messageInput" placeholder="Ketik pesan..." />
            <button onclick="sendMessage()" id="sendBtn">Kirim</button>
        </div>
    </div>

    <script>
        let chatBox = document.getElementById('chatBox');

        function appendMessage(content, sender = 'user') {
            let bubble = document.createElement('div');
            bubble.className = `bubble ${sender}`;
            bubble.textContent = content;
            chatBox.appendChild(bubble);

            // Scroll otomatis ke bawah
            setTimeout(() => {
                chatBox.scrollTo({
                    top: chatBox.scrollHeight,
                    behavior: 'smooth'
                });
            }, 50); // Delay sedikit untuk memastikan elemen baru ditambahkan
        }

        function sendMessage() {
            let input = document.getElementById('messageInput');
            let text = input.value.trim();
            if (!text) return;

            appendMessage(text, 'user');
            input.value = '';
            document.getElementById('sendBtn').disabled = true;


            fetch('/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message: text
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.errors && data.errors.message) {
                        appendMessage(data.errors.message[0], 'bot'); // Menampilkan error
                    } else {
                        appendMessage(data.reply, 'bot');
                    }
                    document.getElementById('sendBtn').disabled = false;
                })
                .catch(() => {
                    appendMessage("Gagal mengirim pesan.", 'bot');
                    document.getElementById('sendBtn').disabled = false;
                });

            function scrollToBottom() {
                chatBox.scrollTo({
                    top: chatBox.scrollHeight,
                    behavior: 'smooth'
                });
            }

        }
    </script>
    <a href="{{ url('/') }}" class="floating-btn" title="Kembali ke Home">
        <i class="fas fa-home"></i>
    </a>


</body>

</html>
