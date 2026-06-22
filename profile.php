<?php
require_once 'config/config.php';

requireLogin();

$page_title = 'My Profile';
$pdo = getDBConnection();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM forum_topics WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$topic_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM forum_replies WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$reply_count = $stmt->fetchColumn();

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-4">
            <div class="card glass-card border-0 text-center p-4 mb-4">
                <div class="position-relative d-inline-block mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-flex">
                        <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>
                    </div>
                    <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success border border-2 border-white p-2">
                        <span class="visually-hidden">Online</span>
                    </span>
                </div>
                <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                <p class="text-muted mb-3">@<?php echo htmlspecialchars($user['username']); ?></p>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-4"><?php echo ucfirst($user['role']); ?></span>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <button class="btn btn-primary rounded-pill px-4 shadow-sm hover-lift">Edit Profile</button>
                    <button class="btn btn-outline-primary rounded-pill px-4 hover-lift">Settings</button>
                </div>

                <hr class="border-secondary border-opacity-10 my-4">
                
                <div class="text-start px-2">
                    <div class="d-flex align-items-center mb-3 text-muted">
                        <div class="bg-light rounded-circle p-2 me-3">
                            <i class="bi bi-envelope text-primary"></i>
                        </div>
                        <div>
                            <small class="d-block text-uppercase fw-bold opacity-75" style="font-size: 0.7rem;">Email Address</small>
                            <span class="text-dark fw-medium"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3 text-muted">
                        <div class="bg-light rounded-circle p-2 me-3">
                            <i class="bi bi-calendar3 text-primary"></i>
                        </div>
                        <div>
                            <small class="d-block text-uppercase fw-bold opacity-75" style="font-size: 0.7rem;">Joined Date</small>
                            <span class="text-dark fw-medium"><?php echo formatDate($user['created_at']); ?></span>
                        </div>
                    </div>
                    <?php if ($user['last_login']): ?>
                        <div class="d-flex align-items-center text-muted">
                            <div class="bg-light rounded-circle p-2 me-3">
                                <i class="bi bi-clock-history text-primary"></i>
                            </div>
                            <div>
                                <small class="d-block text-uppercase fw-bold opacity-75" style="font-size: 0.7rem;">Last Login</small>
                                <span class="text-dark fw-medium"><?php echo timeAgo($user['last_login']); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm hover-lift h-100 overflow-hidden">
                        <div class="card-body p-4 position-relative">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h2 class="display-4 fw-bold text-success mb-0"><?php echo $topic_count; ?></h2>
                                    <p class="text-muted mb-0">Topics Created</p>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-chat-square-text text-success fs-4"></i>
                                </div>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm hover-lift h-100 overflow-hidden">
                        <div class="card-body p-4 position-relative">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h2 class="display-4 fw-bold text-primary mb-0"><?php echo $reply_count; ?></h2>
                                    <p class="text-muted mb-0">Replies Posted</p>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-reply-all text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0 text-dark">Quick Actions</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="/articles.php" class="card h-100 border-0 bg-light hover-lift text-decoration-none p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white rounded-circle p-2 shadow-sm me-3 text-primary">
                                        <i class="bi bi-file-text fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">Browse Articles</h6>
                                        <small class="text-muted">Read expert content</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="/videos.php" class="card h-100 border-0 bg-light hover-lift text-decoration-none p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white rounded-circle p-2 shadow-sm me-3 text-danger">
                                        <i class="bi bi-play-circle fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">Watch Videos</h6>
                                        <small class="text-muted">Visual learning</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="/forum.php" class="card h-100 border-0 bg-light hover-lift text-decoration-none p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white rounded-circle p-2 shadow-sm me-3 text-success">
                                        <i class="bi bi-chat-dots fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">Visit Forum</h6>
                                        <small class="text-muted">Join discussions</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="/faq.php" class="card h-100 border-0 bg-light hover-lift text-decoration-none p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white rounded-circle p-2 shadow-sm me-3 text-info">
                                        <i class="bi bi-question-circle fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">View FAQ</h6>
                                        <small class="text-muted">Get answers</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
