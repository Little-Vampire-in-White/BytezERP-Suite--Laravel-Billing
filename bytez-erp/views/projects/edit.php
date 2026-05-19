<div class="row justify-content-center">
<div class="col-md-9">
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0"><i class="fas fa-edit me-2 text-primary"></i>Edit Project</h5>
        <a href="/Codebytez/projects/index" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-bold">Project Title *</label>
                <input type="text" name="title" class="form-control"
                       value="<?= htmlspecialchars($_POST['title'] ?? $project['title']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <?php foreach(['pending','in_progress','completed','cancelled'] as $s): ?>
                    <option value="<?= $s ?>" <?= $project['status']===$s?'selected':'' ?>>
                        <?= ucfirst(str_replace('_',' ',$s)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Client *</label>
                <select name="client_id" class="form-select" required>
                    <?php foreach($clients as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $project['client_id']==$c['id']?'selected':'' ?>>
                        <?= htmlspecialchars($c['company_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Project Manager</label>
                <select name="manager_id" class="form-select">
                    <option value="">Select Manager</option>
                    <?php foreach($users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $project['manager_id']==$u['id']?'selected':'' ?>>
                        <?= htmlspecialchars($u['name']) ?> (<?= $u['role'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Start Date</label>
                <input type="date" name="start_date" class="form-control"
                       value="<?= $_POST['start_date'] ?? $project['start_date'] ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Deadline</label>
                <input type="date" name="deadline" class="form-control"
                       value="<?= $_POST['deadline'] ?? $project['deadline'] ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Budget ($)</label>
                <input type="number" name="budget" class="form-control" step="0.01"
                       value="<?= $_POST['budget'] ?? $project['budget'] ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Progress: <span id="progressVal"><?= $project['progress'] ?>%</span></label>
                <input type="range" name="progress" class="form-range" min="0" max="100"
                       value="<?= $project['progress'] ?>"
                       oninput="document.getElementById('progressVal').textContent = this.value + '%'">
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Team Members</label>
                <div class="row g-2">
                    <?php foreach($users as $u): ?>
                    <div class="col-md-3">
                        <div class="form-check border rounded p-2">
                            <input class="form-check-input" type="checkbox"
                                   name="members[]" value="<?= $u['id'] ?>"
                                   id="user_<?= $u['id'] ?>"
                                   <?= in_array($u['id'], $memberIds)?'checked':'' ?>>
                            <label class="form-check-label small" for="user_<?= $u['id'] ?>">
                                <?= htmlspecialchars($u['name']) ?><br>
                                <span class="text-muted"><?= $u['role'] ?></span>
                            </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($_POST['description'] ?? $project['description']) ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i> Update Project
                </button>
                <a href="/Codebytez/projects/index" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>

<script>
// Set deadline min date based on start date
$('#start_date').on('change', function() {
    var startDate = $(this).val();
    $('#deadline').attr('min', startDate);
    // Clear deadline if it's before new start date
    if ($('#deadline').val() && $('#deadline').val() < startDate) {
        $('#deadline').val('');
        alert('Deadline has been cleared because it was before the new start date.');
    }
});

// Form validation before submit
$('form').on('submit', function(e) {
    var title     = $('input[name="title"]').val().trim();
    var clientId  = $('select[name="client_id"]').val();
    var startDate = $('#start_date').val();
    var deadline  = $('#deadline').val();
    var budget    = $('input[name="budget"]').val();

    if (!title) {
        e.preventDefault();
        alert('❌ Project title is required!');
        return false;
    }
    if (!clientId) {
        e.preventDefault();
        alert('❌ Please select a client!');
        return false;
    }
    if (!startDate) {
        e.preventDefault();
        alert('❌ Start date is required!');
        return false;
    }
    if (!deadline) {
        e.preventDefault();
        alert('❌ Deadline is required!');
        return false;
    }
    if (deadline < startDate) {
        e.preventDefault();
        alert('❌ Deadline cannot be before start date!');
        return false;
    }
    if (budget < 0) {
        e.preventDefault();
        alert('❌ Budget cannot be negative!');
        return false;
    }
});
</script>