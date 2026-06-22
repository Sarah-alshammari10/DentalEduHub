<?php
require_once 'config/config.php';

$page_title = 'Frequently Asked Questions';
$pdo = getDBConnection();

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

$where_conditions = ['f.is_published = true'];
$params = [];

if ($category_id) {
    $where_conditions[] = 'f.category_id = ?';
    $params[] = $category_id;
}

$where_clause = implode(' AND ', $where_conditions);

$query = "SELECT f.*, c.name as category_name 
          FROM faqs f 
          LEFT JOIN categories c ON f.category_id = c.id 
          WHERE $where_clause 
          ORDER BY f.display_order, f.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$faqs = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY display_order")->fetchAll();

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row mb-5 justify-content-center text-center">
        <div class="col-lg-8">
            <h6 class="text-primary fw-bold text-uppercase letter-spacing-2 mb-2">Help Center</h6>
            <h1 class="display-5 fw-bold mb-3">Frequently Asked Questions</h1>
            <p class="lead text-muted">Find answers to common questions about oral health, dental procedures, and our platform.</p>
        </div>
    </div>

    <div class="mb-5 overflow-auto pb-2 text-center">
        <div class="d-inline-flex gap-2">
            <a href="/faq.php" class="btn rounded-pill px-4 fw-medium <?php echo !$category_id ? 'btn-primary shadow-sm' : 'btn-light text-muted'; ?>">All Questions</a>
            <?php foreach ($categories as $cat): ?>
                <a href="/faq.php?category=<?php echo $cat['id']; ?>" 
                   class="btn rounded-pill px-4 fw-medium <?php echo $category_id == $cat['id'] ? 'btn-primary shadow-sm' : 'btn-light text-muted'; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($faqs)): ?>
        <div class="text-center py-5">
            <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
            </div>
            <h3 class="fw-bold text-muted">No FAQs found</h3>
            <p class="text-muted">Try selecting a different category.</p>
        </div>
    <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion custom-accordion" id="faqAccordion">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                            <h2 class="accordion-header" id="heading<?php echo $faq['id']; ?>">
                                <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?> p-4 fw-bold text-dark bg-white" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?php echo $faq['id']; ?>" 
                                        aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                    <div class="d-flex align-items-center w-100">
                                        <span class="badge bg-primary bg-opacity-10 text-primary me-3 rounded-pill px-3"><?php echo htmlspecialchars($faq['category_name']); ?></span>
                                        <span class="flex-grow-1"><?php echo htmlspecialchars($faq['question']); ?></span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $faq['id']; ?>" 
                                 class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body p-4 bg-white border-top border-light text-secondary lh-lg">
                                    <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center mt-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg overflow-hidden text-center text-white" style="background: var(--gradient-primary);">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-chat-dots-fill fs-1 opacity-50"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Still have questions?</h3>
                    <p class="mb-4 opacity-75 lead">Can't find the answer you're looking for? Join our community forum and ask our experts and other members.</p>
                    <a href="/forum.php" class="btn btn-light text-primary rounded-pill px-5 py-3 fw-bold hover-lift">Visit Community Forum</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-accordion .accordion-button:not(.collapsed) {
    background-color: #fff;
    color: var(--primary-color);
    box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
}
.custom-accordion .accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0,0,0,.125);
}
.custom-accordion .accordion-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230f172a'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}
.custom-accordion .accordion-button:not(.collapsed)::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230ea5e9'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}
</style>

<?php include 'includes/footer.php'; ?>
