<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "../config/database.php";

$categoryId = $_GET['category_id'] ?? null;

/*
|--------------------------------------------------------------------------
| BASE URL FOTO (FIX 127.0.0.1)
|--------------------------------------------------------------------------
*/
$basePhotoUrl = "http://localhost/UAS_MOBILE/upload/products/";

if ($categoryId) {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.category_id = ?
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$categoryId]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC
    ");
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| TAMBAHKAN URL FOTO LENGKAP
|--------------------------------------------------------------------------
*/
foreach ($products as &$product) {
    $product['photo_url'] = !empty($product['photo'])
        ? $basePhotoUrl . $product['photo']
        : null;

    unset($product['photo']); // optional
}

echo json_encode([
    "status" => true,
    "data" => $products
]);
