<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75 small">Total Clients</p>
                    <h2 class="mb-0 fw-bold"><?= $stats['total_clients'] ?></h2>
                </div>
                <i class="fas fa-building fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0891b2,#0284c7)">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75 small">Active Projects</p>
                    <h2 class="mb-0 fw-bold"><?= $stats['active_projects'] ?></h2>
                </div>
                <i class="fas fa-diagram-project fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#059669,#10b981)">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75 small">Completed Tasks</p>
                    <h2 class="mb-0 fw-bold"><?= $stats['completed_tasks'] ?></h2>
                </div>
                <i class="fas fa-check-circle fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#dc2626,#f97316)">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75 small">Employees</p>
                    <h2 class="mb-0 fw-bold"><?= $stats['total_employees'] ?></h2>
                </div>
                <i class="fas fa-users fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Task Status Overview</h6>
            <canvas id="taskChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Project Status Overview</h6>
            <canvas id="projectChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Recent Tables -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card p-4">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="fw-bold mb-0">Recent Projects</h6>
                <a href="/Codebytez/projects/index" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <table class="table table-sm">
                <thead><tr><th>Project</th><th>Client</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($recentProjects as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['title']) ?></td>
                    <td><?= htmlspecialchars($p['company_name'] ?? '-') ?></td>
                    <td>
                        <?php $b=['pending'=>'warning','in_progress'=>'primary','completed'=>'success','cancelled'=>'danger']; ?>
                        <span class="badge bg-<?= $b[$p['status']]??'secondary' ?>"><?= ucfirst(str_replace('_',' ',$p['status'])) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($recentProjects)): ?>
                <tr><td colspan="3" class="text-center text-muted py-3">No projects yet</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="fw-bold mb-0">Recent Tasks</h6>
                <a href="/Codebytez/tasks/index" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <table class="table table-sm">
                <thead><tr><th>Task</th><th>Assigned To</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($recentTasks as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['title']) ?></td>
                    <td><?= htmlspecialchars($t['assigned_name'] ?? 'Unassigned') ?></td>
                    <td>
                        <?php $tb=['todo'=>'secondary','in_progress'=>'primary','completed'=>'success']; ?>
                        <span class="badge bg-<?= $tb[$t['status']]??'secondary' ?>"><?= ucfirst(str_replace('_',' ',$t['status'])) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($recentTasks)): ?>
                <tr><td colspan="3" class="text-center text-muted py-3">No tasks yet</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('taskChart'), {
    type: 'doughnut',
    data: {
        labels: ['To Do', 'In Progress', 'Completed'],
        datasets: [{
            data: [<?= $taskChart['todo'] ?>, <?= $taskChart['in_progress'] ?>, <?= $taskChart['completed'] ?>],
            backgroundColor: ['#94a3b8','#4f46e5','#10b981'],
            borderWidth: 0
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } }, cutout: '70%' }
});

new Chart(document.getElementById('projectChart'), {
    type: 'bar',
    data: {
        labels: ['Pending','In Progress','Completed','Cancelled'],
        datasets: [{
            label: 'Projects',
            data: [<?= $projectChart['pending'] ?>, <?= $projectChart['in_progress'] ?>, <?= $projectChart['completed'] ?>, <?= $projectChart['cancelled'] ?>],
            backgroundColor: ['#f59e0b','#4f46e5','#10b981','#ef4444'],
            borderRadius: 8
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>