<?php
$title = "Dashboard";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../auth/login.php");
    exit;
}

// Get stats
$products_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$categories_count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$active_products = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
?>

<?php require_once 'layout.php'; ?>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1" style="font-size: 12px;">TOTAL PRODUCTS</p>
                    <h4 class="mb-0 fw-bold"><?= $products_count ?></h4>
                </div>
                <div style="background: #667eea; color: #fff; padding: 12px; border-radius: 10px;">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1" style="font-size: 12px;">ACTIVE</p>
                    <h4 class="mb-0 fw-bold"><?= $active_products ?></h4>
                </div>
                <div style="background: #28a745; color: #fff; padding: 12px; border-radius: 10px;">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1" style="font-size: 12px;">CATEGORIES</p>
                    <h4 class="mb-0 fw-bold"><?= $categories_count ?></h4>
                </div>
                <div style="background: #f6c23e; color: #fff; padding: 12px; border-radius: 10px;">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card-clean p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1" style="font-size: 12px;">USERS</p>
                    <h4 class="mb-0 fw-bold"><?= $users_count ?></h4>
                </div>
                <div style="background: #e74c3c; color: #fff; padding: 12px; border-radius: 10px;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card-clean">
    <div class="card-header p-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">All Products</h5>
            <a href="products/create.php" class="btn btn-primary btn-sm">+ Add Product</a>
        </div>
    </div>
    <table class="table mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
            while ($row = $stmt->fetch()):
            ?>
            <tr>
                <td>#<?= $row['id'] ?></td>
                <td>
                    <?php if($row['image']): ?>
                    <img src="../uploads/<?= $row['image'] ?>" width="40" height="40" style="object-fit: cover; border-radius: 5px;">
                    <?php else: ?>
                    <div style="width: 40px; height: 40px; background: #f0f0f0; border-radius: 5px;"></div>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td>$<?= number_format($row['price'], 2) ?></td>
                <td>
                    <span style="background: <?= $row['status']=='active'?'#d4edda':'#eee' ?>; padding: 3px 10px; border-radius: 10px; font-size: 12px;">
                        <?= $row['status'] ?>
                    </span>
                </td>
                <td>
                    <a href="products/edit.php?id=<?= $row['id'] ?>" class="text-warning me-2">Edit</a>
                    <a href="products/delete.php?id=<?= $row['id'] ?>" class="text-danger" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>