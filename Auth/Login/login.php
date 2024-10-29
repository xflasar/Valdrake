<?php
require '../../config.php';

header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
      echo json_encode(['error' => "Username and email are required"]);
      exit;
    }

    if($_POST['username'] == '' || $_POST['password'] == ''){
      echo json_encode(['error' => "Username and email are required 2", $_POST['username'] , $_POST['password']]);
      exit;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user exists and is verified
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = :username and is_verified = true");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);

    if($user && password_verify($password, $user['password'])) {
        // Generate token
        $token = bin2hex(random_bytes(32));

        $stmt = $pdo->prepare("DELETE FROM sessions WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user['id']]);

        // Store token in database
        $stmt = $pdo->prepare("INSERT INTO sessions (user_id, token) VALUES (:user_id, :token)");
        $stmt->execute([
          'user_id' => $user['id'],
          'token' => $token
        ]);

        // Return token
        echo json_encode(["message" => "Login succesful", "token" => $token]);
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Invalid credentials"]);
    }
}
?>