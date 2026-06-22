<?php
require_once 'config/config.php';

$page_title = 'Community Forum';
$pdo = getDBConnection();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * ITEMS_PER_PAGE;
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

$where_conditions = ['1=1'];
$params = [];

if ($category_id) {
    $where_conditions[] = 't.category_id = ?';
    $params[] = $category_id;
}

$where_clause = implode(' AND ', $where_conditions);

$count_query = "SELECT COUNT(*) FROM forum_topics t WHERE $where_clause";
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_items = $stmt->fetchColumn();
$total_pages = ceil($total_items / ITEMS_PER_PAGE);

$query = "SELECT t.*, u.username, u.full_name, c.name as category_name,
          (SELECT COUNT(*) FROM forum_replies WHERE topic_id = t.id) as reply_count,
          (SELECT created_at FROM forum_replies WHERE topic_id = t.id ORDER BY created_at DESC LIMIT 1) as last_reply
          FROM forum_topics t
          LEFT JOIN users u ON t.user_id = u.id
          LEFT JOIN categories c ON t.category_id = c.id
          WHERE $where_clause
          ORDER BY t.is_pinned DESC, t.updated_at DESC
          LIMIT " . (int)ITEMS_PER_PAGE . " OFFSET " . (int)$offset;
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$topics = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY display_order")->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row mb-5 align-items-end">
        <div class="col-md-8">
            <h6 class="text-success fw-bold text-uppercase letter-spacing-2 mb-2">Community</h6>
            <h1 class="display-5 fw-bold mb-3">Discussion Forum</h1>
            <p class="lead text-muted mb-0">Connect with the community, share experiences, and get expert advice.</p>
        </div>
        <div class="col-md-4 text-md-end mt-4 mt-md-0">
            <?php if (isLoggedIn()): ?>
                <a href="/forum-new-topic.php" class="btn btn-success rounded-pill px-4 py-2 shadow-sm hover-lift fw-bold">
                    <i class="bi bi-plus-lg me-2"></i> New Topic
                </a>
            <?php else: ?>
                <a href="/login.php" class="btn btn-outline-success rounded-pill px-4 py-2 fw-bold">
                    Login to Post
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="mb-5 overflow-auto pb-2">
        <div class="d-flex gap-2">
            <a href="/forum.php" class="btn rounded-pill px-4 fw-medium <?php echo !$category_id ? 'btn-success shadow-sm' : 'btn-light text-muted'; ?>">All Topics</a>
            <?php foreach ($categories as $cat): ?>
                <a href="/forum.php?category=<?php echo $cat['id']; ?>" 
                   class="btn rounded-pill px-4 fw-medium <?php echo $category_id == $cat['id'] ? 'btn-success shadow-sm' : 'btn-light text-muted'; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($topics)): ?>
        <div class="text-center py-5">
            <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                <i class="bi bi-chat-dots text-muted" style="font-size: 3rem;"></i>
            </div>
            <h3 class="fw-bold text-muted">No topics found</h3>
            <p class="text-muted">Be the first to start a discussion in this category!</p>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-3 mb-5">
            <?php foreach ($topics as $topic): ?>
                <a href="/forum-topic.php?id=<?php echo $topic['id']; ?>" class="text-decoration-none">
                    <div class="card border-0 shadow-sm hover-lift transition-all <?php echo $topic['is_pinned'] ? 'border-start border-4 border-success' : ''; ?>">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-lg-8 mb-3 mb-lg-0">
                                    <div class="d-flex align-items-center mb-2 gap-2">
                                        <?php if ($topic['is_pinned']): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-pin-angle-fill me-1"></i> Pinned</span>
                                        <?php endif; ?>
                                        <span class="badge bg-light text-secondary border"><?php echo htmlspecialchars($topic['category_name']); ?></span>
                                        <?php if ($topic['is_locked']): ?>
                                            <span class="badge bg-secondary"><i class="bi bi-lock-fill me-1"></i> Locked</span>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($topic['title']); ?></h5>
                                    <p class="text-muted small mb-3 text-truncate">
                                        <?php echo htmlspecialchars(substr($topic['content'], 0, 150)); ?>...
                                    </p>
                                    <div class="d-flex align-items-center text-muted small">
                                        <div class="d-flex align-items-center me-3">
                                            <div class="bg-light rounded-circle p-1 me-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                <i class="bi bi-person-fill text-secondary" style="font-size: 12px;"></i>
                                            </div>
                                            <span class="fw-medium text-dark"><?php echo htmlspecialchars($topic['username']); ?></span>
                                        </div>
                                        <span class="me-3">&bull;</span>
                                        <span><?php echo timeAgo($topic['created_at']); ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="d-flex justify-content-lg-end gap-3 text-center">
                                        <div class="bg-light rounded-3 p-2 px-3 min-w-80">
                                            <div class="h5 fw-bold text-dark mb-0"><?php echo $topic['reply_count']; ?></div>
                                            <div class="small text-muted">Replies</div>
                                        </div>
                                        <div class="bg-light rounded-3 p-2 px-3 min-w-80">
                                            <div class="h5 fw-bold text-dark mb-0"><?php echo $topic['views']; ?></div>
                                            <div class="small text-muted">Views</div>
                                        </div>
                                    </div>
                                    <?php if ($topic['last_reply']): ?>
                                        <div class="text-lg-end mt-3 small text-muted">
                                            <i class="bi bi-reply-fill me-1"></i> Last reply <?php echo timeAgo($topic['last_reply']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <nav>
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

<style>
.min-w-80 {
    min-width: 80px;
}
</style>

<?php include 'includes/footer.php'; ?>
