<?php
// require_once 'app/models/ProjectModel.php';
// require_once 'app/models/ClientModel.php';
// require_once 'app/models/UserModel.php';

class ProjectsController {
    private $model;

    public function __construct() {
        // $this->model = new ProjectModel();
    }

    private function callApi($endpoint, $method = 'GET', $data = null) {
        $ch = curl_init("http://127.0.0.1:5000/api" . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            }
        }
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true)['data'] ?? [];
    }

    public function index($param = null) {
        requireLogin();
        $projects  = $this->callApi('/projects');
        $pageTitle = 'Projects';
        require_once 'views/layouts/header.php';
        require_once 'views/projects/index.php';
        require_once 'views/layouts/footer.php';
    }

    public function create($param = null) {
        requireRole('admin', 'manager');
        $error   = '';
        $clients = $this->callApi('/clients');
        $users   = $this->callApi('/users');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title'       => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'client_id'   => (int)($_POST['client_id'] ?? 0),
                'manager_id'  => (int)($_POST['manager_id'] ?? 0),
                'start_date'  => $_POST['start_date'] ?? '',
                'deadline'    => $_POST['deadline'] ?? '',
                'budget'      => (float)($_POST['budget'] ?? 0),
                'status'      => $_POST['status'] ?? 'pending',
                'progress'    => (int)($_POST['progress'] ?? 0),
            ];
            if (empty($data['title'])) {
                $error = 'Project title is required.';
            } elseif (empty($data['client_id'])) {
                $error = 'Please select a client.';
            } elseif (empty($data['start_date'])) {
                $error = 'Start date is required.';
            } elseif (empty($data['deadline'])) {
                $error = 'Deadline is required.';
            } elseif ($data['deadline'] < $data['start_date']) {
                $error = 'Deadline cannot be before start date.';
            } elseif ($data['budget'] < 0) {
                $error = 'Budget cannot be negative.';
            } elseif ($data['progress'] < 0 || $data['progress'] > 100) {
                $error = 'Progress must be between 0 and 100.';
            } else {
                $data['members'] = $_POST['members'] ?? [];
                $this->callApi('/projects', 'POST', $data);
                header('Location: /Codebytez/projects/index');
                exit();
            }
        }
        $pageTitle = 'Create Project';
        require_once 'views/layouts/header.php';
        require_once 'views/projects/create.php';
        require_once 'views/layouts/footer.php';
    }

    public function view($id = null) {
        requireLogin();
        $project   = $this->callApi("/projects/$id");
        if (!$project) { die('Project not found'); }
        $members   = $this->callApi("/projects/$id/members");
        $tasks     = $this->callApi("/projects/$id/tasks");
        $pageTitle = $project['title'];
        require_once 'views/layouts/header.php';
        require_once 'views/projects/view.php';
        require_once 'views/layouts/footer.php';
    }

    public function edit($id = null) {
        requireRole('admin', 'manager');
        $project = $this->callApi("/projects/$id");
        if (!$project) { die('Project not found'); }
        $clients = $this->callApi('/clients');
        $users   = $this->callApi('/users');
        $members = $this->callApi("/projects/$id/members");
        $memberIds = array_column($members, 'id');
        $error   = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title'       => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'client_id'   => (int)($_POST['client_id'] ?? 0),
                'manager_id'  => (int)($_POST['manager_id'] ?? 0),
                'start_date'  => $_POST['start_date'] ?? '',
                'deadline'    => $_POST['deadline'] ?? '',
                'budget'      => (float)($_POST['budget'] ?? 0),
                'status'      => $_POST['status'] ?? 'pending',
                'progress'    => (int)($_POST['progress'] ?? 0),
            ];
            if (empty($data['title'])) {
                $error = 'Project title is required.';
            } elseif (empty($data['client_id'])) {
                $error = 'Please select a client.';
            } elseif (empty($data['start_date'])) {
                $error = 'Start date is required.';
            } elseif (empty($data['deadline'])) {
                $error = 'Deadline is required.';
            } elseif ($data['deadline'] < $data['start_date']) {
                $error = 'Deadline cannot be before start date.';
            } elseif ($data['budget'] < 0) {
                $error = 'Budget cannot be negative.';
            } elseif ($data['progress'] < 0 || $data['progress'] > 100) {
                $error = 'Progress must be between 0 and 100.';
            } else {
                $data['members'] = $_POST['members'] ?? [];
                $this->callApi("/projects/$id", 'PUT', $data);
                header('Location: /Codebytez/projects/index');
                exit();
            }
        }
        $pageTitle = 'Edit Project';
        require_once 'views/layouts/header.php';
        require_once 'views/projects/edit.php';
        require_once 'views/layouts/footer.php';
    }

    public function delete($id = null) {
        requireRole('admin');
        $this->callApi("/projects/$id", 'DELETE');
        header('Location: /Codebytez/projects/index');
        exit();
    }
}