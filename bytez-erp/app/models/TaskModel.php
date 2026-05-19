<?php
class TaskModel {
    public $db;

    public function __construct() { $this->db = getDB(); }

    public function getAll() {
        $result = $this->db->query("
            SELECT t.*, p.title as project_name, 
            u.name as assigned_name, c.name as created_name
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN users u ON t.assigned_to = u.id
            LEFT JOIN users c ON t.created_by = c.id
            ORDER BY t.created_at DESC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT t.*, p.title as project_name,
            u.name as assigned_name
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN users u ON t.assigned_to = u.id
            WHERE t.id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByProject($project_id) {
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

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO tasks 
            (title, description, project_id, assigned_to, created_by, priority, status, deadline)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('ssiissss',
            $data['title'],
            $data['description'],
            $data['project_id'],
            $data['assigned_to'],
            $data['created_by'],
            $data['priority'],
            $data['status'],
            $data['deadline']
        );
        $result = $stmt->execute();
        $this->lastId = $this->db->insert_id;
        return $result;
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE tasks SET title=?, description=?, project_id=?,
            assigned_to=?, priority=?, status=?, deadline=?
            WHERE id=?
        ");
        $stmt->bind_param('ssiisssi',
            $data['title'],
            $data['description'],
            $data['project_id'],
            $data['assigned_to'],
            $data['priority'],
            $data['status'],
            $data['deadline'],
            $id
        );
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id=?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function getComments($task_id) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as user_name
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.task_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->bind_param('i', $task_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addComment($task_id, $user_id, $comment) {
        $stmt = $this->db->prepare("
            INSERT INTO comments (task_id, user_id, comment) VALUES (?, ?, ?)
        ");
        $stmt->bind_param('iis', $task_id, $user_id, $comment);
        return $stmt->execute();
    }

    public function getAttachments($task_id) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.name as user_name
            FROM attachments a
            JOIN users u ON a.user_id = u.id
            WHERE a.task_id = ?
            ORDER BY a.uploaded_at DESC
        ");
        $stmt->bind_param('i', $task_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addAttachment($data) {
        $stmt = $this->db->prepare("
            INSERT INTO attachments (task_id, user_id, file_name, file_path, file_type)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('iisss',
            $data['task_id'],
            $data['user_id'],
            $data['file_name'],
            $data['file_path'],
            $data['file_type']
        );
        return $stmt->execute();
    }
}