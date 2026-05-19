<?php
class AiModel {
    private $db;

    public function __construct() { $this->db = getDB(); }

    public function saveLog($data) {
        $stmt = $this->db->prepare("
            INSERT INTO ai_logs (user_id, prompt, response, type)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('isss',
            $data['user_id'],
            $data['prompt'],
            $data['response'],
            $data['type']
        );
        return $stmt->execute();
    }

    public function getLogs($user_id) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.name as user_name
            FROM ai_logs a
            JOIN users u ON a.user_id = u.id
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC
            LIMIT 20
        ");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}