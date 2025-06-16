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
    <title>Ahmad's Google Meet - Meeting Room</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #f5f5f5, #e0f7fa);
            color: #202124;
            overflow: hidden;
        }
        .header {
            position: absolute;
            top: 1rem;
            left: 1rem;
            font-size: 1.2rem;
            color: #1a73e8;
            font-weight: 500;
        }
        #video-container {
            display: flex;
            flex-wrap: wrap;
            padding: 1rem;
            background: #fff;
            border-radius: 10px;
            margin: 3rem 1rem 1rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        video {
            width: 300px;
            height: auto;
            margin: 0.5rem;
            border: 2px solid #dadce0;
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
            font-size: 0.9rem;
        }
        #chat-input {
            padding: 0.5rem;
            display: flex;
        }
        #chat-input input {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid #dadce0;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        #chat-input button {
            padding: 0.5rem 1rem;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 0.5rem;
        }
        #chat-input button:hover {
            background: #1557b0;
        }
        #controls {
            padding: 1rem;
            text-align: center;
            background: #fff;
            border-radius: 10px;
            margin: 0 1rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        button {
            padding: 0.8rem 1.5rem;
            margin: 0 0.5rem;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
        }
        button:hover {
            background: #1557b0;
        }
        .leave-btn {
            background: #ea4335;
        }
        .leave-btn:hover {
            background: #c5221f;
        }
        #background-selector {
            position: fixed;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            background: #fff;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        #background-selector select {
            padding: 0.5rem;
            width: 200px;
            border: 1px solid #dadce0;
            border-radius: 4px;
        }
        #background-selector button {
            padding: 0.5rem 1rem;
            margin-top: 0.5rem;
        }
        @media (max-width: 600px) {
            #video-container { flex-direction: column; margin-top: 2rem; }
            video { width: 100%; }
            #chat-container { width: 90%; left: 5%; bottom: 1rem; }
            #controls { margin: 0 0.5rem; }
            #background-selector { left: 0.5rem; width: 90%; }
        }
    </style>
</head>
<body>
    <div class="header">Ahmad's Google Meet</div>
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
        <button class="leave-btn" onclick="leaveMeeting()">Leave</button>
    </div>
    <div id="background-selector">
        <select id="background-options">
            <option value="none">No Background</option>
            <option value="https://images.unsplash.com/photo-1501785888041-af3ef285b470">Nature</option>
            <option value="https://images.unsplash.com/photo-1519681393784-d120267933ba">City</option>
            <option value="https://images.unsplash.com/photo-1472214103451-9374bd1c798e">Beach</option>
        </select>
        <button onclick="applyBackground()">Apply Background</button>
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
            if (message) {
                socket.emit('chatMessage', '<?php echo $room_id; ?>', message);
                document.getElementById('chat-message').value = '';
            }
        }

        function startScreenShare() {
            api.executeCommand('toggleShareScreen');
        }

        function leaveMeeting() {
            api.dispose();
            window.location.href = 'index.php';
        }

        function applyBackground() {
            const selectedBackground = document.getElementById('background-options').value;
            if (selectedBackground === 'none') {
                api.executeCommand('setVirtualBackground', { enabled: false });
            } else {
                api.executeCommand('setVirtualBackground', {
                    enabled: true,
                    backgroundType: 'image',
                    backgroundImageUrl: selectedBackground
                });
            }
        }

        // Ensure Jitsi Meet API is fully loaded before enabling controls
        api.addEventListener('videoConferenceJoined', () => {
            console.log('Conference joined, controls are active');
        });
    </script>
</body>
</html>
