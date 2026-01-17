<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode([
        "status" => false,
        "message" => "ID tidak valid"
    ]);
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE users SET status = 'approved' WHERE id = ?"
);
$stmt->execute([$id]);

echo json_encode([
    "status" => true,
    "message" => "Client berhasil di-approve"
]);
