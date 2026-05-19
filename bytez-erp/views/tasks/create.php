<div class="row justify-content-center">
<div class="col-md-8">
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0"><i class="fas fa-check-square me-2 text-primary"></i>Create New Task</h5>
        <a href="/Codebytez/tasks/index" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" id="taskForm">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold">Task Title *</label>
                <input type="text" name="title" class="form-control"
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                       placeholder="Enter task title" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Project *</label>
                <select name="project_id" class="form-select" required>
                    <option value="">Select Project</option>
                    <?php foreach($projects as $p): ?>
                    <option value="<?= $p['id'] ?>"
                        <?= (($_POST['project_id'] ?? $selectedProject)==$p['id'])?'selected':'' ?>>
                        <?= htmlspecialchars($p['title']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Assign To</label>
                <select name="assigned_to" class="form-select">
                    <option value="">Unassigned</option>
                    <?php foreach($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= $u['role'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Priority</label>
                <select name="priority" class="form-select">
                    <option value="low">🟢 Low</option>
                    <option value="medium" selected>🟡 Medium</option>
                    <option value="high">🔴 High</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="todo">To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Deadline *</label>
                <input type="date" name="deadline" id="deadline" class="form-control"
                       value="<?= $_POST['deadline'] ?? '' ?>"
                       min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="4"
                          placeholder="Describe the task..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i> Create Task
                </button>
                <a href="/Codebytez/tasks/index" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>

<script>
$('#taskForm').on('submit', function(e) {
    var title    = $('input[name="title"]').val().trim();
    var project  = $('select[name="project_id"]').val();
    var deadline = $('#deadline').val();

    if (!title) {
        e.preventDefault();
        alert('❌ Task title is required!');
        return false;
    }
    if (!project) {
        e.preventDefault();
        alert('❌ Please select a project!');
        return false;
    }
    if (!deadline) {
        e.preventDefault();
        alert('❌ Please set a deadline!');
        return false;
    }
});
</script>