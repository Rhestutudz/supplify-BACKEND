<?php
// ================== CORS ==================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ================== DB ==================
require_once "../config/database.php";

// ================== METHOD ==================
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // ================== GET ==================
    case 'GET':
        $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => true,
            "data" => $data
        ]);
        break;

    // ================== POST ==================
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        $name = trim($input['name'] ?? '');

        if (!$name) {
            echo json_encode([
                "status" => false,
                "message" => "Nama kategori wajib diisi"
            ]);
            exit;
        }

        $stmt = $pdo->prepare(
            "INSERT INTO categories (name) VALUES (?)"
        );
        $stmt->execute([$name]);

        echo json_encode([
            "status" => true,
            "message" => "Kategori berhasil ditambahkan"
        ]);
        break;

    // ================== PUT ==================
    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        $id   = $input['id'] ?? null;
        $name = trim($input['name'] ?? '');

        if (!$id || !$name) {
            echo json_encode([
                "status" => false,
                "message" => "ID dan nama kategori wajib diisi"
            ]);
            exit;
        }

        $stmt = $pdo->prepare(
            "UPDATE categories SET name = ? WHERE id = ?"
        );
        $stmt->execute([$name, $id]);

        echo json_encode([
            "status" => true,
            "message" => "Kategori berhasil diupdate"
        ]);
        break;

    // ================== DELETE ==================
    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);
        $id = $input['id'] ?? null;

        if (!$id) {
            echo json_encode([
                "status" => false,
                "message" => "ID kategori wajib diisi"
            ]);
            exit;
        }

        $stmt = $pdo->prepare(
            "DELETE FROM categories WHERE id = ?"
        );
        $stmt->execute([$id]);

        echo json_encode([
            "status" => true,
            "message" => "Kategori berhasil dihapus"
        ]);
        break;

    // ================== DEFAULT ==================
    default:
        echo json_encode([
            "status" => false,
            "message" => "Method tidak diizinkan"
        ]);
        break;
}
