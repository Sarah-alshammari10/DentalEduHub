<?php
require_once 'config/config.php';

$page_title = 'Register';
$error = '';
$success = '';

if (isLoggedIn()) {
    header('Location: /index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $full_name = sanitize($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($username) || empty($email) || empty($full_name) || empty($password)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        $pdo = getDBConnection();
        
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            $error = 'Username or email already exists';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, 'patient')");
            
            if ($stmt->execute([$username, $email, $password_hash, $full_name])) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
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
            <h1 class="display-4 fw-bold mb-4">Join Our Community</h1>
            <p class="lead mb-4">Create an account to access exclusive dental education content, participate in forums, and track your learning journey.</p>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                        <i class="bi bi-journal-text text-white"></i>
                    </div>
                    <span>Access Premium Articles</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                        <i class="bi bi-people text-white"></i>
                    </div>
                    <span>Connect with Experts</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                        <i class="bi bi-robot text-white"></i>
                    </div>
                    <span>AI Health Assistant</span>
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
            <div class="text-center mb-4">
                <div class="d-inline-flex p-3 mb-3">
                    <i class="bi bi-person-plus-fill text-primary" style="font-size: 2rem;"></i>
                </div>
                <h2 class="fw-bold text-primary">Create Account</h2>
                <p class="text-muted">Join us today! It takes less than a minute.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <div><?php echo $error; ?></div>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div><?php echo $success; ?></div>
                </div>
                <div class="text-center mt-4">
                    <a href="/login.php" class="btn btn-primary btn-lg shadow-sm px-5">Go to Login</a>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label fw-medium text-muted">Full Name</label>
                            <input type="text" class="form-control bg-light" id="full_name" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required placeholder="John Doe">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label fw-medium text-muted">Username</label>
                            <input type="text" class="form-control bg-light" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required placeholder="johndoe">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium text-muted">Email Address</label>
                        <input type="email" class="form-control bg-light" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required placeholder="john@example.com">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="password" class="form-label fw-medium text-muted">Password</label>
                            <input type="password" class="form-control bg-light" id="password" name="password" required>
                            <small class="text-muted" style="font-size: 0.75rem;">Min 6 chars</small>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="confirm_password" class="form-label fw-medium text-muted">Confirm Password</label>
                            <input type="password" class="form-control bg-light" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">Create Account</button>
                    </div>
                </form>
                
                <div class="text-center">
                    <p class="mb-0 text-muted">Already have an account? <a href="/login.php" class="text-decoration-none fw-semibold">Sign In</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
