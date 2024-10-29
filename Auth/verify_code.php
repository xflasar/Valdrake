<?php
require './config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $code = $_POST['code'];

  $stmt = $pdo->prepare("SELECT * FROM email_verifications WHERE email = :email AND verification_code = :code AND created_at >= NOW() - INTERVAL '10 minutes'");
  $stmt->execute([
    'email' => $email,
    'code' => $code
  ]);

  if($stmt->rowCount() > 0) {
    $pdo->prepare("DELETE FROM email_verifications WHERE email = :email")->execute(['email' => $email]);

    echo "Code verified successfully. You can now proceed to set your password.";
  } else {
    echo "Invalid or expired verification code";
  }
}