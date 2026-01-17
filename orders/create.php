<?php
// ===== CORS (WAJIB DI PALING ATAS) =====
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// ===== HANDLE PREFLIGHT =====
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ===== DB =====
require_once __DIR__ . '/../config/database.php';

// ===== READ BODY =====
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode([
        "status" => false,
        "message" => "Invalid JSON body",
        "raw" => $raw
    ]);
    exit;
}

// ===== VALIDASI =====
if (
    !isset($data['user_id']) ||
    !isset($data['items']) ||
    !is_array($data['items']) ||
    count($data['items']) === 0
) {
    echo json_encode([
        "status" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

$user_id = (int)$data['user_id'];
$items = $data['items'];

// ===== HITUNG TOTAL =====
$total = 0;
foreach ($items as $item) {
    $total += ((int)$item['qty']) * ((int)$item['price']);
}

try {
    $pdo->beginTransaction();

    // INSERT ORDERS
    $stmt = $pdo->prepare(
        "INSERT INTO orders (user_id, status, total_price)
         VALUES (?, 'pending', ?)"
    );
    $stmt->execute([$user_id, $total]);

    $order_id = $pdo->lastInsertId();

    // INSERT ORDER ITEMS
    $stmtItem = $pdo->prepare(
        "INSERT INTO order_items (order_id, product_id, qty, price)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($items as $item) {
        $stmtItem->execute([
            $order_id,
            $item['product_id'],
            $item['qty'],
            $item['price']
        ]);
    }

    $pdo->commit();

    echo json_encode([
        "status" => true,
        "message" => "Checkout berhasil",
        "order_id" => $order_id
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => $e->getMessage()
    ]);
}
