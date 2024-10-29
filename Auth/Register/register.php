<?php
require '../../config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (!isset($_POST['username']) || !isset($_POST['email'])) {
    echo json_encode(['error' => 'Username and email are required']);
    exit;
  }

  if($_POST['username'] == '' || $_POST['email'] == ''){
    echo json_encode(['error' => `Username and email are required 2 `, $_POST['username'] , $_POST['email']]);
    exit;
  }

  $username = $_POST['username'];
  $email = $_POST['email'];

  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
  $stmt->execute([
    'email' => $email,
    'username' => $username
  ]);

  if($stmt->rowCount() > 0){
    echo json_encode(['error' => 'Username or email already exists']);
    exit;
  }

  $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
  
  $stmt = $pdo->prepare("INSERT INTO email_verifications (email, verification_code) VALUES (:email, :code) ON CONFLICT (email) DO UPDATE SET verification_code = :code, created_at = NOW()");
  
  $stmt->execute([
    'email' => $email,
    'code' => $code
  ]);

  if(send_verification_email($email, $code)) {
    echo json_encode(['success' => 'Verification email sent']);
  } else {
    echo json_encode(['error' => 'Failed to send verification email']);
  }
}
?>