<?php
require_once 'config/config.php';

$pdo = getDBConnection();
$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: /videos.php');
    exit;
}

$stmt = $pdo->prepare("SELECT v.*, c.name as category_name 
                       FROM videos v 
                       LEFT JOIN categories c ON v.category_id = c.id 
                       WHERE v.slug = ? AND v.is_published = true");
$stmt->execute([$slug]);
$video = $stmt->fetch();

if (!$video) {
    header('Location: /videos.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE videos SET views = views + 1 WHERE id = ?");
$stmt->execute([$video['id']]);

$stmt = $pdo->prepare("SELECT * FROM videos 
                       WHERE category_id = ? AND id != ? AND is_published = true 
                       ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$video['category_id'], $video['id']]);
$related_videos = $stmt->fetchAll();

$page_title = $video['title'];
include 'includes/header.php';
?>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="/videos.php" class="text-decoration-none text-muted">Videos</a></li>
            <li class="breadcrumb-item active text-danger" aria-current="page"><?php echo htmlspecialchars($video['title']); ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-8">
            <div class="mb-4">
                <span class="badge bg-danger bg-opacity-10 text-danger mb-3 px-3 py-2 rounded-pill"><?php echo htmlspecialchars($video['category_name']); ?></span>
                <h1 class="display-5 fw-bold mb-3 text-dark lh-sm"><?php echo htmlspecialchars($video['title']); ?></h1>
                <div class="d-flex align-items-center text-muted small gap-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar3 me-2 text-danger"></i>
                        <span><?php echo formatDate($video['created_at']); ?></span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-eye me-2 text-danger"></i>
                        <span><?php echo $video['views'] + 1; ?> views</span>
                    </div>
                    <?php if ($video['duration']): ?>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock me-2 text-danger"></i>
                            <span><?php echo htmlspecialchars($video['duration']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ratio ratio-16x9 mb-5 shadow-lg rounded-4 overflow-hidden">
                <iframe src="<?php echo htmlspecialchars($video['video_url']); ?>" 
                        allowfullscreen 
                        class="rounded-4"></iframe>
            </div>

            <div class="card glass-card border-0 p-4 mb-5">
                <h5 class="fw-bold mb-3 text-dark">About this video</h5>
                <p class="text-secondary lh-lg mb-0"><?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="/videos.php" class="btn btn-outline-danger rounded-pill px-4 fw-medium">
                    <i class="bi bi-arrow-left me-2"></i> Back to Videos
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0 text-danger">Related Videos</h5>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        <?php if (empty($related_videos)): ?>
                            <div class="text-muted small fst-italic">No related videos found</div>
                        <?php else: ?>
                            <?php foreach ($related_videos as $related): ?>
                                <a href="/video.php?slug=<?php echo htmlspecialchars($related['slug']); ?>" 
                                   class="list-group-item list-group-item-action border-0 px-0 py-3">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-4">
                                            <div class="position-relative rounded overflow-hidden ratio ratio-16x9">
                                                <?php if ($related['thumbnail']): ?>
                                                    <img src="<?php echo htmlspecialchars($related['thumbnail']); ?>" 
                                                         class="img-fluid object-fit-cover" 
                                                         alt="<?php echo htmlspecialchars($related['title']); ?>">
                                                <?php else: ?>
                                                    <div class="bg-dark d-flex align-items-center justify-content-center h-100">
                                                        <i class="bi bi-play-fill text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <h6 class="mb-1 fw-bold text-dark small text-truncate-2"><?php echo htmlspecialchars($related['title']); ?></h6>
                                            <small class="text-muted d-block">
                                                <?php if ($related['duration']): ?>
                                                    <i class="bi bi-clock me-1"></i> <?php echo htmlspecialchars($related['duration']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
