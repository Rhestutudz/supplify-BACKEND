<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once "../config/database.php";

$category_id = $_GET['category_id'] ?? null;

if (!$category_id) {
  echo json_encode([
    "status" => false,
    "message" => "category_id wajib"
  ]);
  exit;
}

$stmt = $pdo->prepare("
  SELECT p.*, c.name AS category_name
  FROM products p
  JOIN categories c ON p.category_id = c.id
  WHERE p.category_id = ?
  ORDER BY p.created_at DESC
");

$stmt->execute([$category_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
  "status" => true,
  "data" => $products
]);
