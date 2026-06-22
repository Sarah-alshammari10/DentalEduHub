<?php
require_once 'config/config.php';

$page_title = 'Login';
$error = '';

if (isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = true");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            // Update last login
            $stmt = $pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            header('Location: /index.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - ' . SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body>

<div class="auth-container">
    <!-- Image Side -->
    <div class="auth-image-side">
        <div class="auth-image-content">
            <h1 class="display-4 fw-bold mb-4">Welcome Back!</h1>
            <p class="lead mb-4">Access your personalized dental education dashboard and stay updated with the latest resources.</p>
            <div class="d-flex gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-info"></i>
                    <span>Expert Content</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-info"></i>
                    <span>Community Access</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Side -->
    <div class="auth-form-side">
        <a href="/index.php" class="auth-back-link">
            <i class="bi bi-arrow-left"></i> Back to Home
        </a>

        <div class="auth-form-container">
            <div class="text-center mb-5">
                <div class="d-inline-flex p-3 mb-3">
                    <i class="bi bi-person-circle text-primary" style="font-size: 2rem;"></i>
                </div>
                <h2 class="fw-bold text-primary">Sign In</h2>
                <p class="text-muted">Please login to continue to your account.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <div><?php echo $error; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-4">
                    <label for="username" class="form-label fw-medium text-muted">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0 bg-light" id="username" name="username" required autofocus placeholder="Enter your username">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fw-medium text-muted">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0 bg-light" id="password" name="password" required placeholder="Enter your password">
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember">
                        <label class="form-check-label text-muted" for="remember">Remember me</label>
                    </div>
                    <a href="#" class="text-decoration-none small">Forgot Password?</a>
                </div>

                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">Sign In</button>
                </div>
            </form>

            <div class="text-center">
                <p class="mb-0 text-muted">Don't have an account? <a href="/register.php" class="text-decoration-none fw-semibold">Create Account</a></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
