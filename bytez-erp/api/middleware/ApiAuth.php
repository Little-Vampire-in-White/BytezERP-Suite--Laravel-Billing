<?php
class ApiAuth {
    private $db;
    private $secretKey = 'bytez_erp_secret_2024';

    public function __construct() {
        $this->db = getDB();
    }

    public function verify() {
        $headers = getallheaders();
        $token   = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        $token   = str_replace('Bearer ', '', $token);

        if (empty($token)) return false;

        $decoded = $this->decodeToken($token);
        if (!$decoded) return false;

        // Set user session for API
        $_SESSION['api_user_id']   = $decoded['user_id'];
        $_SESSION['api_user_role'] = $decoded['role'];

        return true;
    }

    public function generateToken($userId, $role) {
        $payload = base64_encode(json_encode([
            'user_id'   => $userId,
            'role'      => $role,
            'issued_at' => time(),
            'expires'   => time() + (24 * 60 * 60), // 24 hours
        ]));
        $signature = hash_hmac('sha256', $payload, $this->secretKey);
        return $payload . '.' . $signature;
    }

    private function decodeToken($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 2) return false;

        [$payload, $signature] = $parts;
        $expectedSig = hash_hmac('sha256', $payload, $this->secretKey);

        if (!hash_equals($expectedSig, $signature)) return false;

        $data = json_decode(base64_decode($payload), true);
        if (!$data) return false;

        if ($data['expires'] < time()) return false;

        return $data;
    }
}