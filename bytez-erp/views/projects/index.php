<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="fw-bold mb-0">All Projects (<?= count($projects) ?>)</h6>
    <a href="/Codebytez/projects/create" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> New Project
    </a>
</div>

<!-- Filter Buttons -->
<div class="mb-3">
    <button class="btn btn-sm btn-outline-secondary filter-btn active" data-filter="all">All</button>
    <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="pending">Pending</button>
    <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="in_progress">In Progress</button>
    <button class="btn btn-sm btn-outline-success filter-btn" data-filter="completed">Completed</button>
    <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="cancelled">Cancelled</button>
</div>

<div class="row g-4" id="projectCards">
<?php foreach ($projects as $p): ?>
<div class="col-md-4 project-card" data-status="<?= $p['status'] ?>">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <?php
                $badges = ['pending'=>'warning','in_progress'=>'primary','completed'=>'success','cancelled'=>'danger'];
                $b = $badges[$p['status']] ?? 'secondary';
                ?>
                <span class="badge bg-<?= $b ?>"><?= ucfirst(str_replace('_',' ',$p['status'])) ?></span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/Codebytez/projects/view/<?= $p['id'] ?>"><i class="fas fa-eye me-2"></i>View</a></li>
                        <li><a class="dropdown-item" href="/Codebytez/projects/edit/<?= $p['id'] ?>"><i class="fas fa-edit me-2"></i>Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/Codebytez/projects/delete/<?= $p['id'] ?>"
                               onclick="return confirm('Delete this project?')"><i class="fas fa-trash me-2"></i>Delete</a></li>
                    </ul>
                </div>
            </div>
            <h6 class="fw-bold mb-1"><?= htmlspecialchars($p['title']) ?></h6>
            <p class="text-muted small mb-2"><?= htmlspecialchars($p['company_name'] ?? '-') ?></p>
            <p class="small text-muted mb-3"><?= htmlspecialchars(substr($p['description'] ?? '',0,80)) ?>...</p>

            <!-- Progress Bar -->
            <div class="mb-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span>Progress</span>
                    <span><?= $p['progress'] ?>%</span>
                </div>
                <div class="progress" style="height:6px;">
                    <div class="progress-bar bg-<?= $b ?>" style="width:<?= $p['progress'] ?>%"></div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3 small text-muted">
                <span><i class="fas fa-calendar me-1"></i><?= $p['deadline'] ?? 'No deadline' ?></span>
                <span><i class="fas fa-user me-1"></i><?= htmlspecialchars($p['manager_name'] ?? 'No manager') ?></span>
            </div>
        </div>
        <div class="card-footer bg-transparent">
            <a href="/Codebytez/projects/view/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary w-100">
                View Details
            </a>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php if(empty($projects)): ?>
<div class="col-12">
    <div class="text-center py-5 text-muted">
        <i class="fas fa-diagram-project fa-3x mb-3 opacity-25"></i>
        <p>No projects yet. <a href="/Codebytez/projects/create">Create one!</a></p>
    </div>
</div>
<?php endif; ?>
</div>

<script>
$('.filter-btn').on('click', function() {
    $('.filter-btn').removeClass('active');
    $(this).addClass('active');
    var filter = $(this).data('filter');
    if (filter === 'all') {
        $('.project-card').show();
    } else {
        $('.project-card').hide();
        $('.project-card[data-status="' + filter + '"]').show();
    }
});
</script>