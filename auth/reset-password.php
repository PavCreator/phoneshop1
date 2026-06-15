<?php
require_once '../config/database.php';

$message = '';
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

// DEBUG: Show what's in database
if ($token && $email) {
    $stmt = $pdo->prepare("SELECT reset_token, token_expiry FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    echo "<!-- DEBUG: Token from URL: $token -->\n";
    echo "<!-- DEBUG: Email from URL: $email -->\n";
    
    if ($user) {
        echo "<!-- DEBUG: Token in DB: " . $user['reset_token'] . " -->\n";
        echo "<!-- DEBUG: Expiry in DB: " . $user['token_expiry'] . " -->\n";
        
        // Check if token matches
        if ($token !== $user['reset_token']) {
            $message = '<div class="alert alert-danger">Token does not match! Debug info logged.</div>';
        } elseif (strtotime($user['token_expiry']) < time()) {
            $message = '<div class="alert alert-danger">Token has expired! Request a new one.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Email not found!</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'] ?? '';
    $email = $_POST['email'] ?? '';
    $new_password = $_POST['password'];
    
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE email = ? AND reset_token = ?");
    $stmt->execute([$hashed_password, $email, $token]);
    
    if ($stmt->rowCount() > 0) {
        $message = '<div class="alert alert-success">Password reset! <a href="login.php">Login here</a></div>';
    } else {
        $message = '<div class="alert alert-danger">Error resetting password!</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#f5f5f5}</style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4" style="width:400px">
        <h3>Reset Password</h3>
        <?= $message ?? '' ?>
        
        <?php if($token && empty($message) || (strpos($message ?? '', 'does not match') === false && strpos($message ?? '', 'expired') === false)): ?>
        <form method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Reset Password</button>
        </form>
        <?php else: ?>
        <a href="forgot-password.php" class="btn btn-warning w-100">Request New Link</a>
        <?php endif; ?>
    </div>
</body>
</html>