<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="fw-bold mb-0">All Clients (<?= count($clients) ?>)</h6>
    <a href="/Codebytez/clients/create" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Add Client
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="p-3 border-bottom">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search clients...">
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="clientTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Company</th>
                        <th>Industry</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Projects</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($clients as $i => $c): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:35px;height:35px;background:#4f46e5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:0.8rem;">
                                <?= strtoupper(substr($c['company_name'],0,2)) ?>
                            </div>
                            <strong><?= htmlspecialchars($c['company_name']) ?></strong>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($c['industry'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['contact_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['phone'] ?? '-') ?></td>
                    <td><span class="badge bg-info"><?= $c['project_count'] ?></span></td>
                    <td>
                        <span class="badge bg-<?= $c['status']==='active'?'success':'danger' ?>">
                            <?= ucfirst($c['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="/Codebytez/clients/edit/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="/Codebytez/clients/delete/<?= $c['id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Delete this client?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($clients)): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">No clients yet. <a href="/Codebytez/clients/create">Add one!</a></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$('#searchInput').on('keyup', function() {
    var val = $(this).val().toLowerCase();
    $('#clientTable tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
    });
});
</script>