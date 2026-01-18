<?php
header("Content-Type: application/json");
require_once "../config/database.php";

// Ambil body JSON dari Flutter
$data = json_decode(file_get_contents("php://input"), true);

// Validasi
if (!isset($data['order_id']) || !isset($data['status'])) {
    echo json_encode([
        "status" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

$orderId = $data['order_id'];
$status  = $data['status'];

try {
    $stmt = $pdo->prepare("
        UPDATE orders 
        SET status = ? 
        WHERE id = ?
    ");
    $stmt->execute([$status, $orderId]);

    echo json_encode([
        "status" => true,
        "message" => "Status order berhasil diubah"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => false,
        "message" => $e->getMessage()
    ]);
}
