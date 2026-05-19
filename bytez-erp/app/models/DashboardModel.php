<?php
class DashboardModel {
    private $db;

    public function __construct() { 
        $this->db = getDB(); 
    }

    public function getStats() {
        return [
            'total_clients'   => $this->db->query("SELECT COUNT(*) as c FROM clients")->fetch_assoc()['c'],
            'active_projects' => $this->db->query("SELECT COUNT(*) as c FROM projects WHERE status='in_progress'")->fetch_assoc()['c'],
            'completed_tasks' => $this->db->query("SELECT COUNT(*) as c FROM tasks WHERE status='completed'")->fetch_assoc()['c'],
            'total_employees' => $this->db->query("SELECT COUNT(*) as c FROM users WHERE role='employee'")->fetch_assoc()['c'],
            'total_projects'  => $this->db->query("SELECT COUNT(*) as c FROM projects")->fetch_assoc()['c'],
            'pending_tasks'   => $this->db->query("SELECT COUNT(*) as c FROM tasks WHERE status='todo'")->fetch_assoc()['c'],
        ];
    }

    public function getRecentProjects() {
        $result = $this->db->query("
            SELECT p.*, c.company_name, u.name as manager_name
            FROM projects p
            LEFT JOIN clients c ON p.client_id = c.id
            LEFT JOIN users u ON p.manager_id = u.id
            ORDER BY p.created_at DESC LIMIT 5
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRecentTasks() {
        $result = $this->db->query("
            SELECT t.*, p.title as project_name, u.name as assigned_name
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN users u ON t.assigned_to = u.id
            ORDER BY t.created_at DESC LIMIT 5
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTaskStatusCounts() {
        $result = $this->db->query("SELECT status, COUNT(*) as count FROM tasks GROUP BY status");
        $data = ['todo' => 0, 'in_progress' => 0, 'completed' => 0];
        while ($row = $result->fetch_assoc()) { 
            $data[$row['status']] = $row['count']; 
        }
        return $data;
    }

    public function getProjectStatusCounts() {
        $result = $this->db->query("SELECT status, COUNT(*) as count FROM projects GROUP BY status");
        $data = ['pending' => 0, 'in_progress' => 0, 'completed' => 0, 'cancelled' => 0];
        while ($row = $result->fetch_assoc()) { 
            $data[$row['status']] = $row['count']; 
        }
        return $data;
    }
}