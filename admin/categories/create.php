<?php 
$title = "Add Category";
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../../auth/login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = trim($_POST['category_name']);
    $description = trim($_POST['description']);

    $stmt = $pdo->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
    $stmt->execute([$category_name, $description]);
    
    header("Location: index.php");
    exit;
}
?>

<?php require_once '../../admin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Add New Category</h4>
        <p class="text-muted mb-0">Create a new product category</p>
    </div>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <?= $message ?? '' ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name *</label>
                        <input type="text" name="category_name" class="form-control" placeholder="Enter category name" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Describe this category"></textarea>
                    </div>
                    
                    <?= csrf_token() ?>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Category
                        </button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <i class="fas fa-lightbulb me-2"></i>Tips
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li class="mb-2">Use clear category names</li>
                    <li class="mb-2">e.g., "Electronics"</li>
                    <li class="mb-2">Keep descriptions brief</li>
                    <li>Add products later</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../admin/footer.php'; ?>