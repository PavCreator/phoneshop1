<?php 
$title = "Products - MyShop";
require_once 'config/database.php';
require_once 'includes/functions.php';

$search = $_GET['search'] ?? '';
$category_id = $_GET['category'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$conditions = ["status = 'active'"];
$params = [];

if ($search) {
    $conditions[] = "(product_name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_id) {
    $conditions[] = "category_id = ?";
    $params[] = $category_id;
}

$where = implode(" AND ", $conditions);
$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE $where");
$total_stmt->execute($params);
$total = $total_stmt->fetchColumn();
$total_pages = ceil($total / $limit);

$sql = "SELECT * FROM products WHERE $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name");
$categories = $cat_stmt->fetchAll();

function getCategoryIcon($name) {
    $name = strtolower($name);
    if (strpos($name, 'phone') !== false) return 'fa-mobile-alt';
    if (strpos($name, 'laptop') !== false) return 'fa-laptop';
    if (strpos($name, 'tablet') !== false) return 'fa-tablet-alt';
    if (strpos($name, 'watch') !== false) return 'fa-clock';
    if (strpos($name, 'headphone') !== false) return 'fa-headphones';
    if (strpos($name, 'camera') !== false) return 'fa-camera';
    if (strpos($name, 'tv') !== false) return 'fa-tv';
    if (strpos($name, 'game') !== false) return 'fa-gamepad';
    return 'fa-tag';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { font-family: 'Inter', sans-serif; background: #fff; color: #333; }
        
        /* Navbar */
        .navbar-custom { padding: 20px 0; background: #fff; box-shadow: 0 2px 20px rgba(0,0,0,0.05); }
        .navbar-brand { font-size: 25px; font-weight: 700; }
        .nav-link { color: #666; font-weight: 500; padding: 8px 20px; }
        .nav-link:hover, .nav-link.active { color: #667eea; }
        .search-input { background: #f5f5f5; border: none; border-radius: 25px; padding: 10px 20px; width: 220px; }
        
        /* Page Title */
        .page-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 0; margin-bottom: 40px; }
        .page-header h1 { color: #fff; font-size: 36px; font-weight: 700; }
        .page-header p { color: rgba(255,255,255,0.8); margin: 0; }
        
        /* Sidebar */
        .sidebar-card { background: #fff; border: 1px solid #eee; border-radius: 15px; padding: 20px; }
        .sidebar-title { font-size: 16px; font-weight: 600; margin-bottom: 15px; color: #333; }
        
        .category-link { display: block; padding: 12px 15px; border-radius: 10px; color: #666; text-decoration: none; transition: all 0.3s; margin-bottom: 5px; }
        .category-link:hover { background: #f5f5f5; color: #667eea; }
        .category-link.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
        
        /* Search Box */
        .search-box { background: #fff; border: 1px solid #eee; border-radius: 15px; padding: 20px; margin-bottom: 30px; }
        .search-box input { border: 1px solid #eee; border-radius: 10px; padding: 12px 15px; }
        .search-box input:focus { border-color: #667eea; outline: none; }
        
        /* Product Card */
        .product-card { background: #fff; border: 1px solid #eee; border-radius: 15px; overflow: hidden; transition: all 0.3s; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        
        .btn-detail { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 12px 25px; border-radius: 25px; font-weight: 500; border: none; }
        
        /* Pagination */
        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 40px; }
        .pagination a { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 10px; color: #666; text-decoration: none; background: #f5f5f5; transition: all 0.3s; }
        .pagination a:hover { background: #667eea; color: #fff; }
        .pagination a.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
        
        /* Empty State */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 60px; color: #ddd; margin-bottom: 20px; }
        
        /* Footer */
        .footer { background: #111; padding: 60px 0 25px; }
        .footer-h { color: #fff; font-weight: 600; font-size: 18px; margin-bottom: 20px; }
        .footer a { color: #888; text-decoration: none; display: block; padding: 7px 0; }
        .footer a:hover { color: #fff; }
        .footer-bottom { border-top: 1px solid #222; padding: 25px 0; margin-top: 40px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-shopping-bag me-2" style="color: #667eea;"></i>MyShop
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                
                <form class="d-flex ms-4" action="products.php" method="GET">
                    <input class="search-input" type="search" name="search" placeholder="Search products...">
                </form>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1>Products</h1>
            <p>Browse our collection of products</p>
        </div>
    </div>

    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar-card">
                    <div class="sidebar-title">Categories</div>
                    
                    <a href="products.php" class="category-link <?= !$category_id ? 'active' : '' ?>">
                        <i class="fas fa-th-large me-2"></i>All Products
                    </a>
                    
                    <?php foreach ($categories as $cat): 
                        $icon = getCategoryIcon($cat['category_name']);
                    ?>
                    <a href="products.php?category=<?= $cat['id'] ?>" class="category-link <?= $category_id == $cat['id'] ? 'active' : '' ?>">
                        <i class="fas <?= $icon ?> me-2"></i><?= sanitize($cat['category_name']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Products -->
            <div class="col-lg-9">
                <!-- Search -->
                <div class="search-box">
                    <form method="GET" class="d-flex gap-2">
                        <?php if($category_id): ?>
                        <input type="hidden" name="category" value="<?= $category_id ?>">
                        <?php endif; ?>
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= sanitize($search) ?>">
                        <button type="submit" class="btn btn-detail">Search</button>
                        <?php if($search): ?>
                        <a href="products.php<?= $category_id ? '?category='.$category_id : '' ?>" class="btn btn-secondary" style="padding: 12px 20px; border-radius: 25px;">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Results -->
                <p style="color: #666; margin-bottom: 20px;">Showing <?= count($products) ?> of <?= $total ?> products</p>

                <?php if(empty($products)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h4>No products found</h4>
                    <p style="color: #999;">Try a different search term</p>
                    <a href="products.php" class="btn btn-detail">View All Products</a>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col-md-4 col-lg-4">
                        <div class="product-card h-100">
                            <?php if($product['image'] && file_exists('uploads/'.$product['image'])): ?>
                            <img src="uploads/<?= $product['image'] ?>" style="height: 200px; object-fit: cover; width: 100%;">
                            <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                            <?php endif; ?>
                            
                            <div class="p-3">
                                <h6><?= sanitize($product['product_name']) ?></h6>
                                <p class="text-muted small mb-2"><?= substr($product['description'], 0, 50) ?>...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span style="font-size: 18px; font-weight: 700;">$<?= number_format($product['price'], 2) ?></span>
                                    <span style="background: #667eea; color: #fff; padding: 5px 12px; border-radius: 15px; font-size: 11px;"><?= $product['status'] ?></span>
                                </div>
                            </div>
                            <div class="p-3 pt-0">
                                <a href="product-detail.php?id=<?= $product['id'] ?>" class="btn btn-detail w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <div class="pagination">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?><?= $search ? '&search='.$search : '' ?><?= $category_id ? '&category='.$category_id : '' ?>" class="<?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-4">
                    <div class="footer-h"><i class="fas fa-shopping-bag me-2" style="color: #667eea;"></i>MyShop</div>
                    <p style="color: #888; margin-top: 15px;">Your trusted online store for quality products.</p>
                </div>
                <div class="col-md-2">
                    <h6 style="color: #fff; font-weight: 600; margin-bottom: 15px;">Shop</h6>
                    <a href="index.php" style="color: #888;">Home</a>
                    <a href="products.php" style="color: #888;">Products</a>
                </div>
                <div class="col-md-2">
                    <h6 style="color: #fff; font-weight: 600; margin-bottom: 15px;">Support</h6>
                    <a href="#" style="color: #888;">Contact</a>
                    <a href="#" style="color: #888;">FAQs</a>
                </div>
                <div class="col-md-4">
                    <h6 style="color: #fff; font-weight: 600; margin-bottom: 15px;">News letter</h6>
                    <p style="color: #888;">Subscribe for updates.</p>
                    
                </div>
            </div>
            
            <div class="footer-bottom text-center">
                <p style="color: #666; margin: 0;">&copy; <?= date('Y') ?> MyShop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>