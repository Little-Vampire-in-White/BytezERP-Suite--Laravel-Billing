<?php
// require_once 'app/models/AiModel.php';
// require_once 'app/models/ProjectModel.php';

class AiController {

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
        $decoded = json_decode($res, true);
        return $decoded['data'] ?? [];
    }

    public function index($param = null) {
        requireLogin();
        $logs     = $this->callApi('/ai/logs/' . $_SESSION['user_id']);
        $pageTitle = 'AI Tools';
        require_once 'views/layouts/header.php';
        require_once 'views/ai/index.php';
        require_once 'views/layouts/footer.php';
    }

    public function generate($param = null) {
        requireLogin();
        header('Content-Type: application/json');

        $input       = trim($_POST['input'] ?? '');
        $contentType = trim($_POST['content_type'] ?? 'social_media');

        if (empty($input)) {
            echo json_encode(['error' => 'Input is required']);
            return;
        }

        $prompts = [
            'social_media' => "You are a social media expert. Create an engaging social media caption for: {$input}. Make it catchy with relevant emojis and hashtags.",
            'blog_idea'    => "You are a content strategist. Generate 3 creative blog post ideas with titles and brief outlines for the topic: {$input}.",
            'ad_copy'      => "You are an advertising expert. Write compelling ad copy for: {$input}. Include a headline, body text, and call-to-action.",
            'email'        => "You are an email marketing expert. Write a professional marketing email for: {$input}. Include subject line, body, and sign-off.",
            'seo'          => "You are an SEO expert. Generate SEO-optimized meta title, meta description, and 10 relevant keywords for: {$input}.",
        ];

        $prompt = $prompts[$contentType] ?? $prompts['social_media'];
        $result = $this->callOpenAI($prompt);

        if ($result['success']) {
            $this->callApi('/ai/logs', 'POST', [
                'user_id'  => $_SESSION['user_id'],
                'prompt'   => $input,
                'response' => $result['text'],
                'type'     => 'content'
            ]);
            echo json_encode(['success' => true, 'text' => $result['text']]);
        } else {
            echo json_encode(['error' => $result['error']]);
        }
    }

    public function insights($param = null) {
        requireLogin();
        header('Content-Type: application/json');

        $projects     = $this->callApi('/projects');

        $total     = count($projects);
        $completed = count(array_filter($projects, fn($p) => $p['status'] === 'completed'));
        $inProgress= count(array_filter($projects, fn($p) => $p['status'] === 'in_progress'));
        $pending   = count(array_filter($projects, fn($p) => $p['status'] === 'pending'));

        $projectList = '';
        foreach ($projects as $p) {
            $clientName = $p['company_name'] ?? 'N/A';
            $projectList .= "- {$p['title']} (Client: {$clientName}, Status: {$p['status']}, Progress: {$p['progress']}%, Deadline: {$p['deadline']})\n";
        }

        $prompt = "You are a business analyst for a digital marketing agency. Analyze these projects and provide insights:

        {$projectList}

        Summary: Total={$total}, Completed={$completed}, In Progress={$inProgress}, Pending={$pending}

        Please provide:
        1. Overall performance assessment
        2. Which project is performing best and why
        3. Projects at risk (overdue or low progress)
        4. Recommendations to improve productivity
        5. Content strategy suggestions

        Be specific and actionable.";

        $result = $this->callOpenAI($prompt);

        if ($result['success']) {
            $this->callApi('/ai/logs', 'POST', [
                'user_id'  => $_SESSION['user_id'],
                'prompt'   => 'Project Insights Analysis',
                'response' => $result['text'],
                'type'     => 'insight'
            ]);
            echo json_encode(['success' => true, 'text' => $result['text']]);
        } else {
            echo json_encode(['error' => $result['error']]);
        }
    }

    private function callOpenAI($prompt) {
        $envFile = __DIR__ . '/../../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false) {
                    [$key, $value] = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }
        $apiKey = $_ENV['GROQ_API_KEY'] ?? '';

        $data = [
            'model'    => 'llama3-8b-8192',
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You are a helpful digital marketing assistant.'
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens'  => 800,
            'temperature' => 0.7,
        ];

        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response) {
            return ['success' => false, 'error' => 'Failed to connect to AI service.'];
        }

        $decoded = json_decode($response, true);

        if ($httpCode !== 200) {
            $errorMsg = $decoded['error']['message'] ?? 'Unknown API error';
            return ['success' => false, 'error' => $errorMsg];
        }

        $text = $decoded['choices'][0]['message']['content'] ?? '';
        return ['success' => true, 'text' => trim($text)];
    }
}