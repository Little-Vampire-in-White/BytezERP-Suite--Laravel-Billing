<?php
$db = getDB();

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $db->prepare("
                SELECT p.*, c.company_name, u.name as manager_name
                FROM projects p
                LEFT JOIN clients c ON p.client_id = c.id
                LEFT JOIN users u ON p.manager_id = u.id
                WHERE p.id = ?
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $project = $stmt->get_result()->fetch_assoc();
            if (!$project) ApiResponse::error('Project not found', 404);
            ApiResponse::success($project, 'Project retrieved');
        } else {
            $result   = $db->query("
                SELECT p.*, c.company_name, u.name as manager_name
                FROM projects p
                LEFT JOIN clients c ON p.client_id = c.id
                LEFT JOIN users u ON p.manager_id = u.id
                ORDER BY p.created_at DESC
            ");
            $projects = $result->fetch_all(MYSQLI_ASSOC);
            ApiResponse::success($projects, 'Projects retrieved');
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['title'])) ApiResponse::error('Title is required', 400);
        if (empty($input['client_id'])) ApiResponse::error('Client ID is required', 400);

        $stmt = $db->prepare("
            INSERT INTO projects (title, description, client_id, manager_id, start_date, deadline, budget, status, progress)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $status   = $input['status'] ?? 'pending';
        $progress = (int)($input['progress'] ?? 0);
        $budget   = (float)($input['budget'] ?? 0);
        $stmt->bind_param('ssiissdsi',
            $input['title'],
            $input['description'] ?? '',
            $input['client_id'],
            $input['manager_id'] ?? null,
            $input['start_date'] ?? date('Y-m-d'),
            $input['deadline'] ?? '',
            $budget,
            $status,
            $progress
        );
        $stmt->execute();
        $newId = $db->insert_id;
        ApiResponse::success(['id' => $newId], 'Project created', 201);
        break;

    case 'PUT':
        if (!$id) ApiResponse::error('Project ID required', 400);
        $input = json_decode(file_get_contents('php://input'), true);

        $stmt = $db->prepare("
            UPDATE projects SET title=?, description=?, status=?, progress=? WHERE id=?
        ");
        $status   = $input['status'] ?? 'pending';
        $progress = (int)($input['progress'] ?? 0);
        $stmt->bind_param('sssii',
            $input['title'],
            $input['description'] ?? '',
            $status,
            $progress,
            $id
        );
        $stmt->execute();
        ApiResponse::success(['id' => $id], 'Project updated');
        break;

    case 'DELETE':
        if (!$id) ApiResponse::error('Project ID required', 400);
        $stmt = $db->prepare("DELETE FROM projects WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        ApiResponse::success(null, 'Project deleted');
        break;

    default:
        ApiResponse::error('Method not allowed', 405);
}