<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "../config/database.php";

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
  echo json_encode([
    "status" => false,
    "message" => "User ID required"
  ]);
  exit;
}

$stmt = $pdo->prepare(
  "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC"
);
$stmt->execute([$user_id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
  "status" => true,
  "data" => $data
]);
