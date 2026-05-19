<div class="row justify-content-center">
<div class="col-md-8">
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0"><i class="fas fa-edit me-2 text-primary"></i>Edit Task</h5>
        <a href="/Codebytez/tasks/view/<?= $task['id'] ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold">Task Title *</label>
                <input type="text" name="title" class="form-control"
                       value="<?= htmlspecialchars($_POST['title'] ?? $task['title']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Project *</label>
                <select name="project_id" class="form-select" required>
                    <?php foreach($projects as $p): ?>
                    <option value="<?= $p['id'] ?>"
                        <?= $task['project_id']==$p['id']?'selected':'' ?>>
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
                    <option value="<?= $u['id'] ?>"
                        <?= $task['assigned_to']==$u['id']?'selected':'' ?>>
                        <?= htmlspecialchars($u['name']) ?> (<?= $u['role'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Priority</label>
                <select name="priority" class="form-select">
                    <?php foreach(['low','medium','high'] as $pr): ?>
                    <option value="<?= $pr ?>" <?= $task['priority']===$pr?'selected':'' ?>>
                        <?= ucfirst($pr) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <?php foreach(['todo'=>'To Do','in_progress'=>'In Progress','completed'=>'Completed'] as $s=>$l): ?>
                    <option value="<?= $s ?>" <?= $task['status']===$s?'selected':'' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Deadline *</label>
                <input type="date" name="deadline" class="form-control"
                       value="<?= $_POST['deadline'] ?? $task['deadline'] ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($_POST['description'] ?? $task['description']) ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i> Update Task
                </button>
                <a href="/Codebytez/tasks/view/<?= $task['id'] ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>