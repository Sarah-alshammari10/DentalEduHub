<?php
require_once 'config/config.php';

requireLogin();

$page_title = 'New Topic';
$pdo = getDBConnection();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    
    if (empty($title) || empty($content)) {
        $error = 'Please fill in all fields';
    } elseif (!$category_id) {
        $error = 'Please select a category';
    } else {
        $stmt = $pdo->prepare("INSERT INTO forum_topics (user_id, category_id, title, content) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$_SESSION['user_id'], $category_id, $title, $content])) {
            $topic_id = $pdo->lastInsertId();
            header("Location: /forum-topic.php?id=$topic_id");
            exit;
        } else {
            $error = 'Failed to create topic. Please try again.';
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY display_order")->fetchAll();

include 'includes/header.php';
?>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="/forum.php">Forum</a></li>
            <li class="breadcrumb-item active">New Topic</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Topic</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Select a category...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Topic Title</label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   placeholder="What would you like to discuss?" 
                                   value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea name="content" id="content" class="form-control" rows="8" 
                                      placeholder="Describe your question or topic in detail..." required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/forum.php" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send"></i> Create Topic
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
