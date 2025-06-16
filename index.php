<?php
session_start();
include 'db.php';

if (isset($_POST['create_meeting'])) {
    $room_id = uniqid();
    $_SESSION['room_id'] = $room_id;
    echo "<script>window.location.href = 'meeting.php?room=" . $room_id . "';</script>";
}

if (isset($_POST['join_meeting'])) {
    $room_id = $_POST['room_id'];
    $_SESSION['room_id'] = $room_id;
    echo "<script>window.location.href = 'meeting.php?room=" . $room_id . "';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Meet Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%;
            max-width: 500px;
        }
        h1 {
            color: #0288d1;
            margin-bottom: 1.5rem;
        }
        .input-group {
            margin: 1rem 0;
        }
        input[type="text"] {
            padding: 0.5rem;
            width: 70%;
            border: 2px solid #b0bec5;
            border-radius: 5px;
            font-size: 1rem;
        }
        button {
            padding: 0.7rem 1.5rem;
            background: #0288d1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }
        button:hover {
            background: #01579b;
        }
        @media (max-width: 600px) {
            .container { padding: 1rem; }
            input[type="text"] { width: 60%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Google Meet Clone</h1>
        <form method="post">
            <div class="input-group">
                <button type="submit" name="create_meeting">Create Meeting</button>
            </div>
        </form>
        <form method="post">
            <div class="input-group">
                <input type="text" name="room_id" placeholder="Enter Room ID" required>
                <button type="submit" name="join_meeting">Join Meeting</button>
            </div>
        </form>
    </div>
</body>
</html>
