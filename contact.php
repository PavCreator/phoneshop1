<?php 
$title = "Contact - MyShop";
require_once 'config/database.php';
require_once 'includes/functions.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    if ($name && $email && $subject && $message) {
        // In production, you'd save to database or send email
        $success = true;
    } else {
        $error = 'Please fill in all fields';
    }
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
        .search-input { background: #f5f5f5; border: none; border-radius: 25px; padding: 10px 20px; width: 180px; }
        
        /* Page Header */
        .page-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 60px 0; }
        .page-header h1 { color: #fff; font-size: 40px; font-weight: 700; }
        .page-header p { color: rgba(255,255,255,0.8); margin: 10px 0 0; }
        
        /* Contact Section */
        .contact-card { background: #fff; border: 1px solid #eee; border-radius: 20px; padding: 40px; }
        
        .contact-info { padding: 30px 0; border-bottom: 1px solid #eee; margin-bottom: 30px; }
        
        .contact-item { display: flex; align-items: flex-start; gap: 20px; margin-bottom: 25px; }
        
        .contact-icon { 
            width: 50px; height: 50px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            color: #fff; font-size: 18px; 
            flex-shrink: 0;
        }
        
        .contact-text h5 { font-weight: 600; margin-bottom: 5px; }
        .contact-text p { color: #666; margin: 0; }
        
        /* Form */
        .form-label { font-weight: 500; margin-bottom: 8px; }
        
        .form-control { 
            border: 1px solid #eee; 
            border-radius: 12px; 
            padding: 14px 18px; 
            margin-bottom: 20px;
        }
        
        .form-control:focus { border-color: #667eea; outline: none; box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }
        
        .btn-submit { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: #fff; 
            padding: 15px 40px; 
            border-radius: 30px; 
            font-weight: 600; 
            border: none;
            font-size: 16px;
        }
        
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(102,126,234,0.3); }
        
        /* Map */
        .map-container { background: #f5f5f5; border-radius: 20px; height: 100%; min-height: 400px; display: flex; align-items: center; justify-content: center; }
        
        /* Success */
        .alert-success { background: #d4edda; border: none; border-radius: 12px; padding: 20px; }
        
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
                    <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
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
            <h1>Contact Us</h1>
            <p>We'd love to hear from you</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-card">
                    <h3 style="font-weight: 700; margin-bottom: 30px;">Send us a Message</h3>
                    
                    <?php if($success): ?>
                    <div class="alert-success">
                        <i class="fas fa-check-circle me-2" style="color: #28a745;"></i>
                        Thank you! We've received your message and will get back to you soon.
                    </div>
                    <?php else: ?>
                    
                    <?php if($error): ?>
                    <div class="alert alert-danger" style="background: #f8d7da; border: none; border-radius: 12px;">
                        <?= $error ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Your Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Ah Long" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="AhLong@gmail.com" required>
                            </div>
                        </div>
                        
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="How can we help?" required>
                        
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="6" placeholder="Your message..." required></textarea>
                        
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="contact-card">
                    <h3 style="font-weight: 700; margin-bottom: 30px;">Contact Information</h3>
                    
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="contact-text">
                                <h5>Our Location</h5>
                                <p>123 Shop Street, PhnomPenh, Cambodia</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-phone"></i></div>
                            <div class="contact-text">
                                <h5>Phone</h5>
                                <p>012 345 6789</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                            <div class="contact-text">
                                <h5>Email</h5>
                                <p>Pav@myshop.com</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-clock"></i></div>
                            <div class="contact-text">
                                <h5>Opening Hours</h5>
                                <p>Mon - Fri: 9AM - 6PM</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social -->
                    <h5 style="font-weight: 600; margin-bottom: 15px;">Follow Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" style="width: 45px; height: 45px; background: #f5f5f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #333; text-decoration: none;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" style="width: 45px; height: 45px; background: #f5f5f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #333; text-decoration: none;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" style="width: 45px; height: 45px; background: #f5f5f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #333; text-decoration: none;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" style="width: 45px; height: 45px; background: #f5f5f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #333; text-decoration: none;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <a href="contact.php" style="color: #888;">Contact</a>
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