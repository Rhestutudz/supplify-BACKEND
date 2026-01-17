<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once "../config/database.php";

$stmt = $pdo->query("SELECT id, name FROM categories");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
  "status" => true,
  "data" => $data
]);
