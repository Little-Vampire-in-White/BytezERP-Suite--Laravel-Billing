<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<div class="row g-4">

    <!-- AI Content Generator -->
    <div class="col-md-7">
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-1">
                <i class="fas fa-magic me-2 text-primary"></i>AI Content Generator
            </h5>
            <p class="text-muted small mb-4">Generate marketing content instantly using AI</p>

            <div class="mb-3">
                <label class="form-label fw-bold">Content Type</label>
                <div class="row g-2" id="contentTypes">
                    <div class="col-md-4">
                        <div class="content-type-btn active border rounded p-3 text-center"
                             data-type="social_media" style="cursor:pointer;">
                            <i class="fab fa-instagram fa-2x text-primary mb-2 d-block"></i>
                            <small class="fw-bold">Social Media</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="content-type-btn border rounded p-3 text-center"
                             data-type="blog_idea" style="cursor:pointer;">
                            <i class="fas fa-blog fa-2x text-success mb-2 d-block"></i>
                            <small class="fw-bold">Blog Ideas</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="content-type-btn border rounded p-3 text-center"
                             data-type="ad_copy" style="cursor:pointer;">
                            <i class="fas fa-bullhorn fa-2x text-warning mb-2 d-block"></i>
                            <small class="fw-bold">Ad Copy</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="content-type-btn border rounded p-3 text-center"
                             data-type="email" style="cursor:pointer;">
                            <i class="fas fa-envelope fa-2x text-danger mb-2 d-block"></i>
                            <small class="fw-bold">Email Campaign</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="content-type-btn border rounded p-3 text-center"
                             data-type="seo" style="cursor:pointer;">
                            <i class="fas fa-search fa-2x text-info mb-2 d-block"></i>
                            <small class="fw-bold">SEO Content</small>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="selectedType" value="social_media">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Topic / Product / Description</label>
                <textarea id="aiInput" class="form-control" rows="3"
                          placeholder="e.g. New eco-friendly water bottle for fitness enthusiasts..."></textarea>
            </div>

            <button id="generateBtn" class="btn btn-primary w-100">
                <i class="fas fa-magic me-2"></i> Generate Content
            </button>
        </div>

        <!-- Result -->
        <div class="card p-4" id="resultCard" style="display:none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-robot me-2 text-success"></i>AI Generated Content
                </h6>
                <button class="btn btn-sm btn-outline-secondary" id="copyBtn">
                    <i class="fas fa-copy me-1"></i> Copy
                </button>
            </div>
            <div id="aiResult" class="bg-light rounded p-3"
                 style="white-space:pre-wrap;font-size:0.9rem;line-height:1.6;"></div>
        </div>
    </div>

    <!-- AI Project Insights + History -->
    <div class="col-md-5">

        <!-- Insights -->
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-1">
                <i class="fas fa-chart-line me-2 text-success"></i>AI Project Insights
            </h5>
            <p class="text-muted small mb-3">Analyze your projects with AI</p>
            <button id="insightsBtn" class="btn btn-success w-100 mb-3">
                <i class="fas fa-brain me-2"></i> Analyze My Projects
            </button>
            <div id="insightsResult" class="bg-light rounded p-3"
                 style="display:none;white-space:pre-wrap;font-size:0.85rem;line-height:1.6;max-height:400px;overflow-y:auto;"></div>
        </div>

        <!-- History -->
        <div class="card p-4">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-history me-2 text-muted"></i>Recent AI History
            </h6>
            <?php if(empty($logs)): ?>
            <p class="text-muted small text-center py-3">No AI history yet</p>
            <?php else: ?>
            <?php foreach($logs as $log): ?>
            <div class="border rounded p-3 mb-2">
                <div class="d-flex justify-content-between mb-1">
                    <span class="badge bg-<?= $log['type']==='insight'?'success':'primary' ?>">
                        <?= ucfirst($log['type']) ?>
                    </span>
                    <small class="text-muted">
                        <?= date('M d, H:i', strtotime($log['created_at'])) ?>
                    </small>
                </div>
                <p class="small fw-bold mb-1"><?= htmlspecialchars(substr($log['prompt'],0,60)) ?>...</p>
                <p class="small text-muted mb-0"><?= htmlspecialchars(substr($log['response'],0,80)) ?>...</p>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.content-type-btn.active {
    background: #eff6ff;
    border-color: #4f46e5 !important;
    color: #4f46e5;
}
.content-type-btn:hover {
    background: #f8fafc;
}
</style>

<script>
// Content type selection
$('.content-type-btn').on('click', function() {
    $('.content-type-btn').removeClass('active');
    $(this).addClass('active');
    $('#selectedType').val($(this).data('type'));
});

// Generate content
$('#generateBtn').on('click', function() {
    var input = $('#aiInput').val().trim();
    var type  = $('#selectedType').val();

    if (!input) {
        alert('Please enter a topic or description!');
        return;
    }

    var btn = $(this);
    btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Generating...').prop('disabled', true);
    $('#resultCard').hide();

    $.post('/Codebytez/ai/generate', { input: input, content_type: type })
        .done(function(res) {
            if (res.success) {
                $('#aiResult').text(res.text);
                $('#resultCard').show();
                $('html, body').animate({ scrollTop: $('#resultCard').offset().top - 100 }, 500);
            } else {
                alert('Error: ' + res.error);
            }
        })
        .fail(function() {
            alert('Failed to connect to AI service. Check your API key.');
        })
        .always(function() {
            btn.html('<i class="fas fa-magic me-2"></i> Generate Content').prop('disabled', false);
        });
});

// Copy result
$('#copyBtn').on('click', function() {
    var text = $('#aiResult').text();
    navigator.clipboard.writeText(text).then(function() {
        $('#copyBtn').html('<i class="fas fa-check me-1"></i> Copied!');
        setTimeout(function() {
            $('#copyBtn').html('<i class="fas fa-copy me-1"></i> Copy');
        }, 2000);
    });
});

// Project insights
$('#insightsBtn').on('click', function() {
    var btn = $(this);
    btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Analyzing...').prop('disabled', true);
    $('#insightsResult').hide();

    $.post('/Codebytez/ai/insights')
        .done(function(res) {
            if (res.success) {
                $('#insightsResult').text(res.text).show();
            } else {
                alert('Error: ' + res.error);
            }
        })
        .fail(function() {
            alert('Failed to connect to AI service.');
        })
        .always(function() {
            btn.html('<i class="fas fa-brain me-2"></i> Analyze My Projects').prop('disabled', false);
        });
});
</script>