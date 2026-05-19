<?php
// require_once 'app/models/DashboardModel.php'; // Bypassing MySQL-dependent model to avoid connection errors

class DashboardController {
    private function callApi($endpoint) {
        $ch = curl_init("http://127.0.0.1:5000/api" . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true);
    }

    public function index($param = null) {
        requireLogin();

        $apiData = $this->callApi('/dashboard');
        $data = $apiData['data'] ?? [];

        // Populate stats and chart data from the API response
        $stats          = array_merge([
            'total_clients'   => 0,
            'total_projects'  => 0,
            'total_tasks'     => 0,
            'total_users'     => 0,
            'active_projects' => 0,
            'completed_tasks' => 0,
            'total_employees' => 0,
            'cancelled'       => 0
        ], (array)($data['stats'] ?? []));
        $recentProjects = $data['recent_projects'] ?? [];
        $recentTasks    = $data['recent_tasks'] ?? [];
        $taskChart      = $data['task_chart'] ?? [];
        $projectChart   = $data['project_chart'] ?? [];

        $pageTitle      = 'Dashboard';
        require_once 'views/layouts/header.php';
        require_once 'views/dashboard/index.php';
        require_once 'views/layouts/footer.php';
    }
}