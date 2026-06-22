<?php
require_once 'config/config.php';

$page_title = 'Videos';
$pdo = getDBConnection();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * ITEMS_PER_PAGE;
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

$where_conditions = ['v.is_published = true'];
$params = [];

if ($category_id) {
    $where_conditions[] = 'v.category_id = ?';
    $params[] = $category_id;
}

$where_clause = implode(' AND ', $where_conditions);

$count_query = "SELECT COUNT(*) FROM videos v WHERE $where_clause";
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_items = $stmt->fetchColumn();
$total_pages = ceil($total_items / ITEMS_PER_PAGE);

$query = "SELECT v.*, c.name as category_name 
          FROM videos v 
          LEFT JOIN categories c ON v.category_id = c.id 
          WHERE $where_clause 
          ORDER BY v.created_at DESC 
          LIMIT " . (int)ITEMS_PER_PAGE . " OFFSET " . (int)$offset;
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$videos = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY display_order")->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row mb-5 align-items-end">
        <div class="col-md-8">
            <h6 class="text-danger fw-bold text-uppercase letter-spacing-2 mb-2">Video Library</h6>
            <h1 class="display-5 fw-bold mb-3">Educational Videos</h1>
            <p class="lead text-muted mb-0">Watch helpful videos about oral health and dental care.</p>
        </div>
    </div>

    <div class="mb-5 overflow-auto pb-2">
        <div class="d-flex gap-2">
            <a href="/videos.php" class="btn rounded-pill px-4 fw-medium <?php echo !$category_id ? 'btn-danger shadow-sm' : 'btn-light text-muted'; ?>">All Videos</a>
            <?php foreach ($categories as $cat): ?>
                <a href="/videos.php?category=<?php echo $cat['id']; ?>" 
                   class="btn rounded-pill px-4 fw-medium <?php echo $category_id == $cat['id'] ? 'btn-danger shadow-sm' : 'btn-light text-muted'; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($videos)): ?>
        <div class="text-center py-5">
            <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                <i class="bi bi-play-circle text-muted" style="font-size: 3rem;"></i>
            </div>
            <h3 class="fw-bold text-muted">No videos found</h3>
            <p class="text-muted">Try selecting a different category.</p>
            <?php if ($category_id): ?>
                <a href="/videos.php" class="btn btn-outline-danger rounded-pill mt-2">Clear Filters</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row g-4 mb-5">
            <?php foreach ($videos as $video): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-lift overflow-hidden">
                        <div class="position-relative">
                            <?php if ($video['thumbnail']): ?>
                                <img src="<?php echo htmlspecialchars($video['thumbnail']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($video['title']); ?>" style="height: 220px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-dark d-flex align-items-center justify-content-center" style="height: 220px;">
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
                        <div class="card-body p-4">
                            <span class="badge bg-danger bg-opacity-10 text-danger mb-2"><?php echo htmlspecialchars($video['category_name']); ?></span>
                            <h5 class="card-title fw-bold mb-3 text-truncate-2 lh-base">
                                <a href="/video.php?slug=<?php echo htmlspecialchars($video['slug']); ?>" class="text-dark text-decoration-none stretched-link">
                                    <?php echo htmlspecialchars($video['title']); ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted small mb-0"><?php echo htmlspecialchars(substr($video['description'], 0, 100)); ?>...</p>
                        </div>
                        <div class="card-footer bg-white border-0 p-4 pt-0">
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <small class="text-muted fw-medium"><i class="bi bi-eye me-1"></i> <?php echo $video['views']; ?> views</small>
                                <span class="btn btn-sm btn-danger rounded-pill px-3">Watch Now</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <nav class="mb-5">
                <ul class="pagination justify-content-center gap-2">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" href="?page=<?php echo $page - 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>"><i class="bi bi-chevron-left"></i></a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" href="?page=<?php echo $i; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link border-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" href="?page=<?php echo $page + 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>"><i class="bi bi-chevron-right"></i></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
