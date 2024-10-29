<?php

// Load env variables from .env file
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$dotenv = Dotenv::createImmutable(__DIR__);

$dotenv->load();

define("DEBUG",true);


// Connect to the database
  $host = $_ENV['DB_HOST'];
  $port = $_ENV['DB_PORT'];
  $dbname = $_ENV['DB_DATABASE'];
  $user = $_ENV['DB_USERNAME'];
  $password = $_ENV['DB_PASSWORD'];

  try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    // echo "Connected to the database successfully!";
  } catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
  }

  function send_verification_email($email, $code) {
      //Create a new PHPMailer instance
    $mail = new PHPMailer;
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    $mail->Debugoutput = 'html';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->SMTPOptions = array(
      'ssl' => [
          'verify_peer' => false,
          'verify_depth' => 0,
          'allow_self_signed' => true
      ],
    );
    //Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = $_ENV['EMAIL_USERNAME'];
    //Password to use for SMTP authentication
    $mail->Password = $_ENV['EMAIL_PASSWORD'];
    //Set who the message is to be sent from
    $mail->setFrom('xflasar@gmail.com', 'Valdrake');
    //Set who the message is to be sent to
    $mail->addAddress($email);
    
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';

    //Set the subject line
    $mail->Subject = 'Email Verification';
    $mail->Body = "Your verification code is: <strong>$code</strong>";
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!";
    }


    return true;
  };

  function verify_session_token($pdo, $token) {
    $stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE token = :token");
    $stmt->execute(['token' => $token]);
    return $stmt->fetchColumn() ? true : false;
  };

  function authenticate($pdo) {
    $headers = getallheaders();

    if(!isset($headers['Authorization'])) {
      http_response_code(401);
      echo json_encode(['error' => 'Unauthorized']);
      exit();
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    $stmt = $pdo->prepare("SELECT user_id FROM sessions WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $user_id = $stmt->fetchColumn();

    if(!$user_id) {
      http_response_code(401);
      echo json_encode(['error' => 'Invalid session token']);
      exit();
    }

    return $user_id;
  };
?>