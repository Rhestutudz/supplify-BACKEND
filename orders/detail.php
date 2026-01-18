<?php
header("Content-Type: application/json");
require_once "../config/database.php";

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
  echo json_encode(["status" => false, "message" => "Order ID required"]);
  exit;
}

$stmt = $pdo->prepare("
  SELECT 
    oi.id,
    p.name AS product_name,
    oi.qty,
    oi.price
  FROM order_items oi
  JOIN products p ON p.id = oi.product_id
  WHERE oi.order_id = ?
");

$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
  "status" => true,
  "data" => $items
]);
