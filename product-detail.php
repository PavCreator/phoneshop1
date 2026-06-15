<?php 
$title = "Product Details - MyShop";
require_once 'config/database.php';
require_once 'includes/functions.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ? AND p.status = 'active'");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}

// Get related products
$related_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? AND status = 'active' LIMIT 4");
$related_stmt->execute([$product['category_id'], $id]);
$related_products = $related_stmt->fetchAll();
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
        
        body { font-family: 'Inter', sans-serif; background: #f9f9f9; color: #333; }
        
        /* Navbar */
        .navbar-custom { padding: 20px 0; background: #fff; box-shadow: 0 2px 20px rgba(0,0,0,0.05); }
        .navbar-brand { font-size: 25px; font-weight: 700; }
        .nav-link { color: #666; font-weight: 500; padding: 8px 20px; }
        .nav-link:hover, .nav-link.active { color: #667eea; }
        .search-input { background: #f5f5f5; border: none; border-radius: 25px; padding: 10px 20px; width: 180px; }
        
        /* Breadcrumb */
        .breadcrumb-custom { background: #fff; padding: 20px 0; margin-bottom: 30px; box-shadow: 0 1px 10px rgba(0,0,0,0.02); }
        .breadcrumb-custom a { color: #667eea; text-decoration: none; font-weight: 500; }
        .breadcrumb-custom a:hover { text-decoration: underline; }
        .breadcrumb-custom span { color: #ddd; margin: 0 10px; }
        
        /* Product Detail Card */
        .product-detail-card { background: #fff; border-radius: 20px; padding: 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); margin-bottom: 40px; }
        
        /* Image Gallery */
        .image-gallery-container { position: relative; }
        .main-image { width: 100%; height: 450px; object-fit: cover; border-radius: 15px; background: #f5f5f5; }
        .main-image-placeholder { width: 100%; height: 450px; background: #f5f5f5; border-radius: 15px; display: flex; align-items: center; justify-content: center; }
        
        .thumb-images { display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap; }
        .thumb-image { width: 80px; height: 80px; border-radius: 10px; cursor: pointer; border: 2px solid #eee; overflow: hidden; transition: all 0.3s; }
        .thumb-image:hover { border-color: #667eea; }
        .thumb-image img { width: 100%; height: 100%; object-fit: cover; }
        
        /* Product Info */
        .product-header { margin-bottom: 25px; }
        .category-badge { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 8px 18px; border-radius: 25px; font-size: 12px; font-weight: 600; display: inline-block; margin-bottom: 15px; }
        
        .product-title { font-size: 32px; font-weight: 700; margin-bottom: 15px; color: #1a1a1a; }
        
        .rating-section { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .stars { display: flex; gap: 3px; }
        .star { color: #ffd700; font-size: 18px; }
        .star.empty { color: #ddd; }
        .review-count { color: #666; font-size: 14px; }
        
        .product-price-section { margin-bottom: 30px; }
        .product-price { font-size: 36px; font-weight: 700; color: #667eea; margin-bottom: 8px; }
        .price-subtext { color: #999; font-size: 14px; }
        
        .product-desc { color: #555; line-height: 1.8; margin-bottom: 30px; font-size: 15px; }
        
        /* Specifications */
        .specs-section { background: #f9f9f9; padding: 20px; border-radius: 12px; margin-bottom: 25px; }
        .spec-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee; }
        .spec-item:last-child { border-bottom: none; }
        .spec-label { color: #666; font-weight: 500; }
        .spec-value { font-weight: 600; color: #333; }
        
        /* Quantity & Action Buttons */
        .purchase-section { margin-bottom: 30px; }
        .quantity-section { display: flex; align-items: center; gap: 20px; margin-bottom: 25px; }
        .quantity-label { color: #333; font-weight: 600; font-size: 15px; }
         
        .quantity-input { 
            display: flex; 
            align-items: center; 
            border: 2px solid #e5e5e5; 
            border-radius: 12px; 
            background: #fff;
            transition: all 0.3s;
        }
        .quantity-input:focus-within { 
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
         
        .quantity-btn { 
            background: transparent;
            border: none; 
            padding: 12px 16px; 
            cursor: pointer; 
            font-weight: 700;
            transition: all 0.2s;
            color: #667eea;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
        }
        .quantity-btn:hover { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }
        .quantity-btn:active { 
            transform: scale(0.95);
        }
         
        .quantity-input input { 
            width: 70px; 
            padding: 12px 8px; 
            border: none; 
            text-align: center; 
            font-weight: 700;
            font-size: 16px;
            background: transparent;
            color: #333;
        }
        .quantity-input input:focus { 
            outline: none;
        }
        .quantity-input input::-webkit-outer-spin-button,
        .quantity-input input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .quantity-input input[type=number] {
            -moz-appearance: textfield;
        }
         
        .quantity-info { 
            font-size: 13px; 
            color: #28a745; 
            font-weight: 500;
            margin-top: 8px;
        }
        .quantity-info i { margin-right: 6px; }
        
        .action-buttons { display: flex; gap: 15px; }
        .btn-add-cart { 
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: #fff; 
            padding: 16px 35px; 
            border-radius: 30px; 
            font-weight: 600; 
            border: none;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-add-cart:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4); }
        
        .btn-wishlist {
            background: #fff;
            border: 2px solid #ddd;
            color: #666;
            padding: 14px 25px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-wishlist:hover { border-color: #667eea; color: #667eea; background: #f9f9f9; }
        .btn-wishlist.liked { border-color: #e74c3c; color: #e74c3c; background: #ffe5e5; }
        
        /* Tabs */
        .tabs-section { margin-top: 40px; border-top: 1px solid #eee; padding-top: 40px; }
        .tabs-nav { display: flex; gap: 0; border-bottom: 2px solid #eee; margin-bottom: 30px; }
        .tab-link { 
            padding: 15px 25px; 
            cursor: pointer; 
            color: #666; 
            font-weight: 600; 
            border-bottom: 3px solid transparent; 
            transition: all 0.3s;
            margin-bottom: -2px;
        }
        .tab-link:hover { color: #667eea; }
        .tab-link.active { color: #667eea; border-bottom-color: #667eea; }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        /* Reviews */
        .review-item { padding: 25px; background: #f9f9f9; border-radius: 12px; margin-bottom: 20px; }
        .review-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px; }
        .reviewer-name { font-weight: 600; color: #333; }
        .review-date { color: #999; font-size: 13px; }
        .review-rating { color: #ffd700; margin-bottom: 8px; }
        .review-text { color: #555; line-height: 1.6; }
        
        .add-review-form { background: #f9f9f9; padding: 25px; border-radius: 12px; margin-top: 25px; }
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; font-weight: 600; margin-bottom: 8px; color: #333; }
        .form-input, .form-textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; }
        .form-textarea { min-height: 120px; resize: vertical; }
        .btn-submit-review { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 12px 30px; border: none; border-radius: 25px; font-weight: 600; cursor: pointer; }
        
        /* Related Products */
        .related-section { margin-top: 10px; padding-top: 10px; padding-bottom: 40px; }
        .related-title { font-size: 32px; font-weight: 700; margin-bottom: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        .product-card { background: #fff; border: 1px solid #eee; border-radius: 15px; overflow: hidden; transition: all 0.3s; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        
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
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                </ul>
                
                <form class="d-flex ms-4" action="products.php" method="GET">
                    <input class="search-input" type="search" name="search" placeholder="Search products...">
                </form>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="breadcrumb-custom">
        <div class="container">
            <a href="index.php">Home</a>
            <span>/</span>
            <a href="products.php">Products</a>
            <span>/</span>
            <a href="products.php?category=<?= $product['category_id'] ?>"><?= sanitize($product['category_name'] ?? 'All') ?></a>
            <span>/</span>
            <span style="color: #333;"><?= sanitize($product['product_name']) ?></span>
        </div>
    </div>

    <!-- Product Detail -->
    <div class="container">
        <div class="product-detail-card">
            <div class="row g-5">
                <!-- Image Gallery -->
                <div class="col-lg-5">
                    <div class="image-gallery-container">
                        <?php if($product['image'] && file_exists('uploads/'.$product['image'])): ?>
                        <img src="uploads/<?= $product['image'] ?>" id="mainImage" class="main-image" alt="<?= sanitize($product['product_name']) ?>">
                        <?php else: ?>
                        <div class="main-image-placeholder">
                            <i class="fas fa-image fa-5x text-muted"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="col-lg-7">
                    <div class="product-header">
                        <span class="category-badge"><?= sanitize($product['category_name'] ?? 'Uncategorized') ?></span>
                        <h1 class="product-title"><?= sanitize($product['product_name']) ?></h1>
                        
                        <!-- Rating -->
                        <div class="rating-section">
                            <div class="stars">
                                <span class="star"><i class="fas fa-star"></i></span>
                                <span class="star"><i class="fas fa-star"></i></span>
                                <span class="star"><i class="fas fa-star"></i></span>
                                <span class="star"><i class="fas fa-star"></i></span>
                                <span class="star empty"><i class="fas fa-star"></i></span>
                            </div>
                            <span class="review-count">(4.0/5 • 48 reviews)</span>
                        </div>
                    </div>
                    
                    <!-- Price -->
                    <div class="product-price-section">
                        <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                        <div class="price-subtext">Free shipping on orders over $50</div>
                    </div>
                    
                    <!-- Description -->
                    <p class="product-desc"><?= nl2br(sanitize($product['description'])) ?></p>
                    
                    <!-- Specifications -->
                    <div class="specs-section">
                        <div class="spec-item">
                            <span class="spec-label">Status</span>
                            <span class="spec-value"><i class="fas fa-check-circle" style="color: #28a745; margin-right: 6px;"></i><?= ucfirst($product['status']) ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Available</span>
                            <span class="spec-value">In Stock</span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Added</span>
                            <span class="spec-value"><?= date('M d, Y', strtotime($product['created_at'])) ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">SKU</span>
                            <span class="spec-value">#<?= str_pad($product['id'], 5, '0', STR_PAD_LEFT) ?></span>
                        </div>
                    </div>
                    
                    <!-- Quantity & Purchase -->
                    <div class="purchase-section">
                        <div class="quantity-section">
                            <label class="quantity-label">Quantity:</label>
                            <div class="quantity-input">
                                <button class="quantity-btn" onclick="decrementQty()" title="Decrease quantity">−</button>
                                <input type="number" id="quantity" value="1" min="1" max="99">
                                <button class="quantity-btn" onclick="incrementQty()" title="Increase quantity">+</button>
                            </div>
                        </div>
                        
                        <div class="action-buttons" style="margin-top: 20px;">
                            <button class="btn-add-cart" onclick="addToCart(<?= $product['id'] ?>)">
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                            <button class="btn-wishlist" id="wishlistBtn" onclick="toggleWishlist()">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="container">
        <div class="tabs-section">
            <!-- Tab Navigation -->
            <div class="tabs-nav">
                <div class="tab-link active" onclick="openTab(event, 'details')">
                    <i class="fas fa-list me-2"></i>Details
                </div>
                <div class="tab-link" onclick="openTab(event, 'reviews')">
                    <i class="fas fa-star me-2"></i>Reviews
                </div>
            </div>
            
            <!-- Details Tab -->
            <div id="details" class="tab-content active">
                <div class="row">
                    <div class="col-md-6">
                        <h4 style="margin-bottom: 20px; font-weight: 700;">Product Specifications</h4>
                        <div class="specs-section" style="background: transparent;">
                            <div class="spec-item">
                                <span class="spec-label">Product Name</span>
                                <span class="spec-value"><?= sanitize($product['product_name']) ?></span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Category</span>
                                <span class="spec-value"><?= sanitize($product['category_name'] ?? 'Uncategorized') ?></span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Price</span>
                                <span class="spec-value">$<?= number_format($product['price'], 2) ?></span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Status</span>
                                <span class="spec-value"><i class="fas fa-check-circle" style="color: #28a745; margin-right: 6px;"></i><?= ucfirst($product['status']) ?></span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-label">Added Date</span>
                                <span class="spec-value"><?= date('M d, Y', strtotime($product['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h4 style="margin-bottom: 20px; font-weight: 700;">Product Information</h4>
                        
                        <div style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); padding: 20px; border-radius: 12px; margin-top: 20px; border-left: 4px solid #667eea;">
                            <h6 style="color: #667eea; font-weight: 700; margin-bottom: 10px;">
                                <i class="fas fa-info-circle me-2"></i>Shipping Info
                            </h6>
                            <p style="color: #666; margin: 0; font-size: 14px;">
                                Free shipping on orders over $50. Usually ships within 2-3 business days. Easy 30-day returns available.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reviews Tab -->
            <div id="reviews" class="tab-content">
                <div class="row">
                    <div class="col-lg-8">
                        <h4 style="margin-bottom: 25px; font-weight: 700;">Customer Reviews</h4>
                        
                        <!-- Sample Reviews -->
                        <div class="review-item">
                            <div class="review-header">
                                <div>
                                    <div class="reviewer-name">
                                        <i class="fas fa-user-circle me-2" style="color: #667eea;"></i>John Doe
                                    </div>
                                    <div class="review-date">Verified Purchase • 2 weeks ago</div>
                                </div>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="review-text">
                                Excellent product! Great quality and fast shipping. Highly recommended to everyone.
                            </div>
                        </div>
                        
                        <div class="review-item">
                            <div class="review-header">
                                <div>
                                    <div class="reviewer-name">
                                        <i class="fas fa-user-circle me-2" style="color: #667eea;"></i>Sarah Smith
                                    </div>
                                    <div class="review-date">Verified Purchase • 1 month ago</div>
                                </div>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            <div class="review-text">
                                Very satisfied with this purchase. The only minor issue was packaging, but product quality is amazing.
                            </div>
                        </div>
                        
                        <div class="review-item">
                            <div class="review-header">
                                <div>
                                    <div class="reviewer-name">
                                        <i class="fas fa-user-circle me-2" style="color: #667eea;"></i>Mike Johnson
                                    </div>
                                    <div class="review-date">Verified Purchase • 1 month ago</div>
                                </div>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                            <div class="review-text">
                                Good value for money. Works as expected. Would buy again!
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add Review -->
                    <div class="col-lg-4">
                        <div style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); padding: 25px; border-radius: 15px; border: 1px solid #667eea30;">
                            <h6 style="font-weight: 700; margin-bottom: 20px;">Share Your Review</h6>
                            <form class="add-review-form" style="background: transparent; padding: 0;">
                                <div class="form-group">
                                    <label class="form-label">Rating</label>
                                    <div style="font-size: 24px; gap: 5px; display: flex;">
                                        <span class="star" onclick="setRating(1)" style="cursor: pointer;">☆</span>
                                        <span class="star" onclick="setRating(2)" style="cursor: pointer;">☆</span>
                                        <span class="star" onclick="setRating(3)" style="cursor: pointer;">☆</span>
                                        <span class="star" onclick="setRating(4)" style="cursor: pointer;">☆</span>
                                        <span class="star" onclick="setRating(5)" style="cursor: pointer;">☆</span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Your Review</label>
                                    <textarea class="form-textarea" placeholder="Share your experience with this product..."></textarea>
                                </div>
                                
                                <button type="submit" class="btn-submit-review w-100">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Review
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if(!empty($related_products)): ?>
    <div class="container">
        <div class="related-section">
            <h2 class="related-title">Related Products</h2>
            
            <div class="row g-4">
                <?php foreach ($related_products as $related): ?>
                <div class="col-md-3">
                    <a href="product-detail.php?id=<?= $related['id'] ?>" class="text-decoration-none">
                        <div class="product-card">
                            <?php if($related['image'] && file_exists('uploads/'.$related['image'])): ?>
                            <img src="uploads/<?= $related['image'] ?>" style="height: 180px; object-fit: cover; width: 100%;">
                            <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="fas fa-image fa-2x text-muted"></i>
                            </div>
                            <?php endif; ?>
                            
                            <div class="p-3">
                                <h6 style="color: #333; font-weight: 700; margin-bottom: 8px;"><?= sanitize($related['product_name']) ?></h6>
                                <div style="font-size: 18px; font-weight: 700; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">$<?= number_format($related['price'], 2) ?></div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-4">
                    <div class="footer-h"><i class="fas fa-shopping-bag me-2" style="color: #667eea;"></i>MyShop</div>
                    <p style="color: #888; margin-top: 15px;">Your trusted online store.</p>
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
                    <h6 style="color: #fff; font-weight: 600; margin-bottom: 15px;">Newsletter</h6>
                    <p style="color: #888;">Subscribe for updates.</p>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email" style="border-radius: 25px; border: none; background: #222; color: #fff;">
                        <button class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-radius: 25px;">Subscribe</button>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom text-center">
                <p style="color: #666; margin: 0;">&copy; <?= date('Y') ?> MyShop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Quantity Controls
        function incrementQty() {
            const qty = document.getElementById('quantity');
            qty.value = Math.min(parseInt(qty.value) + 1, 99);
        }
         
        function decrementQty() {
            const qty = document.getElementById('quantity');
            qty.value = Math.max(parseInt(qty.value) - 1, 1);
        }
        
        // Tab Management
        function openTab(evt, tabName) {
            const tabContents = document.querySelectorAll('.tab-content');
            const tabLinks = document.querySelectorAll('.tab-link');
            
            tabContents.forEach(tab => tab.classList.remove('active'));
            tabLinks.forEach(link => link.classList.remove('active'));
            
            document.getElementById(tabName).classList.add('active');
            evt.currentTarget.classList.add('active');
        }
        
        // Wishlist Toggle
        let isWishlisted = false;
        function toggleWishlist() {
            const btn = document.getElementById('wishlistBtn');
            isWishlisted = !isWishlisted;
            if (isWishlisted) {
                btn.classList.add('liked');
                btn.style.background = '#ffe5e5';
            } else {
                btn.classList.remove('liked');
                btn.style.background = '#fff';
            }
        }
        
        // Add to Cart
        function addToCart(productId) {
            const quantity = document.getElementById('quantity').value;
            alert('Added ' + quantity + ' item(s) to cart! (Feature coming soon)');
        }
        
        // Rating System
        function setRating(rating) {
            const stars = document.querySelectorAll('.add-review-form .star');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.textContent = '★';
                    star.style.color = '#ffd700';
                } else {
                    star.textContent = '☆';
                    star.style.color = '#ddd';
                }
            });
        }
    </script>
</body>
</html>