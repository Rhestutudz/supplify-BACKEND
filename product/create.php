<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// HANDLE PREFLIGHT (WAJIB UNTUK FLUTTER WEB)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../config/database.php";

// ================= AMBIL DATA FORM =================
$category_id = $_POST['category_id'] ?? null;
$name        = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$price       = $_POST['price'] ?? 0;
$stock       = $_POST['stock'] ?? 0;

// ================= VALIDASI =================
if (!$category_id || !$name || !$price || !$stock) {
    echo json_encode([
        "status" => false,
        "message" => "Data produk tidak lengkap"
    ]);
    exit;
}

// ================= HANDLE UPLOAD FOTO =================
$imageName = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $imageName = time() . '_' . uniqid() . '.' . $ext;

    $uploadDir = "../upload/products/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        $uploadDir . $imageName
    );
}

// ================= INSERT DATABASE =================
$stmt = $pdo->prepare("
  INSERT INTO products 
  (category_id, name, description, price, stock, image)
  VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
  $category_id,
  $name,
  $description,
  $price,
  $stock,
  $imageName
]);

echo json_encode([
  "status" => true,
  "message" => "Produk berhasil ditambahkan"
]);
