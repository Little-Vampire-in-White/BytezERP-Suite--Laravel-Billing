<?php
class ProjectModel {
    public $db;

    public function __construct() { $this->db = getDB(); }

    public function getAll() {
        $result = $this->db->query("
            SELECT p.*, c.company_name, u.name as manager_name
            FROM projects p
            LEFT JOIN clients c ON p.client_id = c.id
            LEFT JOIN users u ON p.manager_id = u.id
            ORDER BY p.created_at DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, c.company_name, u.name as manager_name
            FROM projects p
            LEFT JOIN clients c ON p.client_id = c.id
            LEFT JOIN users u ON p.manager_id = u.id
            WHERE p.id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO projects 
            (title, description, client_id, manager_id, start_date, deadline, budget, status, progress)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('ssiissdsi',
            $data['title'],
            $data['description'],
            $data['client_id'],
            $data['manager_id'],
            $data['start_date'],
            $data['deadline'],
            $data['budget'],
            $data['status'],
            $data['progress']
        );
        $result = $stmt->execute();
        $this->lastId = $this->db->insert_id;
        return $result;
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE projects 
            SET title=?, description=?, client_id=?, manager_id=?,
            start_date=?, deadline=?, budget=?, status=?, progress=? 
            WHERE id=?
        ");
        $stmt->bind_param('ssiissdsii',
            $data['title'],
            $data['description'],
            $data['client_id'],
            $data['manager_id'],
            $data['start_date'],
            $data['deadline'],
            $data['budget'],
            $data['status'],
            $data['progress'],
            $id
        );
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM projects WHERE id=?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function getMembers($project_id) {
        $stmt = $this->db->prepare("
            SELECT u.id, u.name, u.role, u.email
            FROM project_members pm
            JOIN users u ON pm.user_id = u.id
            WHERE pm.project_id = ?
        ");
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addMember($project_id, $user_id) {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO project_members (project_id, user_id) VALUES (?, ?)
        ");
        $stmt->bind_param('ii', $project_id, $user_id);
        return $stmt->execute();
    }

    public function removeMembers($project_id) {
        $stmt = $this->db->prepare("DELETE FROM project_members WHERE project_id=?");
        $stmt->bind_param('i', $project_id);
        return $stmt->execute();
    }

    public function getTasks($project_id) {
        $stmt = $this->db->prepare("
            SELECT t.*, u.name as assigned_name
            FROM tasks t
            LEFT JOIN users u ON t.assigned_to = u.id
            WHERE t.project_id = ?
            ORDER BY t.created_at DESC
        ");
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}