<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

if (!$id) {
    die("Invalid ID");
}

// Get product image to delete file
$stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($product && $product['image']) {
    $upload_dir = __DIR__ . '/../../uploads/';
    if (file_exists($upload_dir . $product['image'])) {
        unlink($upload_dir . $product['image']);
    }
}

// Delete product
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>