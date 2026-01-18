<?php
header("Content-Type: application/json");
require_once "../config/database.php";

$stmt = $pdo->query("
  SELECT 
    COUNT(DISTINCT o.id) AS total_orders,
    IFNULL(SUM(oi.qty * oi.price), 0) AS omzet
  FROM orders o
  LEFT JOIN order_items oi ON oi.order_id = o.id
");

$data = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
  "status" => true,
  "data" => $data
]);
