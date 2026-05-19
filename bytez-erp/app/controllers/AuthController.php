<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'app/models/UserModel.php';

class AuthController {
    public function login($param = null) {
        $__dbgLog = __DIR__ . '/../../storage/debug-auth.log';
        if (!is_dir(dirname($__dbgLog))) {
            @mkdir(dirname($__dbgLog), 0777, true);
        }
        $__dbg = function(string $line) use ($__dbgLog) {
            @file_put_contents($__dbgLog, '[' . date('c') . '] ' . $line . PHP_EOL, FILE_APPEND);
        };
        $__dbg('AUTH CTRL login method=' . ($_SERVER['REQUEST_METHOD'] ?? '') . ' url=' . ($_SERVER['REQUEST_URI'] ?? '') . ' sid=' . session_id());
        $__dbg('AUTH CTRL before isLoggedIn user_id=' . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null') . ' sid=' . (function_exists('session_id') ? session_id() : ''));

        // Render login page.
        // Front controller (/bytez-erp/index.php) performs login gating.

        $error = '';
        // Use Node/SQLite-backed API login instead of MySQL.
        // Client posts form data; we call our API endpoint which authenticates against SQLite.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            if (empty($email) || empty($password)) {
                $error = 'Please fill in all fields.';
            } else {
                // Point to Node/SQLite-backed Bridge API (Port 5000) instead of local PHP API
                $apiUrl = 'http://127.0.0.1:5000/api/auth';
                $payload = json_encode(['email' => $email, 'password' => $password]);

                $ch = curl_init($apiUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_TIMEOUT => 10,
                ]);

                $resp = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlErr = curl_error($ch);
                curl_close($ch);

                if ($resp === false) {
                    $error = 'API request failed: ' . ($curlErr ?: 'unknown error');
                } else {
                    $data = json_decode($resp, true);
                    if ($httpCode >= 200 && $httpCode < 300 && !empty($data['data']['token'])) {
                        // Store user session minimally for UI gating.
                        $_SESSION['user_id'] = $data['data']['user']['id'];
                        $_SESSION['user_name'] = $data['data']['user']['name'];
                        $_SESSION['user_role'] = $data['data']['user']['role'];
                        $__dbg('AUTH CTRL Session set: user_id=' . $_SESSION['user_id'] . ' user_name=' . $_SESSION['user_name'] . ' user_role=' . $_SESSION['user_role'] . ' sid=' . session_id());
                        header('Location: /Codebytez/dashboard/index');
                        exit();
                    }

                    $error = $data['message'] ?? 'Invalid email or password.';
                }
            }
        }
        require_once 'views/auth/login.php';
    }

    public function logout($param = null) {
        session_destroy();
        header('Location: /Codebytez/auth/login');
        exit();
    }
}