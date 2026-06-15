<?php 
$title = "Products";
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../../auth/login.php");
    exit;
}

$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE product_name LIKE ?");
$total_stmt->execute(["%$search%"]);
$total = $total_stmt->fetchColumn();
$total_pages = ceil($total / $limit);

$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.product_name LIKE ? 
    ORDER BY p.id DESC LIMIT $limit OFFSET $offset");
$stmt->execute(["%$search%"]);
$products = $stmt->fetchAll();
?>

<?php require_once '../../admin/layout.php'; ?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 style="font-weight: 600;">Products Management</h5>
        <p class="text-muted mb-0" style="font-size: 14px;">Manage your products here</p>
    </div>
    <a href="create.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Product
    </a>
</div>

<!-- Search -->
<div class="card-clean mb-4" style="padding: 20px;">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= sanitize($search) ?>" style="border-radius: 8px;">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i>
        </button>
        <?php if($search): ?>
        <a href="index.php" class="btn btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Table -->
<div class="card-clean">
    <div class="table-responsive">
        <table class="table-custom table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td style="font-weight: 600;">#<?= $p['id'] ?></td>
                    <td>
                        <?php if($p['image'] && file_exists('../../uploads/'.$p['image'])): ?>
                        <img src="../../uploads/<?= $p['image'] ?>" style="width: 45px; height: 45px; object-fit: cover; border-radius: 8px;">
                        <?php else: ?>
                        <div style="width: 45px; height: 45px; background: #f5f5f5; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 500;"><?= sanitize($p['product_name']) ?></td>
                    <td><span class="badge-clean" style="background: #f5f5f5; padding: 5px 12px; border-radius: 20px; font-size: 12px;"><?= sanitize($p['category_name'] ?? 'None') ?></span></td>
                    <td style="font-weight: 600;">$<?= number_format($p['price'], 2) ?></td>
                    <td>
                        <span class="badge-clean" style="background: <?= $p['status'] == 'active' ? '#d4edda' : '#e9ecef' ?>; padding: 5px 12px; border-radius: 20px; font-size: 12px;">
                            <?= ucfirst($p['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="view.php?id=<?= $p['id'] ?>" style="color: #667eea; margin-right: 10px;"><i class="fas fa-eye"></i></a>
                        <a href="edit.php?id=<?= $p['id'] ?>" style="color: #f6c23e; margin-right: 10px;"><i class="fas fa-edit"></i></a>
                        <a href="delete.php?id=<?= $p['id'] ?>" style="color: #e74c3c;" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if($total_pages > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for($i=1; $i<=$total_pages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require_once '../../admin/footer.php'; ?>