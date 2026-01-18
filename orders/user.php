<?php
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "../config/database.php";

// ================= VALIDASI USER ID =================
$user_id = $_GET['user_id'] ?? null;

if (!$user_id || !is_numeric($user_id)) {
    echo json_encode([
        "status" => false,
        "message" => "User ID required"
    ]);
    exit;
}

// ================= AMBIL ORDERS + TOTAL =================
$stmt = $pdo->prepare(
    "SELECT 
        o.id,
        o.user_id,
        o.status,
        IFNULL(SUM(oi.qty * oi.price), 0) AS total,
        o.created_at
     FROM orders o
     LEFT JOIN order_items oi ON oi.order_id = o.id
     WHERE o.user_id = ?
     GROUP BY o.id
     ORDER BY o.created_at DESC"
);

$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================= JIKA TIDAK ADA ORDER =================
if (!$orders) {
    echo json_encode([
        "status" => true,
        "data" => []
    ]);
    exit;
}

// ================= AMBIL ITEMS PER ORDER =================
$itemStmt = $pdo->prepare(
    "SELECT 
        oi.id,
        oi.order_id,
        oi.product_id,
        p.name AS product_name,
        oi.qty,
        oi.price
     FROM order_items oi
     JOIN products p ON p.id = oi.product_id
     WHERE oi.order_id = ?"
);

$result = [];

foreach ($orders as $order) {
    $itemStmt->execute([$order['id']]);
    $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    $result[] = [
        "id" => (int)$order['id'],
        "user_id" => (int)$order['user_id'],
        "status" => $order['status'],
        "total" => (float)$order['total'],
        "created_at" => $order['created_at'],
        "items" => $items
    ];
}

// ================= RESPONSE =================
echo json_encode([
    "status" => true,
    "data" => $result
]);
