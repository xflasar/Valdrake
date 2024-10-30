<?php
require '../../config.php';

header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_id = authenticate($pdo);

    $stmt = $pdo->prepare("SELECT * FROM item_levels WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $item_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($item_levels);
}
?>