<?php
session_start();

// Deus acima de tudo :)
const FIREBASE_DB_URL = "https://webchat-b5798-default-rtdb.firebaseio.com/";
const FIREBASE_API_KEY = "AIzaSyBvitMgBlY9RCzlEnBRoYYLcxzQ_07yjNM";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? '';
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    if (isset($_POST["register"])) {
        
        $register_url = "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=" . FIREBASE_API_KEY;

        $data = [
            "email" => $email,
            "password" => $password,
            "returnSecureToken" => true
        ];

        $response = json_decode(sendRequest($register_url, $data), true);

        if (isset($response['idToken'])) {
            
            $db_url = FIREBASE_DB_URL . "users.json";
            $db_data = [
                "email" => $email,
                "username" => $username
            ];
            sendRequest($db_url, $db_data);

            $_SESSION["token"] = $response["idToken"];
            header("Location: chat.php");
            exit;
        } else {
            $error = $response["error"]["message"] ?? "Erro desconhecido";
        }
    }

    if (isset($_POST["login"])) {
    
        $login_url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=" . FIREBASE_API_KEY;

        $data = [
            "email" => $email,
            "password" => $password,
            "returnSecureToken" => true
        ];

        $response = json_decode(sendRequest($login_url, $data), true);

        if (isset($response["idToken"])) {
            $_SESSION["token"] = $response["idToken"];
            header("Location: chat.php");
            exit;
        } else {
            $error = $response["error"]["message"] ?? "Erro desconhecido";
        }
    }
}

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
    <title>WebChat - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Login ou Registro</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="username" placeholder="Nome de Usuário" required>
            <input type="password" name="password" placeholder="Senha" required>
            <button type="submit" name="register">Registrar</button>
            <button type="submit" name="login">Login</button>
        </form>
        <p><?= htmlspecialchars($error) ?></p>
    </div>
</body>
</html>
