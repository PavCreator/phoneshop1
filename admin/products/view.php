<?php
$title = "View Product";
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit;
}
?>

<?php require_once '../../admin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Product Details</h4>
        <p class="text-muted mb-0">View product information</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
        <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <?php if($product['image'] && file_exists('../../uploads/'.$product['image'])): ?>
                <img src="../../uploads/<?= $product['image'] ?>" class="img-fluid rounded" alt="<?= sanitize($product['product_name']) ?>">
                <?php else: ?>
                <div class="bg-light rounded d-flex align-items-center justify-content-center py-5">
                    <i class="fas fa-image fa-5x text-muted"></i>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0"><?= sanitize($product['product_name']) ?></h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <p class="text-muted mb-1">Product ID</p>
                        <span class="badge bg-primary">#<?= $product['id'] ?></span>
                    </div>
                    <div class="col-6">
                        <p class="text-muted mb-1">Status</p>
                        <span class="badge bg-<?= $product['status'] == 'active' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($product['status']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <p class="text-muted mb-1">Category</p>
                        <span class="fw-semibold"><?= sanitize($product['category_name'] ?? 'None') ?></span>
                    </div>
                    <div class="col-6">
                        <p class="text-muted mb-1">Price</p>
                        <span class="h5 text-success mb-0">$<?= number_format($product['price'], 2) ?></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="text-muted mb-1">Description</p>
                    <p><?= nl2br(sanitize($product['description'])) ?></p>
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <p class="text-muted mb-1">Created</p>
                        <small><?= date('M d, Y - h:i A', strtotime($product['created_at'])) ?></small>
                    </div>
                    <div class="col-6">
                        <p class="text-muted mb-1">Last Updated</p>
                        <small><?= date('M d, Y - h:i A', strtotime($product['updated_at'])) ?></small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit Product
            </a>
            <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this product?')">
                <i class="fas fa-trash me-2"></i>Delete
            </a>
        </div>
    </div>
</div>

<?php require_once '../../admin/footer.php'; ?>