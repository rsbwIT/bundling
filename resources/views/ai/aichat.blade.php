<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
        }
        .chat-container {
            width: 50%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .chat-box {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            background: #fafafa;
        }
        .user, .ai {
            margin-bottom: 10px;
        }
        .user {
            color: blue;
            font-weight: bold;
        }
        .ai {
            color: green;
            font-weight: bold;
        }
        .input-container {
            display: flex;
        }
        input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px;
            margin-left: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        button:disabled {
            background-color: #ccc;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <h2>Chat dengan AI</h2>
    <div class="chat-box" id="chat-box"></div>

    <div class="input-container">
        <input type="text" id="message" placeholder="Ketik pesan..." />
        <button id="send" onclick="sendMessage()">Kirim</button>
    </div>
</div>

<script>
    function sendMessage() {
        let message = $('#message').val();
        if (message.trim() === '') {
            alert("Pesan tidak boleh kosong!");
            return;
        }

        $('#chat-box').append(`<div class="user">Anda: ${message}</div>`);
        $('#message').val('');
        $('#send').prop('disabled', true);

        $.ajax({
            url: "{{ url('/api/ai/chat') }}",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                message: message
            }),
            success: function(response) {
                $('#chat-box').append(`<div class="ai">AI: ${response.response}</div>`);
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON?.error || "Terjadi kesalahan!";
                $('#chat-box').append(`<div class="ai">AI: ${errorMessage}</div>`);
            },
            complete: function() {
                $('#send').prop('disabled', false);
            }
        });
    }
</script>

</body>
</html>
