<?php
$db = getDB();

if ($method === 'POST') {
    $input    = json_decode(file_get_contents('php://input'), true);
    $email    = trim($input['email'] ?? '');
    $password = trim($input['password'] ?? '');

    if (empty($email) || empty($password)) {
        ApiResponse::error('Email and password are required', 400);
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !password_verify($password, $user['password'])) {
        ApiResponse::error('Invalid email or password', 401);
    }

    $auth  = new ApiAuth();
    $token = $auth->generateToken($user['id'], $user['role']);

    ApiResponse::success([
        'token' => $token,
        'user'  => [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]
    ], 'Login successful');
} else {
    ApiResponse::error('Method not allowed', 405);
}