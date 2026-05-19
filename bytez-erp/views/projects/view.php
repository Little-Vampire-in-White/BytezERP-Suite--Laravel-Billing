<div class="row g-4">
    <!-- Project Details -->
    <div class="col-md-8">
        <div class="card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($project['title']) ?></h4>
                    <p class="text-muted mb-0"><?= htmlspecialchars($project['company_name'] ?? '') ?></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="/Codebytez/projects/edit/<?= $project['id'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>

            <?php
            $badges = ['pending'=>'warning','in_progress'=>'primary','completed'=>'success','cancelled'=>'danger'];
            $b = $badges[$project['status']] ?? 'secondary';
            ?>
            <span class="badge bg-<?= $b ?> mb-3"><?= ucfirst(str_replace('_',' ',$project['status'])) ?></span>

            <p><?= nl2br(htmlspecialchars($project['description'] ?? 'No description')) ?></p>

            <!-- Progress -->
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold">Progress</span>
                    <span><?= $project['progress'] ?>%</span>
                </div>
                <div class="progress" style="height:10px;">
                    <div class="progress-bar bg-<?= $b ?>" style="width:<?= $project['progress'] ?>%"></div>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <small class="text-muted d-block">Start Date</small>
                    <strong><?= $project['start_date'] ?? '-' ?></strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Deadline</small>
                    <strong><?= $project['deadline'] ?? '-' ?></strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Budget</small>
                    <strong>$<?= number_format($project['budget'],2) ?></strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Manager</small>
                    <strong><?= htmlspecialchars($project['manager_name'] ?? '-') ?></strong>
                </div>
            </div>
        </div>

        <!-- Tasks -->
        <div class="card p-4">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="fw-bold mb-0">Tasks (<?= count($tasks) ?>)</h6>
                <a href="/Codebytez/tasks/create?project_id=<?= $project['id'] ?>"
                   class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Task
                </a>
            </div>
            <?php foreach($tasks as $t): ?>
            <?php $tb=['todo'=>'secondary','in_progress'=>'primary','completed'=>'success']; ?>
            <div class="d-flex align-items-center justify-content-between p-3 border rounded mb-2">
                <div>
                    <span class="badge bg-<?= $tb[$t['status']]??'secondary' ?> me-2">
                        <?= ucfirst(str_replace('_',' ',$t['status'])) ?>
                    </span>
                    <strong><?= htmlspecialchars($t['title']) ?></strong>
                    <small class="text-muted ms-2">→ <?= htmlspecialchars($t['assigned_name'] ?? 'Unassigned') ?></small>
                </div>
                <a href="/Codebytez/tasks/edit/<?= $t['id'] ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
            <?php endforeach; ?>
            <?php if(empty($tasks)): ?>
            <p class="text-muted text-center py-3">No tasks yet</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Team Members -->
    <div class="col-md-4">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Team Members (<?= count($members) ?>)</h6>
            <?php foreach($members as $m): ?>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:40px;height:40px;background:#4f46e5;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;">
                    <?= strtoupper(substr($m['name'],0,2)) ?>
                </div>
                <div>
                    <div class="fw-bold small"><?= htmlspecialchars($m['name']) ?></div>
                    <div class="text-muted small"><?= ucfirst($m['role']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if(empty($members)): ?>
            <p class="text-muted small">No members assigned</p>
            <?php endif; ?>
        </div>
    </div>
</div>