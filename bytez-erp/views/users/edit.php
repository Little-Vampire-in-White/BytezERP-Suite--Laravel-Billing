<div class="row justify-content-center">
<div class="col-md-7">
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0"><i class="fas fa-user-edit me-2 text-primary"></i>Edit User</h5>
        <a href="/Codebytez/users/index" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" id="editForm">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Full Name *</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($_POST['name'] ?? $user['name']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Role *</label>
                <select name="role" class="form-select">
                    <?php foreach(['employee','manager','admin'] as $r): ?>
                    <option value="<?= $r ?>" <?= $user['role']===$r?'selected':'' ?>>
                        <?= ucfirst($r) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Email Address *</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                <input type="password" name="password" id="password"
                       class="form-control" placeholder="Enter new password">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Confirm Password</label>
                <input type="password" id="confirm_password"
                       class="form-control" placeholder="Repeat new password">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i> Update User
                </button>
                <a href="/Codebytez/users/index" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </div>
    </form>
</div>
</div>
</div>

<script>
$(document).ready(function() {
    $('#editForm').on('submit', function(e) {
        var password = $('#password').val();
        var confirm  = $('#confirm_password').val();
        if (password && password.length < 6) {
            e.preventDefault();
            alert('❌ Password must be at least 6 characters!');
            return false;
        }
        if (password && password !== confirm) {
            e.preventDefault();
            alert('❌ Passwords do not match!');
            return false;
        }
    });
});
</script>