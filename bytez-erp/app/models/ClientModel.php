<?php
class ClientModel {
    private $db;

    public function __construct() { $this->db = getDB(); }

    public function getAll() {
        $result = $this->db->query("
            SELECT c.*, u.name as created_by_name,
            (SELECT COUNT(*) FROM projects p WHERE p.client_id = c.id) as project_count
            FROM clients c
            LEFT JOIN users u ON c.created_by = u.id
            ORDER BY c.created_at DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO clients (company_name, industry, contact_name, phone, email, address, status, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('sssssssi',
            $data['company_name'], $data['industry'], $data['contact_name'],
            $data['phone'], $data['email'], $data['address'],
            $data['status'], $data['created_by']
        );
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE clients SET company_name=?, industry=?, contact_name=?,
            phone=?, email=?, address=?, status=? WHERE id=?
        ");
        $stmt->bind_param('sssssssi',
            $data['company_name'], $data['industry'], $data['contact_name'],
            $data['phone'], $data['email'], $data['address'],
            $data['status'], $id
        );
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM clients WHERE id=?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}