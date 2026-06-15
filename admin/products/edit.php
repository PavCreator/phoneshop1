<?php
$title = "Edit Product";
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit;
}

// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name");
$categories = $stmt->fetchAll();

$message = '';
$upload_dir = __DIR__ . '/../../uploads/';
$image_url = '../../uploads/' . $product['image'];
$image_exists = !empty($product['image']) && file_exists($upload_dir . $product['image']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = '<div class="alert alert-danger">Invalid security token.</div>';
    } else {
        $product_name = trim($_POST['product_name']);
        $category_id = $_POST['category_id'];
        $description = trim($_POST['description']);
        $price = $_POST['price'];
        $status = $_POST['status'];
        $image = $product['image'];

        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check !== false && $_FILES['image']['size'] <= 5000000 
                && in_array($imageFileType, ['jpg', 'jpeg', 'png', 'webp'])) {
                
                // Create folder if not exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Delete old image
                if (!empty($product['image']) && file_exists($upload_dir . $product['image'])) {
                    unlink($upload_dir . $product['image']);
                }
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image = $file_name;
                    $image_url = '../../uploads/' . $image;
                }
            }
        }

        // Update product
        $stmt = $pdo->prepare("UPDATE products SET product_name = ?, category_id = ?, description = ?, price = ?, image = ?, status = ? WHERE id = ?");
        $stmt->execute([$product_name, $category_id, $description, $price, $image, $status, $id]);

        header("Location: index.php");
        exit;
    }
}
?>

<?php require_once '../../admin/layout.php'; ?>

<div class="p-4">
    <h2 class="mb-4">Edit Product</h2>
    <?= $message ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Product Name *</label>
                            <input type="text" name="product_name" class="form-control" value="<?= sanitize($product['product_name']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                    <?= sanitize($cat['category_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= sanitize($product['description']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Price *</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= $product['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $product['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <div class="mb-2">
                        <?php if($image_exists): ?>
                        <img src="<?= $image_url ?>" class="rounded" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                        <div class="bg-light d-inline-block p-4 rounded"><i class="fas fa-image fa-2x text-muted"></i></div>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Leave empty to keep current image</small>
                </div>

                <?= csrf_token() ?>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Product</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../admin/footer.php'; ?>