<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../auth/login.php");
    exit;
}

$title = $title ?? 'Admin Panel';
$current_folder = basename(dirname($_SERVER['PHP_SELF']));
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - MyShop Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        
        /* Sidebar */
        .sidebar {
            background: #1a1a1a;
            min-height: 100vh;
            position: fixed;
            width: 260px;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 30px 25px;
            border-bottom: 1px solid #333;
            text-align: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 8px;
        }
        
        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
        }
        
        .logo-text {
            color: #fff;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .logo-subtitle {
            color: #888;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .user-info {
            padding: 20px 25px;
            border-bottom: 1px solid #333;
            text-align: center;
        }
        
        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
            margin: 0 auto 10px;
        }
        
        .user-name {
            color: #fff;
            font-weight: 600;
            font-size: 15px;
        }
        
        .user-role {
            color: #888;
            font-size: 12px;
        }
        
        /* Menu */
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-label {
            color: #555;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 25px;
            font-weight: 600;
        }
        
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 25px;
            color: #999;
            text-decoration: none;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 14px;
        }
        
        .sidebar a:hover {
            background: #252525;
            color: #fff;
        }
        
        .sidebar a.active {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            margin-right: 10px;
            border-radius: 0 10px 10px 0;
        }
        
        .sidebar a i {
            width: 20px;
            font-size: 16px;
            text-align: center;
        }
        
        .menu-divider {
            height: 1px;
            background: #333;
            margin: 15px 25px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }
        
        /* Topbar */
        .topbar {
            background: #fff;
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .topbar h4 {
            font-weight: 600;
            margin: 0;
        }
        
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .topbar-actions a {
            color: #666;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.2s;
            font-size: 14px;
        }
        
        .topbar-actions a:hover {
            background: #f5f5f5;
            color: #000;
        }
        
        /* Content */
        .content {
            padding: 30px;
        }
        
        /* Cards */
        .card-clean {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd6 0%, #654ba0 100%);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <span class="logo-text">MyShop</span>
            </div>
            <div class="logo-subtitle">Admin Panel</div>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="user-name"><?= $_SESSION['name'] ?? 'Admin' ?></div>
            <div class="user-role">Administrator</div>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-label">Main Menu</div>
            
            <a href="/project/admin/dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            
            <a href="/project/admin/categories/index.php" class="<?= $current_folder == 'categories' ? 'active' : '' ?>">
                <i class="fas fa-tags"></i> Categories
            </a>
            
            <a href="/project/admin/products/index.php" class="<?= $current_folder == 'products' ? 'active' : '' ?>">
                <i class="fas fa-box"></i> Products
            </a>
            
            <div class="menu-divider"></div>
            
            <div class="menu-label">Account</div>
            
            <a href="/project/index.php" target="_blank">
                <i class="fas fa-globe"></i> View Website
            </a>
            
            <a href="/project/auth/logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="topbar">
            <h4><?= $title ?></h4>
            <div class="topbar-actions">
                <a href="/project/index.php" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i> Visit Site
                </a>
                <a href="/project/auth/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </header>

        <div class="content">