<?php
require '../../config.php';

header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = authenticate($pdo);

    $slot = $_POST['slot'];
    $level = $_POST['level'];

    $stmt = $pdo->prepare("INSERT INTO item_levels (user_id, slot_name, item_level) VALUES (:user_id, :slot, :level)");
    $stmt->execute([
        'user_id' => $user_id,
        'slot' => $slot,
        'level' => $level
    ]);

    echo json_encode(["message" => "Item level added successfully"]);
}
?>