<?php
require '../../config.php';

header("Content-type: application/json");

if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $user_id = authenticate($pdo);
    
    foreach ($_GET as $getParam => $value) {
      echo $getParam . ' = ' . $value . PHP_EOL;
    }
    $item_id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM item_levels WHERE id = :item_id AND user_id = :user_id");
    $stmt->execute([
      'item_id' => $item_id,
      'user_id' => $user_id
    ]);

    echo json_encode(["message" => "Item level deleted successfully"]);
}
?>