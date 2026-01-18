<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// PREFLIGHT
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../config/database.php";

// ================= AMBIL DATA =================
$category_id = $_POST['category_id'] ?? null;
$name        = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$price       = $_POST['price'] ?? null;
$stock       = $_POST['stock'] ?? null;

// ================= VALIDASI =================
if (!$category_id || $name === '' || $price === null || $stock === null) {
    echo json_encode([
        "status" => false,
        "message" => "Data produk tidak lengkap"
    ]);
    exit;
}

// ================= UPLOAD FOTO =================
$photoPath = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = time() . '_' . uniqid() . '.' . $ext;

    $uploadDir = "../upload/products/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        $uploadDir . $fileName
    );

    // PATH YANG DISIMPAN KE DATABASE
    $photoPath = "upload/products/" . $fileName;
}

// ================= INSERT DB =================
$stmt = $pdo->prepare("
  INSERT INTO products 
  (category_id, name, description, price, stock, photo)
  VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
  $category_id,
  $name,
  $description,
  $price,
  $stock,
  $photoPath
]);

echo json_encode([
  "status" => true,
  "message" => "Produk berhasil ditambahkan"
]);
