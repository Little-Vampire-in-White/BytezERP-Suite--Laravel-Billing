<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAll() {
        $result = $this->db->query("SELECT id, name, email, role, is_active, created_at FROM users ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt->bind_param('ssss', $data['name'], $data['email'], $hashed, $data['role']);
        return $stmt->execute();
    }
    public function updatePassword($id, $password) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt   = $this->db->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param('si', $hashed, $id);
        return $stmt->execute();
    }

    public function toggleStatus($id) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = !is_active WHERE id=?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}