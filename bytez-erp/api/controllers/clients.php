<?php
$db = getDB();

switch ($method) {
    case 'GET':
        if ($id) {
            // Get single client
            $stmt = $db->prepare("
                SELECT c.*,
                (SELECT COUNT(*) FROM projects p WHERE p.client_id = c.id) as project_count
                FROM clients c WHERE c.id = ?
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $client = $stmt->get_result()->fetch_assoc();

            if (!$client) {
                ApiResponse::error('Client not found', 404);
            }
            ApiResponse::success($client, 'Client retrieved');
        } else {
            // Get all clients
            $result  = $db->query("
                SELECT c.*,
                (SELECT COUNT(*) FROM projects p WHERE p.client_id = c.id) as project_count
                FROM clients c ORDER BY c.created_at DESC
            ");
            $clients = $result->fetch_all(MYSQLI_ASSOC);
            ApiResponse::success($clients, 'Clients retrieved', 200);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['company_name'])) {
            ApiResponse::error('Company name is required', 400);
        }

        $stmt = $db->prepare("
            INSERT INTO clients (company_name, industry, contact_name, phone, email, address, status, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $status     = $input['status'] ?? 'active';
        $created_by = $_SESSION['api_user_id'];
        $stmt->bind_param('sssssssi',
            $input['company_name'],
            $input['industry'] ?? '',
            $input['contact_name'] ?? '',
            $input['phone'] ?? '',
            $input['email'] ?? '',
            $input['address'] ?? '',
            $status,
            $created_by
        );
        $stmt->execute();
        $newId = $db->insert_id;

        $stmt2 = $db->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt2->bind_param('i', $newId);
        $stmt2->execute();
        $client = $stmt2->get_result()->fetch_assoc();

        ApiResponse::success($client, 'Client created', 201);
        break;

    case 'PUT':
        if (!$id) ApiResponse::error('Client ID required', 400);

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['company_name'])) {
            ApiResponse::error('Company name is required', 400);
        }

        $stmt = $db->prepare("
            UPDATE clients SET company_name=?, industry=?, contact_name=?,
            phone=?, email=?, address=?, status=? WHERE id=?
        ");
        $status = $input['status'] ?? 'active';
        $stmt->bind_param('sssssssi',
            $input['company_name'],
            $input['industry'] ?? '',
            $input['contact_name'] ?? '',
            $input['phone'] ?? '',
            $input['email'] ?? '',
            $input['address'] ?? '',
            $status,
            $id
        );
        $stmt->execute();
        ApiResponse::success(['id' => $id], 'Client updated');
        break;

    case 'DELETE':
        if (!$id) ApiResponse::error('Client ID required', 400);

        $stmt = $db->prepare("DELETE FROM clients WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        ApiResponse::success(null, 'Client deleted');
        break;

    default:
        ApiResponse::error('Method not allowed', 405);
}