<?php
require_once 'config/config.php';
require_once 'config/ai-config.php';

requireLogin();

$page_title = 'AI Dental Assistant';
$response = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question'])) {
    $question = trim($_POST['question']);

    if (empty($question)) {
        $error = 'Please enter a question';
    } else {
        // Prepare the API request
        $prompt = "You are a helpful dental health assistant. Answer the following question about oral and dental health in a clear, simple, and professional manner:\n\n" . $question;

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => AI_TEMPERATURE,
                'maxOutputTokens' => AI_MAX_TOKENS,
            ]
        ];

        $ch = curl_init(GOOGLE_AI_ENDPOINT . '?key=' . GOOGLE_AI_API_KEY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $responseData = json_decode($result, true);
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $response = $responseData['candidates'][0]['content']['parts'][0]['text'];
            } else {
                $error = 'No response received from AI';
            }
        } else {
            $error = 'API request failed. Please check your API key or try again later.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <div class="bg-white rounded-circle d-inline-flex p-4 shadow-sm mb-4 position-relative">
                    <i class="bi bi-robot text-primary" style="font-size: 3.5rem;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success border border-2 border-white">
                        AI
                        <span class="visually-hidden">AI Powered</span>
                    </span>
                </div>
                <h1 class="display-5 fw-bold mb-3">AI Dental Assistant</h1>
                <p class="lead text-muted">Ask questions about oral health and get instant, AI-powered answers.</p>
            </div>

            <div class="card glass-card border-0 p-4 mb-5">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label for="question" class="form-label fw-bold text-dark">Your Question</label>
                            <div class="position-relative">
                                <textarea name="question" id="question" class="form-control form-control-lg border-0 bg-light shadow-inner py-3 ps-4 pe-5" rows="3"
                                    placeholder="Example: What are the best practices for brushing teeth?"
                                    required style="border-radius: 1rem; resize: none;"><?php echo htmlspecialchars($_POST['question'] ?? ''); ?></textarea>
                                <i class="bi bi-chat-text position-absolute top-0 end-0 mt-3 me-3 text-muted opacity-50"></i>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-lg hover-lift py-3 fw-bold">
                                <i class="bi bi-stars me-2"></i> Ask AI Assistant
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center p-4 mb-4">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($response): ?>
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5 animate-slide-up">
                    <div class="card-header bg-primary text-white p-4 border-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                                <i class="bi bi-lightbulb-fill text-white"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">AI Response</h5>
                        </div>
                    </div>
                    <div class="card-body p-4 p-md-5 bg-white">
                        <div class="ai-response">
                            <div id="ai-markdown" style="display:none;"><?php echo htmlspecialchars($response); ?></div>
                            <div id="ai-html" class="fs-5 text-secondary lh-lg"></div>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-4 border-top border-light">
                        <div class="d-flex align-items-start gap-3">
                            <i class="bi bi-info-circle-fill text-primary mt-1"></i>
                            <small class="text-muted">
                                <strong>Disclaimer:</strong> This AI-generated response is for educational purposes only.
                                Always consult with a qualified dentist for medical advice.
                            </small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row g-4 mt-2">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-chat-quote me-2"></i> Example Questions</h5>
                            <div class="d-flex flex-column gap-2">
                                <button onclick="setQuestion('How often should I visit the dentist?'); return false;" class="btn btn-light text-start text-truncate w-100 rounded-pill px-3 py-2 text-muted hover-bg-primary-soft transition-all">
                                    <i class="bi bi-arrow-right-short me-2 text-primary"></i> How often should I visit the dentist?
                                </button>
                                <button onclick="setQuestion('What foods are good for dental health?'); return false;" class="btn btn-light text-start text-truncate w-100 rounded-pill px-3 py-2 text-muted hover-bg-primary-soft transition-all">
                                    <i class="bi bi-arrow-right-short me-2 text-primary"></i> What foods are good for dental health?
                                </button>
                                <button onclick="setQuestion('How do I prevent cavities?'); return false;" class="btn btn-light text-start text-truncate w-100 rounded-pill px-3 py-2 text-muted hover-bg-primary-soft transition-all">
                                    <i class="bi bi-arrow-right-short me-2 text-primary"></i> How do I prevent cavities?
                                </button>
                                <button onclick="setQuestion('What causes tooth sensitivity?'); return false;" class="btn btn-light text-start text-truncate w-100 rounded-pill px-3 py-2 text-muted hover-bg-primary-soft transition-all">
                                    <i class="bi bi-arrow-right-short me-2 text-primary"></i> What causes tooth sensitivity?
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100 bg-success text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4"><i class="bi bi-shield-check me-2"></i> Tips for Best Results</h5>
                            <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill me-3 mt-1 opacity-75"></i>
                                    <span>Ask specific questions about dental health procedures or symptoms.</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill me-3 mt-1 opacity-75"></i>
                                    <span>Provide context if needed (e.g., age group, specific condition).</span>
                                </li>
                                <li class="d-flex align-items-start">
                                    <i class="bi bi-check-circle-fill me-3 mt-1 opacity-75"></i>
                                    <span>Use this tool for general education, not for emergency diagnosis.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setQuestion(question) {
        document.getElementById('question').value = question;
        document.getElementById('question').focus();
    }
    
    document.querySelector('form').addEventListener('submit', function() {
        const button = this.querySelector('button[type="submit"]');
        const icon = button.querySelector('i');
        const originalText = button.innerHTML;
        
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Thinking...';
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
<script>
    (function() {
        const mdEl = document.getElementById('ai-markdown');
        const outEl = document.getElementById('ai-html');
        const markdown = mdEl ? mdEl.textContent : '';
        if (!markdown) return;
        // Convert markdown to HTML and sanitize to prevent XSS
        const rawHtml = marked.parse(markdown);
        const cleanHtml = DOMPurify.sanitize(rawHtml);
        outEl.innerHTML = cleanHtml;
    })();
</script>
<style>
    .ai-response {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }
    .ai-response h1, .ai-response h2, .ai-response h3 {
        color: var(--primary-color);
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }
    .ai-response ul, .ai-response ol {
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .ai-response li {
        margin-bottom: 0.5rem;
    }
    .hover-bg-primary-soft:hover {
        background-color: rgba(14, 165, 233, 0.1) !important;
        color: var(--primary-color) !important;
    }
    .shadow-inner {
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }
</style>

<?php include 'includes/footer.php'; ?>