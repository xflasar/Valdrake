<?php
require '../../config.php';

header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] == 'PUT') {
  $user_id = authenticate($pdo);
  echo('user_id: ' . $user_id . PHP_EOL);

  foreach ($_GET as $getParam => $value) {
    echo $getParam . ' = ' . $value . PHP_EOL;
  }

  $item_id = $_GET['item_id'] ?? null;

  if($item_id == null) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing item_id']);
    exit;
  }

  $level = $_GET['level'] ?? null;

  if($level == null) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing level']);
    exit;
  }

  $stmt = $pdo->prepare('UPDATE item_levels SET item_level = :level WHERE id = :item_id AND user_id = :user_id');
  $stmt->execute([
    'level' => $level,
    'item_id' => $item_id,
    'user_id' => $user_id
  ]);

  echo json_encode(['message' => 'Item level updated successfully']);
}
?>