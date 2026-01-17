<?php
header("Content-Type: application/json");

// ===== LOAD DATABASE (PDO) =====
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Product.php';

// ===== SAFE PATH INFO =====
$method = $_SERVER['REQUEST_METHOD'];
$pathInfo = $_SERVER['PATH_INFO'] ?? '';
$request = explode('/', trim($pathInfo, '/'));
$resource = $request[0] ?? '';

// ===== DEFAULT RESPONSE =====
if ($resource === '') {
    echo json_encode([
        "status" => true,
        "message" => "API is running"
    ]);
    exit;
}

// ================= CATEGORIES =================
if ($resource === 'categories') {
    $category = new Category($pdo);

    if ($method === 'GET') {
        $stmt = $category->read();

        echo json_encode([
            "status" => true,
            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    }
    exit;
}

// ================= PRODUCTS =================
if ($resource === 'products') {
    $product = new Product($pdo);

    if ($method === 'GET') {
        if (isset($_GET['category_id'])) {
            $stmt = $product->readByCategory($_GET['category_id']);
        } else {
            $stmt = $product->read();
        }

        $products = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = [
                "id" => (int)$row['id'],
                "category_id" => (int)$row['category_id'], // ✅ AMAN
                "category_name" => $row['category_name'],
                "name" => $row['name'],
                "description" => $row['description'],
                "price" => (int)$row['price'],
                "stock" => (int)$row['stock'],
                "photo" => $row['photo'],
                "created_at" => $row['created_at']
            ];
        }

        echo json_encode([
            "status" => true,
            "data" => $products
        ]);
    }
    exit;
}

// ===== NOT FOUND =====
http_response_code(404);
echo json_encode([
    "status" => false,
    "message" => "Endpoint not found"
]);
