<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Bytez ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e1b4b 0%, #4f46e5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        .brand { text-align: center; margin-bottom: 30px; }
        .brand h2 { color: #1e1b4b; font-weight: 800; }
        .brand span { color: #4f46e5; }
        .brand p { color: #94a3b8; font-size: 0.9rem; }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.15);
        }
        .btn-login {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: white;
            width: 100%;
            font-size: 1rem;
        }
        .btn-login:hover { opacity: 0.9; color: white; }
        .demo-creds {
            background: #f8fafc;
            border-radius: 10px;
            padding: 15px;
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 20px;
        }
        .input-icon { position: relative; }
        .input-icon i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 10;
        }
        .input-icon input { padding-left: 40px; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="brand">
        <h2><i class="fas fa-bolt text-warning"></i> Bytez<span>ERP</span></h2>
        <p>Digital Agency Management System</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger rounded-3 py-2">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="loginForm">
        <div class="mb-3">
            <label class="form-label fw-bold">Email Address</label>
            <div class="input-icon">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control"
                    placeholder="Enter your email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold">Password</label>
            <div class="input-icon">
                <i class="fas fa-lock"></i>
                <input type="password" name="password"
                    class="form-control" placeholder="Enter your password" required>
            </div>
        </div>
        <button type="submit" class="btn btn-login">
            <i class="fas fa-sign-in-alt me-2"></i> Sign In
        </button>
    </form>

    <div class="demo-creds">
        <strong>Demo Credentials:</strong><br>
        👑 Admin: admin@bytez.com / password<br>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$('#loginForm').on('submit', function() {
    $('.btn-login')
        .html('<i class="fas fa-spinner fa-spin me-2"></i> Signing in...')
        .prop('disabled', true);
});
</script>
</body>
</html>