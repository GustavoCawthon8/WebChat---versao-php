<?php
session_start();

if (!isset($_SESSION['token'])) {
    header("Location: index.php");
    exit;
}

const FIREBASE_DB_URL = "https://webchat-b5798-default-rtdb.firebaseio.com/";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'] ?? '';
    if ($message) {
        $db_url = FIREBASE_DB_URL . "messages.json";

        $data = [
            'username' => "User",
            'message' => $message,
            'timestamp' => time()
        ];

        sendRequest($db_url, $data);
    }
}

$messages = json_decode(file_get_contents(FIREBASE_DB_URL . "messages.json"), true) ?? [];

function sendRequest($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatWeb - Message</title>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100vh;
    background-color: #f4f6f9;
    color: #333;
}

.chat-container {
    display: flex;
    flex-direction: column;
    max-width: 600px;
    margin: 0 auto;
    height: 100%;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.chat-container h2 {
    background-color: #0078d7;
    color: #fff;
    padding: 15px;
    text-align: center;
}

.messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
}

.messages p {
    margin: 10px 0;
    padding: 10px;
    background-color: #f0f0f0;
    border-radius: 8px;
    max-width: 75%;
    word-wrap: break-word;
}

.messages p strong {
    color: #0078d7;
}

.messages p:nth-child(odd) {
    align-self: flex-start;
    background-color: #e8f5e9;
}

form {
    display: flex;
    padding: 10px;
    border-top: 1px solid #ddd;
    background-color: #f9f9f9;
}

form input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-right: 10px;
    font-size: 16px;
}

form button {
    padding: 10px 15px;
    background-color: #0078d7;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

form button:hover {
    background-color: #005bb5;
}

@media (max-width: 600px) {
    .chat-container {
        width: 100%;
        height: 100%;
    }

    .messages p {
        max-width: 90%;
    }
}
    </style>
</head>
<body id="body2">
    <div class="chat-container">
        <h2>WebChat</h2>
        <div class="messages">
            <?php foreach ($messages as $msg): ?>
                <p><strong><?= htmlspecialchars($msg['username']) ?>:</strong> <?= htmlspecialchars($msg['message']) ?></p>
            <?php endforeach; ?>
        </div>
        <form id='campo-msg'method="POST">
            <input id="campo-msg" type="text" name="message" placeholder="Digite sua mensagem..." required>
            <button type="submit">Enviar</button>
        </form>
    </div>
</body>

