<?php 
$title = "Add Product";
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../../auth/login.php");
    exit;
}

$message = '';

$stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = trim($_POST['product_name']);
    $category_id = $_POST['category_id'];
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $status = $_POST['status'];
    
    $image = '';
    $upload_dir = __DIR__ . '/../../uploads/';
    
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false && $_FILES['image']['size'] <= 5000000 
            && in_array($imageFileType, ['jpg', 'jpeg', 'png', 'webp'])) {
            move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
            $image = $file_name;
        }
    }
    
    if ($image) {
        $stmt = $pdo->prepare("INSERT INTO products (product_name, category_id, description, price, image, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$product_name, $category_id, $description, $price, $image, $status]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (product_name, category_id, description, price, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$product_name, $category_id, $description, $price, $status]);
    }
    
    header("Location: index.php");
    exit;
}
?>

<?php require_once '../../admin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Add New Product</h4>
        <p class="text-muted mb-0">Fill in the product details</p>
    </div>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Product Name</label>
                                <input type="text" name="product_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= sanitize($cat['category_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="5" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Price ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">JPG, PNG or WebP (Max 5MB)</small>
                    </div>
                    
                    <?= csrf_token() ?>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Product
                        </button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-info-circle me-2"></i>Tips
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li class="mb-2">Product name should be clear</li>
                    <li class="mb-2">Add detailed description</li>
                    <li class="mb-2">Upload clear product images</li>
                    <li>Set appropriate price</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../admin/footer.php'; ?>