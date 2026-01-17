<?php

require_once "../config/database.php";
require_once "../models/Product.php";
require_once "../core/Response.php";

class ProductController {

    public static function create() {

        // ===== DATA FORM =====
        $category_id = $_POST['category_id'] ?? null;
        $name        = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price       = $_POST['price'] ?? 0;
        $stock       = $_POST['stock'] ?? 0;

        if (!$category_id || !$name || !$price || !$stock) {
            Response::json(false, "Data produk tidak lengkap");
            return;
        }

        // ===== UPLOAD FOTO =====
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

        // ===== SIMPAN DB =====
        Product::create([
            'category_id' => $category_id,
            'name'        => $name,
            'description' => $description,
            'price'       => $price,
            'stock'       => $stock,
            'image'       => $imageName
        ]);

        Response::json(true, "Produk berhasil ditambahkan");
    }

    // ===== GET PRODUK UNTUK CLIENT =====
    public static function get() {
        $products = Product::getAll();

        foreach ($products as &$p) {
            $p['image_url'] = $p['image']
                ? "http://localhost/UAS_MOBILE/upload/products/" . $p['image']
                : null;
        }

        Response::json(true, "OK", $products);
    }
}
