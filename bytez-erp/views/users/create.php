<div class="row justify-content-center">
<div class="col-md-7">
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0"><i class="fas fa-user-plus me-2 text-primary"></i>Add New User</h5>
        <a href="/Codebytez/users/index" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" id="userForm">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Full Name *</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       placeholder="Enter full name" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Role *</label>
                <select name="role" class="form-select">
                    <option value="employee">Employee</option>
                    <option value="manager">Manager</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Email Address *</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="Enter email address" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Password *</label>
                <input type="password" name="password" id="password"
                       class="form-control" placeholder="Min 6 characters" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Confirm Password *</label>
                <input type="password" id="confirm_password"
                       class="form-control" placeholder="Repeat password" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i> Create User
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
    $('#userForm').on('submit', function(e) {
        var name     = $('input[name="name"]').val().trim();
        var email    = $('input[name="email"]').val().trim();
        var password = $('#password').val();
        var confirm  = $('#confirm_password').val();

        if (!name) {
            e.preventDefault();
            alert('❌ Name is required!');
            return false;
        }
        if (!email) {
            e.preventDefault();
            alert('❌ Email is required!');
            return false;
        }
        if (password.length < 6) {
            e.preventDefault();
            alert('❌ Password must be at least 6 characters!');
            return false;
        }
        if (password !== confirm) {
            e.preventDefault();
            alert('❌ Passwords do not match!');
            return false;
        }
    });
});
</script>