<?php
session_start();
include 'db.php';

if (!isset($_GET['room']) || !$_GET['room']) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
$room_id = $_GET['room'];
$_SESSION['room_id'] = $room_id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Room</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #f5f5f5, #e0f7fa);
            color: #333;
            overflow: hidden;
        }
        #video-container {
            display: flex;
            flex-wrap: wrap;
            padding: 1rem;
            background: #fff;
            border-radius: 10px;
            margin: 1rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        video {
            width: 300px;
            height: auto;
            margin: 0.5rem;
            border: 2px solid #b0bec5;
            border-radius: 5px;
        }
        #chat-container {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            width: 300px;
            height: 400px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        #chat-messages {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
            border-bottom: 1px solid #ddd;
        }
        #chat-input {
            padding: 0.5rem;
            display: flex;
        }
        #chat-input input {
            flex: 1;
            padding: 0.5rem;
            border: 2px solid #b0bec5;
            border-radius: 5px;
        }
        #chat-input button {
            padding: 0.5rem 1rem;
            background: #0288d1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #controls {
            padding: 1rem;
            text-align: center;
        }
        button {
            padding: 0.7rem 1.5rem;
            margin: 0 0.5rem;
            background: #0288d1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #01579b;
        }
        @media (max-width: 600px) {
            #video-container { flex-direction: column; }
            video { width: 100%; }
            #chat-container { width: 90%; left: 5%; }
        }
    </style>
</head>
<body>
    <div id="video-container"></div>
    <div id="chat-container">
        <div id="chat-messages"></div>
        <div id="chat-input">
            <input type="text" id="chat-message" placeholder="Type a message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>
    <div id="controls">
        <button onclick="startScreenShare()">Share Screen</button>
        <button onclick="leaveMeeting()">Leave</button>
    </div>

    <script src="https://meet.jit.si/external_api.js"></script>
    <script src="https://cdn.socket.io/4.5.1/socket.io.min.js"></script>
    <script>
        const domain = 'meet.jit.si';
        const options = {
            roomName: '<?php echo $room_id; ?>',
            width: '100%',
            height: 500,
            parentNode: document.querySelector('#video-container')
        };
        const api = new JitsiMeetExternalAPI(domain, options);

        const socket = io('https://your-socket-io-server.com');
        socket.emit('joinRoom', '<?php echo $room_id; ?>');

        socket.on('chatMessage', (msg) => {
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.innerHTML += `<p>${msg}</p>`;
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });

        function sendMessage() {
            const message = document.getElementById('chat-message').value;
            socket.emit('chatMessage', '<?php echo $room_id; ?>', message);
            document.getElementById('chat-message').value = '';
        }

        function startScreenShare() {
            api.executeCommand('toggleShareScreen');
        }

        function leaveMeeting() {
            api.dispose();
            window.location.href = 'index.php';
        }
    </script>
</body>
</html>
