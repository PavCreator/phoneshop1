<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

if (!$id || !is_numeric($id)) {
    echo "<script>alert('Invalid ID'); window.location='index.php';</script>";
    exit;
}

// Check if category has products
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
$stmt->execute([$id]);
$count = $stmt->fetchColumn();

if ($count > 0) {
    echo "<script>alert('Cannot delete! This category has $count product(s). Please delete or move products first.'); window.location='index.php';</script>";
    exit;
}

// Delete category
$stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
$deleted = $stmt->execute([$id]);

if ($deleted) {
    header("Location: index.php");
    exit;
} else {
    echo "<script>alert('Error deleting category'); window.location='index.php';</script>";
    exit;
}
?>