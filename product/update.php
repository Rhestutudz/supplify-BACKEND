<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// HANDLE PREFLIGHT
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

// Validasi
$id          = $data['id'] ?? null;
$category_id = $data['category_id'] ?? null;
$name        = $data['name'] ?? '';
$description = $data['description'] ?? '';
$price       = $data['price'] ?? 0;
$stock       = $data['stock'] ?? 0;

if (!$id || !$category_id || !$name || !$price || !$stock) {
    echo json_encode([
        "status" => false,
        "message" => "Data produk tidak lengkap"
    ]);
    exit;
}

// Update
$stmt = $pdo->prepare("
  UPDATE products
  SET category_id = ?, name = ?, description = ?, price = ?, stock = ?
  WHERE id = ?
");

$stmt->execute([
  $category_id,
  $name,
  $description,
  $price,
  $stock,
  $id,
]);

echo json_encode([
  "status" => true,
  "message" => "Produk berhasil diperbarui"
]);
