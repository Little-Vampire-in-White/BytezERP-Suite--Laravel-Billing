<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="fw-bold mb-0">All Tasks (<?= count($tasks) ?>)</h6>
    <a href="/Codebytez/tasks/create" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> New Task
    </a>
</div>

<!-- Filter & Search -->
<div class="card p-3 mb-4">
    <div class="row g-2">
        <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control form-control-sm"
                   placeholder="🔍 Search tasks...">
        </div>
        <div class="col-md-2">
            <select id="statusFilter" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="todo">To Do</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        <div class="col-md-2">
            <select id="priorityFilter" class="form-select form-select-sm">
                <option value="">All Priority</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
        </div>
    </div>
</div>

<!-- Kanban View -->
<div class="row g-4">
    <!-- To Do -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"
                 style="background:#f1f5f9;border-radius:12px 12px 0 0;">
                <span class="fw-bold"><span class="badge bg-secondary me-2">
                    <?= count(array_filter($tasks, fn($t) => $t['status']==='todo')) ?>
                </span> To Do</span>
            </div>
            <div class="card-body p-2" id="todo-col">
                <?php foreach($tasks as $t): if($t['status']!=='todo') continue; ?>
                <div class="task-card border rounded p-3 mb-2 bg-white"
                     data-status="<?= $t['status'] ?>"
                     data-priority="<?= $t['priority'] ?>"
                     data-title="<?= strtolower($t['title']) ?>">
                    <?php
                    $pc = ['high'=>'danger','medium'=>'warning','low'=>'success'];
                    $p  = $pc[$t['priority']] ?? 'secondary';
                    ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-<?= $p ?>"><?= ucfirst($t['priority']) ?></span>
                        <small class="text-muted"><?= $t['deadline'] ?? '' ?></small>
                    </div>
                    <p class="fw-bold mb-1 small"><?= htmlspecialchars($t['title']) ?></p>
                    <p class="text-muted small mb-2"><?= htmlspecialchars($t['project_name'] ?? '') ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>
                            <?= htmlspecialchars($t['assigned_name'] ?? 'Unassigned') ?>
                        </small>
                        <div>
                            <a href="/Codebytez/tasks/view/<?= $t['id'] ?>"
                               class="btn btn-xs btn-outline-primary btn-sm py-0 px-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/Codebytez/tasks/edit/<?= $t['id'] ?>"
                               class="btn btn-xs btn-outline-secondary btn-sm py-0 px-1">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- In Progress -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"
                 style="background:#eff6ff;border-radius:12px 12px 0 0;">
                <span class="fw-bold"><span class="badge bg-primary me-2">
                    <?= count(array_filter($tasks, fn($t) => $t['status']==='in_progress')) ?>
                </span> In Progress</span>
            </div>
            <div class="card-body p-2">
                <?php foreach($tasks as $t): if($t['status']!=='in_progress') continue; ?>
                <div class="task-card border rounded p-3 mb-2 bg-white"
                     data-status="<?= $t['status'] ?>"
                     data-priority="<?= $t['priority'] ?>"
                     data-title="<?= strtolower($t['title']) ?>">
                    <?php $p = $pc[$t['priority']] ?? 'secondary'; ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-<?= $p ?>"><?= ucfirst($t['priority']) ?></span>
                        <small class="text-muted"><?= $t['deadline'] ?? '' ?></small>
                    </div>
                    <p class="fw-bold mb-1 small"><?= htmlspecialchars($t['title']) ?></p>
                    <p class="text-muted small mb-2"><?= htmlspecialchars($t['project_name'] ?? '') ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>
                            <?= htmlspecialchars($t['assigned_name'] ?? 'Unassigned') ?>
                        </small>
                        <div>
                            <a href="/Codebytez/tasks/view/<?= $t['id'] ?>"
                               class="btn btn-sm btn-outline-primary py-0 px-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/Codebytez/tasks/edit/<?= $t['id'] ?>"
                               class="btn btn-sm btn-outline-secondary py-0 px-1">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Completed -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center"
                 style="background:#f0fdf4;border-radius:12px 12px 0 0;">
                <span class="fw-bold"><span class="badge bg-success me-2">
                    <?= count(array_filter($tasks, fn($t) => $t['status']==='completed')) ?>
                </span> Completed</span>
            </div>
            <div class="card-body p-2">
                <?php foreach($tasks as $t): if($t['status']!=='completed') continue; ?>
                <div class="task-card border rounded p-3 mb-2 bg-white"
                     data-status="<?= $t['status'] ?>"
                     data-priority="<?= $t['priority'] ?>"
                     data-title="<?= strtolower($t['title']) ?>">
                    <?php $p = $pc[$t['priority']] ?? 'secondary'; ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-<?= $p ?>"><?= ucfirst($t['priority']) ?></span>
                        <small class="text-muted"><?= $t['deadline'] ?? '' ?></small>
                    </div>
                    <p class="fw-bold mb-1 small"><?= htmlspecialchars($t['title']) ?></p>
                    <p class="text-muted small mb-2"><?= htmlspecialchars($t['project_name'] ?? '') ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>
                            <?= htmlspecialchars($t['assigned_name'] ?? 'Unassigned') ?>
                        </small>
                        <div>
                            <a href="/Codebytez/tasks/view/<?= $t['id'] ?>"
                               class="btn btn-sm btn-outline-primary py-0 px-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/Codebytez/tasks/edit/<?= $t['id'] ?>"
                               class="btn btn-sm btn-outline-secondary py-0 px-1">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function filterTasks() {
    var search   = $('#searchInput').val().toLowerCase();
    var status   = $('#statusFilter').val();
    var priority = $('#priorityFilter').val();

    $('.task-card').each(function() {
        var title    = $(this).data('title');
        var tStatus  = $(this).data('status');
        var tPriority= $(this).data('priority');

        var show = true;
        if (search && title.indexOf(search) === -1) show = false;
        if (status && tStatus !== status) show = false;
        if (priority && tPriority !== priority) show = false;

        $(this).toggle(show);
    });
}

$('#searchInput, #statusFilter, #priorityFilter').on('input change', filterTasks);
</script>