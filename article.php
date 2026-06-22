<?php
require_once 'config/config.php';

$pdo = getDBConnection();
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: /articles.php');
    exit;
}

// جلب المقال
$stmt = $pdo->prepare("
    SELECT a.*, c.name AS category_name, u.full_name AS author_name 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    LEFT JOIN users u ON a.author_id = u.id 
    WHERE a.slug = ? AND a.is_published = true
");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: /articles.php');
    exit;
}

// تحديث عدد المشاهدات
$stmt = $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
$stmt->execute([$article['id']]);

// جلب المقالات ذات الصلة
$stmt = $pdo->prepare("
    SELECT * FROM articles 
    WHERE category_id = ? AND id != ? AND is_published = true 
    ORDER BY created_at DESC LIMIT 3
");
$stmt->execute([$article['category_id'], $article['id']]);
$related_articles = $stmt->fetchAll();

$page_title = $article['title'];
include 'includes/header.php';
?>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="/articles.php" class="text-decoration-none text-muted">Articles</a></li>
            <li class="breadcrumb-item active text-primary" aria-current="page"><?php echo htmlspecialchars($article['title']); ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Main Content -->
        <div class="col-lg-8">
            <article class="card glass-card border-0 p-4 p-md-5 mb-5">
                <div class="mb-4">
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2 rounded-pill"><?php echo htmlspecialchars($article['category_name']); ?></span>
                    <h1 class="display-5 fw-bold mb-3 text-dark lh-sm"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <div class="d-flex align-items-center text-muted small gap-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle me-2 text-primary"></i>
                            <span class="fw-medium"><?php echo htmlspecialchars($article['author_name'] ?? 'Admin'); ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar3 me-2 text-primary"></i>
                            <span><?php echo formatDate($article['created_at']); ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-eye me-2 text-primary"></i>
                            <span><?php echo $article['views'] + 1; ?> views</span>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <?php if ($article['featured_image']): ?>
                    <div class="mb-5 rounded-4 overflow-hidden shadow-sm position-relative">
                        <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" 
                             class="img-fluid w-100" 
                             style="object-fit: cover; max-height: 500px;"
                             alt="<?php echo htmlspecialchars($article['title']); ?>">
                    </div>
                <?php endif; ?>

                <!-- Article Content -->
                <div class="article-content fs-5 text-secondary lh-lg mb-5">
                    <?php echo html_entity_decode($article['content']); ?>
                </div>

                <hr class="border-secondary border-opacity-10 my-4">

                <div class="d-flex justify-content-between align-items-center">
                    <a href="/articles.php" class="btn btn-outline-primary rounded-pill px-4 fw-medium">
                        <i class="bi bi-arrow-left me-2"></i> Back to Articles
                    </a>
                    <button class="btn btn-light text-muted rounded-pill px-4 fw-medium hover-lift" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i> Print
                    </button>
                </div>
            </article>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Related Articles -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-4 px-4 ">
                    <h5 class="fw-bold mb-0 ">Related Articles</h5>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        <?php if (empty($related_articles)): ?>
                            <div class="text-muted small fst-italic">No related articles found</div>
                        <?php else: ?>
                            <?php foreach ($related_articles as $related): ?>
                                <a href="/article.php?slug=<?php echo htmlspecialchars($related['slug']); ?>" 
                                   class="list-group-item list-group-item-action border-0 px-0 py-3">
                                    <h6 class="mb-1 fw-bold text-dark"><?php echo htmlspecialchars($related['title']); ?></h6>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i> <?php echo formatDate($related['created_at']); ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card border-0 shadow-sm overflow-hidden bg-primary text-white" style="background: var(--gradient-primary);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Quick Links</h5>
                    <div class="d-grid gap-3">
                        <a href="/videos.php" class="btn btn-white bg-white bg-opacity-10 text-white border-0 text-start py-3 px-4 rounded-3 hover-lift d-flex align-items-center">
                            <i class="bi bi-play-circle fs-4 me-3"></i>
                            <div>
                                <div class="fw-bold">Watch Videos</div>
                                <div class="small opacity-75">Visual learning resources</div>
                            </div>
                        </a>
                        <a href="/faq.php" class="btn btn-white bg-white bg-opacity-10 text-white border-0 text-start py-3 px-4 rounded-3 hover-lift d-flex align-items-center">
                            <i class="bi bi-question-circle fs-4 me-3"></i>
                            <div>
                                <div class="fw-bold">Browse FAQ</div>
                                <div class="small opacity-75">Common questions answered</div>
                            </div>
                        </a>
                        <a href="/forum.php" class="btn btn-white bg-white bg-opacity-10 text-white border-0 text-start py-3 px-4 rounded-3 hover-lift d-flex align-items-center">
                            <i class="bi bi-chat-dots fs-4 me-3"></i>
                            <div>
                                <div class="fw-bold">Join Forum</div>
                                <div class="small opacity-75">Connect with the community</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS لتحسين عرض محتوى المقال -->
<style>
.article-content {
    line-height: 1.7;
}
.article-content img {
    max-width: 100%;
    height: auto;
    margin-bottom: 1rem;
}
.article-content h1, .article-content h2, .article-content h3 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}
.article-content p {
    margin-bottom: 1rem;
}
.article-content ul, .article-content ol {
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}
.article-content blockquote {
    border-left: 4px solid #ddd;
    padding-left: 1rem;
    color: #555;
    font-style: italic;
    margin: 1rem 0;
}
</style>

<?php include 'includes/footer.php'; ?>
