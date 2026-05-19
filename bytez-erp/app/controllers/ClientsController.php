<?php
// require_once 'app/models/ClientModel.php'; // Bypassing MySQL-dependent model to avoid connection errors

class ClientsController {
    private $model;

    public function __construct() {
        // $this->model = new ClientModel();
    }

    private function callApi($endpoint, $method = 'GET', $data = null) {
        $ch = curl_init("http://127.0.0.1:5000/api" . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            }
        }
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true);
    }

    public function index($param = null) {
        requireLogin();

        $apiData = $this->callApi('/clients');
        
        $clients = array_map(function($client) {
            $c = (array)$client;
            return [
                'id'            => $c['id'] ?? 0,
                'company_name'  => $c['name'] ?? $c['company_name'] ?? 'N/A',
                'industry'      => (!empty($c['industry'])) ? $c['industry'] : 'N/A',
                'contact_name'  => (!empty($c['contact_name'])) ? $c['contact_name'] : 'N/A',
                'email'         => $c['email'] ?? 'N/A',
                'phone'         => $c['phone'] ?? 'N/A',
                'project_count' => $c['project_count'] ?? 0,
                'status'        => $c['status'] ?? 'active'
            ];
        }, $apiData['data'] ?? []);

        $pageTitle = 'Clients';
        require_once 'views/layouts/header.php';
        require_once 'views/clients/index.php';
        require_once 'views/layouts/footer.php';
    }

    public function create($param = null) {
        requireRole('admin', 'manager');
        $error   = '';
        $success = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'company_name' => trim($_POST['company_name'] ?? ''),
                'industry'     => trim($_POST['industry'] ?? ''),
                'contact_name' => trim($_POST['contact_name'] ?? ''),
                'phone'        => trim($_POST['phone'] ?? ''),
                'email'        => trim($_POST['email'] ?? ''),
                'address'      => trim($_POST['address'] ?? ''),
                'status'       => $_POST['status'] ?? 'active',
                'created_by'   => $_SESSION['user_id'],
            ];
            if (empty($data['company_name'])) {
                $error = 'Company name is required.';
            } else {
                $apiResponse = $this->callApi('/clients', 'POST', $data);
                
                if (isset($apiResponse['success']) && $apiResponse['success']) {
                    header('Location: /Codebytez/clients/index');
                    exit();
                } else {
                    $error = $apiResponse['message'] ?? 'Failed to add client. Check API logs.';
                }
            }
        }
        $pageTitle = 'Add Client';
        require_once 'views/layouts/header.php';
        require_once 'views/clients/create.php';
        require_once 'views/layouts/footer.php';
    }

    public function edit($id = null) {
        requireRole('admin', 'manager');

        $apiData = $this->callApi('/clients/' . $id);
        $client = $apiData['data'] ?? null;
        if ($client) {
            $client['company_name'] = $client['company_name'] ?? $client['name'] ?? '';
        }

        $error   = '';
        if (!$client) { die('Client not found'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'company_name' => trim($_POST['company_name'] ?? ''),
                'industry'     => trim($_POST['industry'] ?? ''),
                'contact_name' => trim($_POST['contact_name'] ?? ''),
                'phone'        => trim($_POST['phone'] ?? ''),
                'email'        => trim($_POST['email'] ?? ''),
                'address'      => trim($_POST['address'] ?? ''),
                'status'       => $_POST['status'] ?? 'active',
            ];
            if (empty($data['company_name'])) {
                $error = 'Company name is required.';
            } else {
                $apiResponse = $this->callApi('/clients/' . $id, 'PUT', $data);
                
                if (isset($apiResponse['success']) && $apiResponse['success']) {
                    header('Location: /Codebytez/clients/index');
                    exit();
                } else {
                    $error = $apiResponse['message'] ?? 'Failed to update client.';
                }
            }
        }
        $pageTitle = 'Edit Client';
        require_once 'views/layouts/header.php';
        require_once 'views/clients/edit.php';
        require_once 'views/layouts/footer.php';
    }

    public function delete($id = null) {
        requireRole('admin');

        $this->callApi('/clients/' . $id, 'DELETE');

        header('Location: /Codebytez/clients/index');
        exit();
    }
}