<?php requireLogin(); $user = currentUser(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bytez ERP - <?= $pageTitle ?? 'Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --sidebar-bg: #1e1b4b; --sidebar-width: 250px; }
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
        }
        .sidebar .brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar .brand h4 { color: #fff; margin: 0; font-weight: 700; font-size: 1.3rem; }
        .sidebar .brand span { color: #818cf8; }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(99,102,241,0.3);
            color: #fff;
        }
        .sidebar .nav-link i { width: 20px; margin-right: 8px; }
        .main-content { margin-left: var(--sidebar-width); }
        .topbar {
            background: #fff;
            padding: 15px 25px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .topbar .page-title { font-weight: 700; color: #1e293b; margin: 0; }
        .avatar {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 0.85rem;
        }
        .content-area { padding: 25px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .stat-card { border-radius: 12px; padding: 20px; color: white; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="brand">
        <h4><i class="fas fa-bolt"></i> Bytez<span>ERP</span></h4>
        <small style="color:rgba(255,255,255,0.4)">Digital Agency</small>
    </div>
    <nav class="nav flex-column mt-3">
        <a href="/Codebytez/dashboard/index" class="nav-link <?= strpos($_SERVER['REQUEST_URI'],'/dashboard')!==false?'active':'' ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <a href="/Codebytez/clients/index" class="nav-link <?= strpos($_SERVER['REQUEST_URI'],'/clients')!==false?'active':'' ?>">
            <i class="fas fa-building"></i> Clients
        </a>
        <a href="/Codebytez/projects/index" class="nav-link <?= strpos($_SERVER['REQUEST_URI'],'/projects')!==false?'active':'' ?>">
            <i class="fas fa-diagram-project"></i> Projects
        </a>
        <a href="/Codebytez/tasks/index" class="nav-link <?= strpos($_SERVER['REQUEST_URI'],'/tasks')!==false?'active':'' ?>">
            <i class="fas fa-check-square"></i> Tasks
        </a>
        <a href="/Codebytez/ai/index" class="nav-link <?= strpos($_SERVER['REQUEST_URI'],'/ai')!==false?'active':'' ?>">
            <i class="fas fa-robot"></i> AI Tools
        </a>
        <?php if ($user['role'] === 'admin'): ?>
        <a href="/Codebytez/users/index" class="nav-link <?= strpos($_SERVER['REQUEST_URI'],'/users')!==false?'active':'' ?>">
            <i class="fas fa-users"></i> Users
        </a>
        <?php endif; ?>
        <hr style="border-color:rgba(255,255,255,0.1);margin:10px 20px;">
        <a href="/Codebytez/auth/logout" class="nav-link" style="color:#f87171;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>
<div class="main-content">
    <div class="topbar">
        <h5 class="page-title"><?= $pageTitle ?? 'Dashboard' ?></h5>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small"><?= htmlspecialchars($user['name']) ?></span>
            <div class="avatar"><?= strtoupper(substr($user['name'],0,2)) ?></div>
            <span class="badge bg-primary"><?= ucfirst($user['role']) ?></span>
        </div>
    </div>
    <div class="content-area">