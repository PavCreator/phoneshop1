<?php 
$title = "Welcome to MyShop";
require_once 'config/database.php';
require_once 'includes/functions.php';
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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { font-family: 'Inter', sans-serif; background: #fafafa; color: #222; }
        
        /* Navbar */
        .navbar-custom { 
            padding: 16px 0; 
            background: #fff; 
            box-shadow: 0 1px 8px rgba(0,0,0,0.08); 
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-brand { font-size: 24px; font-weight: 800; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .nav-link { color: #666; font-weight: 500; padding: 8px 18px; font-size: 14px; }
        .nav-link:hover, .nav-link.active { color: #667eea; font-weight: 600; }
        .search-input { background: #f5f5f5; border: 1px solid #eee; border-radius: 25px; padding: 10px 18px; width: 180px; font-size: 13px; }
        .search-input:focus { outline: none; background: #fff; border-color: #667eea; }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 120px 0;
            position: relative;
            overflow: hidden;
            color: #fff;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }
        
        .hero-content { position: relative; z-index: 2; }
        .hero-title { font-size: 56px; font-weight: 800; margin-bottom: 20px; line-height: 1.2; }
        .hero-subtitle { font-size: 20px; opacity: 0.95; margin-bottom: 35px; font-weight: 300; }
        .btn-primary-custom { 
            background: #fff; 
            color: #667eea; 
            padding: 16px 40px; 
            border-radius: 30px; 
            font-weight: 700; 
            border: none;
            transition: all 0.3s;
            display: inline-block;
            text-decoration: none;
        }
        .btn-primary-custom:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 12px 35px rgba(0,0,0,0.25);
            background: #fff;
            color: #667eea;
        }
        
        .hero-illustration { 
            font-size: 200px; 
            opacity: 0.15;
            text-align: center;
        }
        
        /* Section Styles */
        .section-header { text-align: center; margin-bottom: 60px; }
        .section-title { font-size: 40px; font-weight: 800; margin-bottom: 15px; color: #111; }
        .section-subtitle { font-size: 18px; color: #888; font-weight: 400; }
        .section-padding { padding: 80px 0; }
        
        /* Category Cards */
        .category-section { background: #fff; }
        .category-card { 
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            border: 2px solid transparent;
            border-radius: 20px; 
            padding: 40px 25px;
            text-align: center; 
            transition: all 0.4s;
            cursor: pointer;
        }
        .category-card:hover { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }
        .category-card i { font-size: 36px; margin-bottom: 15px; }
        .category-card h6 { font-size: 16px; font-weight: 700; }
        
        /* Product Section */
        .product-section { background: #fafafa; }
        .product-card { 
            background: #fff; 
            border: none;
            border-radius: 18px; 
            overflow: hidden; 
            transition: all 0.4s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .product-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 20px 50px rgba(102, 126, 234, 0.25);
        }
        .product-img { height: 200px; object-fit: cover; width: 100%; }
        .product-body { padding: 20px; }
        .product-title { font-size: 15px; font-weight: 700; margin-bottom: 8px; color: #222; }
        .product-desc { font-size: 13px; color: #888; margin-bottom: 12px; line-height: 1.4; }
        .product-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; }
        .product-price { font-size: 20px; font-weight: 800; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .product-badge { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        
        .btn-detail { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: #fff; 
            padding: 12px 24px; 
            border-radius: 25px; 
            font-weight: 600; 
            border: none;
            transition: all 0.3s;
        }
        .btn-detail:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: #fff;
        }
        
        /* Features */
        .features-section { background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); }
        .feature-item { text-align: center; padding: 30px 20px; }
        .feature-icon { 
            width: 80px; 
            height: 80px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0 auto 20px; 
            color: #fff; 
            font-size: 28px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        .feature-title { font-size: 18px; font-weight: 700; margin-bottom: 10px; }
        .feature-desc { color: #888; font-size: 14px; }
        
        /* Footer */
        .footer { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 60px 0 25px; color: #ccc; }
        .footer-title { color: #fff; font-weight: 700; font-size: 18px; margin-bottom: 20px; }
        .footer a { color: #aaa; text-decoration: none; display: block; padding: 8px 0; font-size: 14px; transition: all 0.3s; }
        .footer a:hover { color: #667eea; padding-left: 5px; }
        .footer-bottom { border-top: 1px solid #333; padding: 25px 0; margin-top: 40px; text-align: center; font-size: 14px; color: #666; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title { font-size: 36px; }
            .hero-subtitle { font-size: 16px; }
            .section-title { font-size: 28px; }
            .hero-illustration { display: none; }
        }
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
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                
                <form class="d-flex ms-4" action="products.php" method="GET">
                    <input class="search-input" type="search" name="search" placeholder="Search products...">
                </form>
            </div>
        </div>
    </nav>

    <!-- Banner Slider -->
    <div id="bannerSlider" class="carousel slide banner-slider" data-bs-ride="carousel" data-bs-interval="5000">
        <!-- Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerSlider" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#bannerSlider" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#bannerSlider" data-bs-slide-to="2"></button>
        </div>
        
        <!-- Slides -->
        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active">
                <div class="banner-slide">
                    <div class="container">
                        <div class="banner-content">
                            <div class="banner-text">
                                <h1>Summer Sale</h1>
                                <p>Get up to 50% off on all electronics. Limited time offer!</p>
                                <a href="products.php" class="btn btn-banner">Shop Now</a>
                            </div>
                            <div class="banner-img">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="carousel-item">
                <div class="banner-slide" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="container">
                        <div class="banner-content">
                            <div class="banner-text">
                                <h1>New Arrivals</h1>
                                <p>Check out the latest products in our store.</p>
                                <a href="products.php" class="btn btn-banner">View Products</a>
                            </div>
                            <div class="banner-img">
                                <i class="fas fa-box-open"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="carousel-item">
                <div class="banner-slide" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="container">
                        <div class="banner-content">
                            <div class="banner-text">
                                <h1>Free Shipping</h1>
                                <p>Free shipping on orders over $50. Don't miss out!</p>
                                <a href="products.php" class="btn btn-banner">Shop Now</a>
                            </div>
                            <div class="banner-img">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Categories -->
    <section class="category-section section-padding">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Shop by Category</h2>
                <p class="section-subtitle">Explore our wide range of product categories</p>
            </div>
            <div class="row g-4">
                <?php
                $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name LIMIT 6");
                while ($cat = $stmt->fetch()):
                ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="products.php?category=<?= $cat['id'] ?>" class="text-decoration-none" style="color: inherit;">
                        <div class="category-card">
                            <i class="fas fa-tag" style="color: #667eea;"></i>
                            <h6 class="mb-0"><?= sanitize($cat['category_name']) ?></h6>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section class="product-section section-padding">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Featured Products</h2>
                <p class="section-subtitle">Check out our best-selling items</p>
            </div>
            <div class="row g-4">
                <?php
                $stmt = $pdo->query("SELECT * FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 8");
                while ($product = $stmt->fetch()):
                ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card h-100">
                        <?php if($product['image'] && file_exists('uploads/'.$product['image'])): ?>
                        <img src="uploads/<?= $product['image'] ?>" class="product-img">
                        <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                        <?php endif; ?>
                        
                        <div class="product-body">
                            <h6 class="product-title"><?= sanitize($product['product_name']) ?></h6>
                            <p class="product-desc"><?= substr($product['description'], 0, 45) ?>...</p>
                            <div class="product-footer">
                                <span class="product-price">$<?= number_format($product['price'], 2) ?></span>
                                <span class="product-badge"><?= $product['status'] ?></span>
                            </div>
                            <a href="product-detail.php?id=<?= $product['id'] ?>" class="btn btn-detail w-100 mt-3">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features-section section-padding">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-4 feature-item">
                    <div class="feature-icon"><i class="fas fa-shipping-fast"></i></div>
                    <h6 class="feature-title">Free Shipping</h6>
                    <p class="feature-desc">On orders over $50, enjoy fast and free delivery</p>
                </div>
                <div class="col-md-4 feature-item">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h6 class="feature-title">Secure Payment</h6>
                    <p class="feature-desc">100% secure transactions with encrypted data</p>
                </div>
                <div class="col-md-4 feature-item">
                    <div class="feature-icon"><i class="fas fa-undo"></i></div>
                    <h6 class="feature-title">Easy Returns</h6>
                    <p class="feature-desc">30-day return policy, hassle-free process</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-5 mb-4">
                <div class="col-md-3">
                    <div class="footer-title"><i class="fas fa-shopping-bag me-2" style="color: #667eea;"></i>MyShop</div>
                    <p style="color: #999; margin-top: 12px; font-size: 14px;">Your trusted online store for quality products and exceptional service.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="footer-title">Shop</h6>
                    <a href="index.php">Home</a>
                    <a href="products.php">Products</a>
                    <a href="#">Categories</a>
                </div>
                <div class="col-md-3">
                    <h6 class="footer-title">Support</h6>
                    <a href="contact.php">Contact Us</a>
                    <a href="#">FAQs</a>
                    <a href="#">Shipping Info</a>
                </div>
                <div class="col-md-3">
                    <h6 class="footer-title">Newsletter</h6>
                    <p style="color: #999; font-size: 14px;">Subscribe for exclusive deals and updates.</p>
                    <div class="input-group" style="margin-top: 10px;">
                        <input type="email" class="form-control" placeholder="Your email" style="border-radius: 25px 0 0 25px; border: none; background: #222; color: #fff; font-size: 13px;">
                        <button class="btn btn-detail" style="border-radius: 0 25px 25px 0; border: none;">Subscribe</button>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> MyShop. All rights reserved. | <a href="#" style="color: #667eea; text-decoration: none;">Privacy Policy</a> | <a href="#" style="color: #667eea; text-decoration: none;">Terms of Service</a></p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>