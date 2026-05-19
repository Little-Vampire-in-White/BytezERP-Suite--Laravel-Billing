<div class="row g-4">
    <div class="col-md-8">
        <!-- Task Details -->
        <div class="card p-4 mb-4">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <?php
                    $pc = ['high'=>'danger','medium'=>'warning','low'=>'success'];
                    $tc = ['todo'=>'secondary','in_progress'=>'primary','completed'=>'success'];
                    ?>
                    <span class="badge bg-<?= $tc[$task['status']]??'secondary' ?> me-2">
                        <?= ucfirst(str_replace('_',' ',$task['status'])) ?>
                    </span>
                    <span class="badge bg-<?= $pc[$task['priority']]??'secondary' ?>">
                        <?= ucfirst($task['priority']) ?> Priority
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <a href="/Codebytez/tasks/edit/<?= $task['id'] ?>"
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="/Codebytez/tasks/delete/<?= $task['id'] ?>"
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Delete this task?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>

            <h4 class="fw-bold mb-2"><?= htmlspecialchars($task['title']) ?></h4>
            <p class="text-muted mb-3">
                <i class="fas fa-diagram-project me-1"></i>
                <?= htmlspecialchars($task['project_name'] ?? '') ?>
            </p>
            <p><?= nl2br(htmlspecialchars($task['description'] ?? 'No description')) ?></p>

            <div class="row g-3 mt-2 pt-3 border-top">
                <div class="col-md-4">
                    <small class="text-muted d-block">Assigned To</small>
                    <strong><?= htmlspecialchars($task['assigned_name'] ?? 'Unassigned') ?></strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Deadline</small>
                    <strong><?= $task['deadline'] ?? '-' ?></strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Created</small>
                    <strong><?= date('M d, Y', strtotime($task['created_at'])) ?></strong>
                </div>
            </div>
        </div>

        <!-- Comments -->
        <div class="card p-4 mb-4">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-comments me-2 text-primary"></i>
                Comments (<?= count($comments) ?>)
            </h6>

            <?php foreach($comments as $c): ?>
            <div class="d-flex gap-3 mb-3">
                <div style="width:36px;height:36px;min-width:36px;background:#4f46e5;border-radius:50%;
                            display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.8rem;">
                    <?= strtoupper(substr($c['user_name'],0,2)) ?>
                </div>
                <div class="flex-grow-1">
                    <div class="bg-light rounded p-3">
                        <div class="d-flex justify-content-between mb-1">
                            <strong class="small"><?= htmlspecialchars($c['user_name']) ?></strong>
                            <small class="text-muted"><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></small>
                        </div>
                        <p class="mb-0 small"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if(empty($comments)): ?>
            <p class="text-muted small">No comments yet. Be the first to comment!</p>
            <?php endif; ?>

            <!-- Add Comment -->
            <form method="POST" action="/Codebytez/tasks/comment/<?= $task['id'] ?>" class="mt-3">
                <div class="d-flex gap-2">
                    <textarea name="comment" class="form-control form-control-sm"
                              rows="2" placeholder="Write a comment..." required></textarea>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Attachments -->
        <div class="card p-4">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-paperclip me-2 text-primary"></i>
                Attachments (<?= count($attachments) ?>)
            </h6>

            <?php foreach($attachments as $a): ?>
            <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-file text-primary"></i>
                    <div>
                        <div class="small fw-bold"><?= htmlspecialchars($a['file_name']) ?></div>
                        <div class="text-muted" style="font-size:0.75rem;">
                            by <?= htmlspecialchars($a['user_name']) ?> •
                            <?= date('M d, Y', strtotime($a['uploaded_at'])) ?>
                        </div>
                    </div>
                </div>
                <a href="/<?= $a['file_path'] ?>" download class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-download"></i>
                </a>
            </div>
            <?php endforeach; ?>

            <!-- Upload -->
            <form method="POST" action="/Codebytez/tasks/upload/<?= $task['id'] ?>"
                  enctype="multipart/form-data" class="mt-3">
                <div class="d-flex gap-2">
                    <input type="file" name="attachment" class="form-control form-control-sm" required>
                    <button type="submit" class="btn btn-outline-primary btn-sm px-3">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Quick Update Status</h6>
            <?php foreach(['todo'=>['secondary','To Do'],'in_progress'=>['primary','In Progress'],'completed'=>['success','Completed']] as $s=>[$color,$label]): ?>
            <a href="/Codebytez/tasks/edit/<?= $task['id'] ?>"
               class="btn btn-outline-<?= $color ?> w-100 mb-2 <?= $task['status']===$s?'active':'' ?>">
                <?= $label ?>
            </a>
            <?php endforeach; ?>

            <hr>
            <h6 class="fw-bold mb-3">Task Info</h6>
            <table class="table table-sm">
                <tr><td class="text-muted">Project</td><td><?= htmlspecialchars($task['project_name']??'-') ?></td></tr>
                <tr><td class="text-muted">Priority</td><td><?= ucfirst($task['priority']) ?></td></tr>
                <tr><td class="text-muted">Status</td><td><?= ucfirst(str_replace('_',' ',$task['status'])) ?></td></tr>
                <tr><td class="text-muted">Deadline</td><td><?= $task['deadline']??'-' ?></td></tr>
            </table>
        </div>
    </div>
</div>