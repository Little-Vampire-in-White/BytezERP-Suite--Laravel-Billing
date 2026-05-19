<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="fw-bold mb-0">Users Management (<?= count($users) ?>)</h6>
    <a href="/Codebytez/users/create" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Add User
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="p-3 border-bottom">
            <input type="text" id="searchInput" class="form-control"
                   placeholder="🔍 Search users...">
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($users as $i => $u): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:36px;height:36px;background:#4f46e5;border-radius:50%;
                                        display:flex;align-items:center;justify-content:center;
                                        color:white;font-weight:700;font-size:0.8rem;">
                                <?= strtoupper(substr($u['name'],0,2)) ?>
                            </div>
                            <strong><?= htmlspecialchars($u['name']) ?></strong>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <?php
                        $rc = ['admin'=>'danger','manager'=>'primary','employee'=>'success'];
                        $r  = $rc[$u['role']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $r ?>"><?= ucfirst($u['role']) ?></span>
                    </td>
                    <td>
                        <span class="badge bg-<?= $u['is_active']?'success':'danger' ?>">
                            <?= $u['is_active']?'Active':'Inactive' ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <a href="/Codebytez/users/edit/<?= $u['id'] ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if($u['id'] != $_SESSION['user_id']): ?>
                        <a href="/Codebytez/users/delete/<?= $u['id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Delete this user?')">
                            <i class="fas fa-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('#usersTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });
});
</script>