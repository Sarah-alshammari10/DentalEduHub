<?php
require_once 'config/config.php';

$page_title = 'Home';
$pdo = getDBConnection();

// Get latest articles
$stmt = $pdo->prepare("SELECT a.*, c.name as category_name, u.full_name as author_name 
                       FROM articles a 
                       LEFT JOIN categories c ON a.category_id = c.id 
                       LEFT JOIN users u ON a.author_id = u.id 
                       WHERE a.is_published = true 
                       ORDER BY a.created_at DESC LIMIT 4");
$stmt->execute();
$latest_articles = $stmt->fetchAll();

// Get latest videos
$stmt = $pdo->prepare("SELECT v.*, c.name as category_name 
                       FROM videos v 
                       LEFT JOIN categories c ON v.category_id = c.id 
                       WHERE v.is_published = true 
                       ORDER BY v.created_at DESC LIMIT 4");
$stmt->execute();
$latest_videos = $stmt->fetchAll();

// Get categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY display_order");
$categories = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="hero-section mb-5">
    <div class="container">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6">
                <span class="badge bg-white text-primary mb-3 px-3 py-2 shadow-sm rounded-pill"><i class="bi bi-stars me-1"></i> Premium Dental Care</span>
                <h1 class="display-3 fw-bold mb-4 text-white lh-sm">Your Smile, Our <span class="text-warning">Passion</span></h1>
                <p class="lead mb-5 text-white-50" style="max-width: 500px;">Experience world-class dental education and care. We provide expert resources, engaging videos, and a supportive community for your oral health journey.</p>
                <div class="d-flex flex-wrap gap-3">
                    <?php if (!isLoggedIn()): ?>
                        <a href="/register.php" class="btn btn-light btn-lg shadow-lg px-4 py-3 fw-bold">Get Started <i class="bi bi-arrow-right ms-2"></i></a>
                    <?php endif; ?>
                    <a href="/articles.php" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold">Explore Articles</a>
                </div>
                
                <div class="mt-5 d-flex align-items-center gap-4">
                    <div class="d-flex">
                        <div class="bg-white rounded-circle border border-2 border-white" style="width: 40px; height: 40px; background-image: url('https://i.pravatar.cc/100?img=1'); background-size: cover;"></div>
                        <div class="bg-white rounded-circle border border-2 border-white ms-n3" style="width: 40px; height: 40px; background-image: url('https://i.pravatar.cc/100?img=2'); background-size: cover; margin-left: -15px;"></div>
                        <div class="bg-white rounded-circle border border-2 border-white ms-n3" style="width: 40px; height: 40px; background-image: url('https://i.pravatar.cc/100?img=3'); background-size: cover; margin-left: -15px;"></div>
                        <div class="bg-primary rounded-circle border border-2 border-white ms-n3 d-flex align-items-center justify-content-center text-white small fw-bold" style="width: 40px; height: 40px; margin-left: -15px;">+2k</div>
                    </div>
                    <div class="text-white-50 small">
                        <div class="fw-bold text-white">2,000+ Members</div>
                        Joined our community
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="position-relative">
                    <div class="glass-card p-2 rounded-4 rotate-3" style="transform: rotate(2deg);">
                        <img src="Image1.jpeg" alt="Hail Dental Center Team" class="img-fluid rounded-4 shadow-lg w-100" style="object-fit: cover; min-height: 400px;">
                    </div>
                    <!-- Floating Badge -->
                    <div class="position-absolute bottom-0 start-0 translate-middle-x mb-5 ms-5 glass-panel p-3 rounded-4 shadow-lg d-none d-md-block animate-float">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 p-2 rounded-circle text-success">
                                <i class="bi bi-shield-check fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Verified Content</div>
                                <div class="small text-muted">By Dental Experts</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Categories Section -->
    <section class="mb-6">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2 rounded-pill">
                <i class="bi bi-grid-3x3-gap me-1"></i> Categories
            </span>
            <h2 class="fw-bold display-5 mb-3">Explore by Category</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Browse through our comprehensive collection of dental topics and find exactly what you're looking for</p>
        </div>
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <div class="col-md-6 col-lg-3">
                    <a href="/articles.php?category=<?php echo $category['id']; ?>" class="text-decoration-none">
                        <div class="card glass-card border-0 shadow-sm h-100 text-center p-4 hover-lift position-relative overflow-hidden">
                            <!-- Background Icon -->
                            <i class="bi bi-<?php echo htmlspecialchars($category['icon'] ?? 'folder'); ?> position-absolute top-0 end-0 me-n3 mt-n2 opacity-5" style="font-size: 5rem;"></i>
                            
                            <!-- Content -->
                            <div class="position-relative z-1">
                                <div class="mb-3 mx-auto d-inline-flex align-items-center justify-content-center rounded-circle bg-gradient-primary text-white shadow-sm" style="width: 64px; height: 64px;">
                                    <i class="bi bi-<?php echo htmlspecialchars($category['icon'] ?? 'folder'); ?> fs-3"></i>
                                </div>
                                <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($category['name']); ?></h6>
                                <p class="text-muted small mb-0 text-truncate-2" style="font-size: 0.85rem;"><?php echo htmlspecialchars($category['description']); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Latest Articles -->
    <section class="mb-6">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h6 class="text-primary fw-bold text-uppercase letter-spacing-2">Read</h6>
                <h2 class="fw-bold display-6 mb-0">Latest Articles</h2>
            </div>
            <a href="/articles.php" class="btn btn-outline-primary rounded-pill px-4">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach ($latest_articles as $article): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm hover-lift overflow-hidden">
                        <div class="position-relative">
                            <?php if ($article['featured_image']): ?>
                                <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($article['title']); ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-file-text text-muted opacity-25" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-white text-primary shadow-sm"><?php echo htmlspecialchars($article['category_name']); ?></span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-2 text-truncate-2"><?php echo htmlspecialchars($article['title']); ?></h5>
                            <p class="card-text text-muted small mb-3"><?php echo htmlspecialchars(substr($article['summary'] ?? $article['content'], 0, 80)); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <small class="text-muted"><i class="bi bi-eye me-1"></i> <?php echo $article['views']; ?></small>
                                <a href="/article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="text-primary fw-bold text-decoration-none small">Read More <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Latest Videos -->
    <section class="mb-6">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h6 class="text-danger fw-bold text-uppercase letter-spacing-2">Watch</h6>
                <h2 class="fw-bold display-6 mb-0">Latest Videos</h2>
            </div>
            <a href="/videos.php" class="btn btn-outline-danger rounded-pill px-4">View All <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4 pb-5">
            <?php foreach ($latest_videos as $video): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm hover-lift overflow-hidden">
                        <div class="position-relative">
                            <?php if ($video['thumbnail']): ?>
                                <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($video['title']); ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-dark d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-play-circle-fill text-danger opacity-75" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <div class="bg-white rounded-circle p-2 shadow-lg d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-play-fill text-danger fs-3 ms-1"></i>
                                </div>
                            </div>
                            <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75"><?php echo htmlspecialchars($video['duration'] ?? '00:00'); ?></span>
                        </div>
                        <div class="card-body">
                            <span class="badge bg-danger bg-opacity-10 text-danger mb-2"><?php echo htmlspecialchars($video['category_name']); ?></span>
                            <h5 class="card-title fw-bold text-truncate-2"><?php echo htmlspecialchars($video['title']); ?></h5>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="bi bi-eye me-1"></i> <?php echo $video['views']; ?> views</small>
                                <a href="/video.php?slug=<?php echo htmlspecialchars($video['slug']); ?>" class="btn btn-sm btn-danger rounded-pill px-3">Watch Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="mb-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden text-white" style="background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);">
                    <div class="card-body text-center p-5 position-relative">
                        <i class="bi bi-question-circle opacity-25 position-absolute top-0 start-0 m-3" style="font-size: 8rem;"></i>
                        <div class="position-relative z-1">
                            <i class="bi bi-question-circle-fill mb-3 d-block" style="font-size: 3rem;"></i>
                            <h3 class="fw-bold">Have Questions?</h3>
                            <p class="mb-4 opacity-75">Find answers to common dental health questions in our FAQ section.</p>
                            <a href="/faq.php" class="btn btn-light rounded-pill px-4 fw-bold text-primary">View FAQ</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="card-body text-center p-5 position-relative">
                        <i class="bi bi-people opacity-25 position-absolute top-0 start-0 m-3" style="font-size: 8rem;"></i>
                        <div class="position-relative z-1">
                            <i class="bi bi-chat-dots-fill mb-3 d-block" style="font-size: 3rem;"></i>
                            <h3 class="fw-bold">Join Community</h3>
                            <p class="mb-4 opacity-75">Connect with others, share experiences, and get support.</p>
                            <a href="/forum.php" class="btn btn-light rounded-pill px-4 fw-bold text-success">Visit Forum</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden text-dark" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="card-body text-center p-5 position-relative text-white">
                        <i class="bi bi-lightbulb opacity-25 position-absolute top-0 start-0 m-3" style="font-size: 8rem;"></i>
                        <div class="position-relative z-1">
                            <i class="bi bi-lightbulb-fill mb-3 d-block" style="font-size: 3rem;"></i>
                            <h3 class="fw-bold">Daily Tips</h3>
                            <p class="mb-4 opacity-75">Discover daily habits for maintaining a healthy and bright smile.</p>
                            <a href="/articles.php?category=4" class="btn btn-light rounded-pill px-4 fw-bold text-warning">Read Tips</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>