<?php
require_once '../config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Generate token - use raw binary then hex
        $token = bin2hex(random_bytes(16)); // 16 bytes = 32 chars
        $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        
        // Save to database
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $stmt->execute([$token, $expiry, $email]);
        
        // Show link
        $link = "http://localhost/project/auth/reset-password.php?token=$token&email=$email";
        $message = '<div class="alert alert-success">
            <p>Reset link (copy this):</p>
            <a href="'.$link.'">'.$link.'</a>
        </div>';
    } else {
        $message = '<div class="alert alert-danger">Email not found!</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#f5f5f5}</style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4" style="width:400px">
        <h3>Forgot Password</h3>
        <?= $message ?? '' ?>
        <form method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning w-100">Get Reset Link</button>
        </form>
    </div>
</body>
</html>