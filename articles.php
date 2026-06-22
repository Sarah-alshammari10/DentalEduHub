<?php
require_once 'config/config.php';

$page_title = 'Articles';
$pdo = getDBConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * ITEMS_PER_PAGE;

// Category filter
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$where_conditions = ['a.is_published = true'];
$params = [];

if ($category_id) {
    $where_conditions[] = 'a.category_id = ?';
    $params[] = $category_id;
}

if ($search) {
    $where_conditions[] = '(a.title LIKE ? OR a.content LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = implode(' AND ', $where_conditions);

// Get total count
$count_query = "SELECT COUNT(*) FROM articles a WHERE $where_clause";
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_items = $stmt->fetchColumn();
$total_pages = ceil($total_items / ITEMS_PER_PAGE);

// Get articles
$query = "SELECT a.*, c.name as category_name, u.full_name as author_name 
          FROM articles a 
          LEFT JOIN categories c ON a.category_id = c.id 
          LEFT JOIN users u ON a.author_id = u.id 
          WHERE $where_clause 
          ORDER BY a.created_at DESC 
          LIMIT " . (int)ITEMS_PER_PAGE . " OFFSET " . (int)$offset;
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Get categories for filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY display_order")->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row mb-5 align-items-end">
        <div class="col-md-8">
            <h6 class="text-primary fw-bold text-uppercase letter-spacing-2 mb-2">Knowledge Base</h6>
            <h1 class="display-5 fw-bold mb-3">Articles & Resources</h1>
            <p class="lead text-muted mb-0">Explore our collection of expert-verified articles on oral health.</p>
        </div>
        <div class="col-md-4 mt-4 mt-md-0">
            <form method="GET" action="" class="position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" name="search" class="form-control ps-5 rounded-pill py-2 border-0 shadow-sm" placeholder="Search articles..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="mb-5 overflow-auto pb-2">
        <div class="d-flex gap-2">
            <a href="/articles.php" class="btn rounded-pill px-4 fw-medium <?php echo !$category_id ? 'btn-primary shadow-sm' : 'btn-light text-muted'; ?>">All Topics</a>
            <?php foreach ($categories as $cat): ?>
                <a href="/articles.php?category=<?php echo $cat['id']; ?>" 
                   class="btn rounded-pill px-4 fw-medium <?php echo $category_id == $cat['id'] ? 'btn-primary shadow-sm' : 'btn-light text-muted'; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($articles)): ?>
        <div class="text-center py-5">
            <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
            </div>
            <h3 class="fw-bold text-muted">No articles found</h3>
            <p class="text-muted">Try adjusting your search or filter to find what you're looking for.</p>
            <?php if ($search || $category_id): ?>
                <a href="/articles.php" class="btn btn-outline-primary rounded-pill mt-2">Clear Filters</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row g-4 mb-5">
            <?php foreach ($articles as $article): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-lift overflow-hidden">
                        <div class="position-relative">
                            <?php if ($article['featured_image']): ?>
                                <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($article['title']); ?>" style="height: 220px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 220px;">
                                    <i class="bi bi-file-text text-muted opacity-25" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                            <span class="position-absolute top-0 end-0 m-3 badge bg-white text-primary shadow-sm"><?php echo htmlspecialchars($article['category_name']); ?></span>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3 text-muted small">
                                <div class="d-flex align-items-center me-3">
                                    <i class="bi bi-person-circle me-1 text-primary"></i>
                                    <?php echo htmlspecialchars($article['author_name'] ?? 'Admin'); ?>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar3 me-1 text-primary"></i>
                                    <?php echo formatDate($article['created_at']); ?>
                                </div>
                            </div>
                            <h5 class="card-title fw-bold mb-3 text-truncate-2 lh-base">
                                <a href="/article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>" class="text-dark text-decoration-none stretched-link">
                                    <?php echo htmlspecialchars($article['title']); ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted small mb-0"><?php echo htmlspecialchars(substr($article['summary'] ?? $article['content'], 0, 120)); ?>...</p>
                        </div>
                        <div class="card-footer bg-white border-0 p-4 pt-0">
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <small class="text-muted fw-medium"><i class="bi bi-eye me-1"></i> <?php echo $article['views']; ?> views</small>
                                <span class="text-primary fw-bold small">Read Article <i class="bi bi-arrow-right ms-1"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav class="mb-5">
                <ul class="pagination justify-content-center gap-2">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" href="?page=<?php echo $page - 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><i class="bi bi-chevron-left"></i></a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" href="?page=<?php echo $i; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" href="?page=<?php echo $page + 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><i class="bi bi-chevron-right"></i></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
