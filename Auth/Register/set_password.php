<?php
require './config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $pdo->prepare("UPDATE users SET password = :password, is_verified = true WHERE email = :email");
  $stmt->execute([
    'password' => $password,
    'email' => $email
  ]);

  echo "Account created successfully.";
}
?>