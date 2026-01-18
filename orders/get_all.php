<?php
header("Content-Type: application/json");
require_once "../config/database.php";

$stmt = $pdo->prepare("
  SELECT 
    o.id,
    o.user_id,
    o.status,
    IFNULL(SUM(oi.qty * oi.price), 0) AS total,
    o.created_at,
    u.name AS user_name
  FROM orders o
  LEFT JOIN order_items oi ON oi.order_id = o.id
  LEFT JOIN users u ON u.id = o.user_id
  GROUP BY o.id
  ORDER BY o.id DESC
");

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
  "status" => true,
  "data" => $data
]);
