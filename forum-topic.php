<?php
require_once 'config/config.php';

$pdo = getDBConnection();
$topic_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$topic_id) {
    header('Location: /forum.php');
    exit;
}

$stmt = $pdo->prepare("SELECT t.*, u.username, u.full_name, u.role, c.name as category_name 
                       FROM forum_topics t
                       LEFT JOIN users u ON t.user_id = u.id
                       LEFT JOIN categories c ON t.category_id = c.id
                       WHERE t.id = ?");
$stmt->execute([$topic_id]);
$topic = $stmt->fetch();

if (!$topic) {
    header('Location: /forum.php');
    exit;
}

$stmt = $pdo->prepare("UPDATE forum_topics SET views = views + 1 WHERE id = ?");
$stmt->execute([$topic_id]);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn() && !$topic['is_locked']) {
    $content = trim($_POST['content'] ?? '');
    
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO forum_replies (topic_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$topic_id, $_SESSION['user_id'], $content]);
        
        $stmt = $pdo->prepare("UPDATE forum_topics SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$topic_id]);
        
        header("Location: /forum-topic.php?id=$topic_id");
        exit;
    }
}

$stmt = $pdo->prepare("SELECT r.*, u.username, u.full_name, u.role 
                       FROM forum_replies r
                       LEFT JOIN users u ON r.user_id = u.id
                       WHERE r.topic_id = ?
                       ORDER BY r.created_at ASC");
$stmt->execute([$topic_id]);
$replies = $stmt->fetchAll();

$page_title = $topic['title'];
include 'includes/header.php';
?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="/forum.php">Forum</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($topic['title']); ?></li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-light text-dark"><?php echo htmlspecialchars($topic['category_name']); ?></span>
                    <?php if ($topic['is_pinned']): ?>
                        <span class="badge bg-warning text-dark">Pinned</span>
                    <?php endif; ?>
                    <?php if ($topic['is_locked']): ?>
                        <span class="badge bg-danger">Locked</span>
                    <?php endif; ?>
                </div>
                <div class="text-white-50 small">
                    <i class="bi bi-eye"></i> <?php echo $topic['views'] + 1; ?> views
                </div>
            </div>
        </div>
        <div class="card-body">
            <h2 class="card-title"><?php echo htmlspecialchars($topic['title']); ?></h2>
            <div class="mt-3">
                <?php echo nl2br(htmlspecialchars($topic['content'])); ?>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-circle text-success" style="font-size: 2rem;"></i>
                <div class="ms-3">
                    <strong><?php echo htmlspecialchars($topic['full_name']); ?></strong>
                    <?php if ($topic['role'] === 'admin' ): ?>
                        <span class="badge bg-primary"><?php echo htmlspecialchars($topic['role']); ?> </span>
                    <?php endif; ?>
                    <?php if ($topic['role'] === 'doctor'): ?>
                        <span class="badge bg-success"><?php echo htmlspecialchars($topic['role']); ?> </span>
                    <?php endif; ?>
                    <br>
                    <small class="text-muted">Posted <?php echo timeAgo($topic['created_at']); ?></small>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-3">Replies (<?php echo count($replies); ?>)</h4>

    <?php foreach ($replies as $reply): ?>
        <div class="card mb-3 <?php echo $reply['is_verified'] ? 'border-primary' : ''; ?>">
            <?php if ($reply['is_verified']): ?>
                <div class="card-header bg-primary text-white small">
                    <i class="bi bi-check-circle-fill"></i> Verified Answer by Medical Professional
                </div>
            <?php endif; ?>
            <div class="card-body">
                <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle text-muted" style="font-size: 1.5rem;"></i>
                    <div class="ms-2">
                        <strong><?php echo htmlspecialchars($reply['full_name']); ?></strong>
                        <?php if ($reply['role'] === 'admin' ): ?>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($reply['role']); ?> </span>
                        <?php endif; ?>
                        <?php if ($reply['role'] === 'doctor'): ?>
                            <span class="badge bg-success"><?php echo htmlspecialchars($reply['role']); ?> </span>
                        <?php endif; ?>
                        <br>
                        <small class="text-muted"><?php echo timeAgo($reply['created_at']); ?></small>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (isLoggedIn() && !$topic['is_locked']): ?>
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Post a Reply</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <textarea name="content" class="form-control" rows="5" 
                                  placeholder="Write your reply here..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-send"></i> Post Reply
                    </button>
                </form>
            </div>
        </div>
    <?php elseif ($topic['is_locked']): ?>
        <div class="alert alert-warning">
            <i class="bi bi-lock"></i> This topic is locked and cannot receive new replies.
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Please <a href="/login.php">login</a> to post a reply.
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="/forum.php" class="btn btn-outline-success">
            <i class="bi bi-arrow-left"></i> Back to Forum
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
