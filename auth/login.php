<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode([
        "status" => false,
        "message" => "Email dan password wajib diisi"
    ]);
    exit;
}

// cari user
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        "status" => false,
        "message" => "Email tidak terdaftar"
    ]);
    exit;
}

// cek password
if (!password_verify($password, $user['password'])) {
    echo json_encode([
        "status" => false,
        "message" => "Password salah"
    ]);
    exit;
}

// cek status approval
if ($user['status'] !== 'approved') {
    echo json_encode([
        "status" => false,
        "message" => "Akun belum disetujui admin"
    ]);
    exit;
}

// sukses login
echo json_encode([
    "status" => true,
    "message" => "Login berhasil",
    "data" => [
        "id"    => $user['id'],
        "name"  => $user['name'],
        "email" => $user['email'],
        "role"  => $user['role'],
    ]
]);
