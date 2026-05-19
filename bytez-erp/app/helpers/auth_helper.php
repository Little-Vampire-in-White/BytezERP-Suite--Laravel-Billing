<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /Codebytez/auth/login');
        exit();
    }
}

function requireRole(...$roles) {
    requireLogin();
    if (!in_array($_SESSION['user_role'], $roles)) {
        http_response_code(403);
        die('<h2 style="text-align:center;margin-top:100px;color:red;">⛔ Access Denied</h2>');
    }
}

function currentUser() {
    return [
        'id'   => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'role' => $_SESSION['user_role'] ?? null,
    ];
}