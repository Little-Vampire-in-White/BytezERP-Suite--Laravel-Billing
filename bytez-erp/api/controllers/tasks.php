<?php
$db = getDB();

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $db->prepare("
                SELECT t.*, p.title as project_name, u.name as assigned_name
                FROM tasks t
                LEFT JOIN projects p ON t.project_id = p.id
                LEFT JOIN users u ON t.assigned_to = u.id
                WHERE t.id = ?
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $task = $stmt->get_result()->fetch_assoc();
            if (!$task) ApiResponse::error('Task not found', 404);
            ApiResponse::success($task, 'Task retrieved');
        } else {
            $result = $db->query("
                SELECT t.*, p.title as project_name, u.name as assigned_name
                FROM tasks t
                LEFT JOIN projects p ON t.project_id = p.id
                LEFT JOIN users u ON t.assigned_to = u.id
                ORDER BY t.created_at DESC
            ");
            $tasks = $result->fetch_all(MYSQLI_ASSOC);
            ApiResponse::success($tasks, 'Tasks retrieved');
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['title']))      ApiResponse::error('Title is required', 400);
        if (empty($input['project_id'])) ApiResponse::error('Project ID is required', 400);

        $stmt = $db->prepare("
            INSERT INTO tasks (title, description, project_id, assigned_to, created_by, priority, status, deadline)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $priority   = $input['priority'] ?? 'medium';
        $status     = $input['status'] ?? 'todo';
        $created_by = $_SESSION['api_user_id'];
        $stmt->bind_param('ssiissss',
            $input['title'],
            $input['description'] ?? '',
            $input['project_id'],
            $input['assigned_to'] ?? null,
            $created_by,
            $priority,
            $status,
            $input['deadline'] ?? ''
        );
        $stmt->execute();
        $newId = $db->insert_id;
        ApiResponse::success(['id' => $newId], 'Task created', 201);
        break;

    case 'PUT':
        if (!$id) ApiResponse::error('Task ID required', 400);
        $input = json_decode(file_get_contents('php://input'), true);

        $stmt = $db->prepare("
            UPDATE tasks SET title=?, status=?, priority=?, assigned_to=? WHERE id=?
        ");
        $status   = $input['status'] ?? 'todo';
        $priority = $input['priority'] ?? 'medium';
        $stmt->bind_param('sssii',
            $input['title'],
            $status,
            $priority,
            $input['assigned_to'] ?? null,
            $id
        );
        $stmt->execute();
        ApiResponse::success(['id' => $id], 'Task updated');
        break;

    case 'DELETE':
        if (!$id) ApiResponse::error('Task ID required', 400);
        $stmt = $db->prepare("DELETE FROM tasks WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        ApiResponse::success(null, 'Task deleted');
        break;

    default:
        ApiResponse::error('Method not allowed', 405);
}