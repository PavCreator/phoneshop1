<?php 
$title = "Categories";
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../../auth/login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();
?>

<?php require_once '../../admin/layout.php'; ?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 style="font-weight: 600;">Categories Management</h5>
        <p class="text-muted mb-0" style="font-size: 14px;">Manage your categories here</p>
    </div>
    <a href="create.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Category
    </a>
</div>

<!-- Categories Grid -->
<div class="row g-4">
    <?php foreach ($categories as $cat): ?>
    <div class="col-md-4">
        <div class="card-clean" style="padding: 20px;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        #<?= $cat['id'] ?>
                    </span>
                    <h5 class="mt-3 mb-2" style="font-weight: 600;"><?= sanitize($cat['category_name']) ?></h5>
                    <p class="text-muted mb-0" style="font-size: 14px;"><?= sanitize(substr($cat['description'], 0, 80)) ?>...</p>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm" data-bs-toggle="dropdown" style="background: #f5f5f5; border-radius: 8px;">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="edit.php?id=<?= $cat['id'] ?>"><i class="fas fa-edit me-2"></i>Edit</a></li>
                        <li><a class="dropdown-item text-danger" href="delete.php?id=<?= $cat['id'] ?>" onclick="return confirm('Delete?')"><i class="fas fa-trash me-2"></i>Delete</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-3 pt-3" style="border-top: 1px solid #eee;">
                <small class="text-muted"><i class="fas fa-calendar me-1"></i>Created: <?= date('M d, Y', strtotime($cat['created_at'])) ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once '../../admin/footer.php'; ?>