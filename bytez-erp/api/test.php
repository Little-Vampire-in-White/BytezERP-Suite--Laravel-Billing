<?php
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
    <title>API Tester - Bytez ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>
<body class="bg-light p-4">
<div class="container">
    <h3 class="mb-4">🚀 Bytez ERP - API Tester</h3>

    <!-- Login -->
    <div class="card p-4 mb-4">
        <h5 class="fw-bold mb-3">Step 1 — Get Token (Login)</h5>
        <div class="row g-2 mb-3">
            <div class="col-md-5">
                <input type="email" id="loginEmail" class="form-control"
                       value="admin@bytez.com" placeholder="Email">
            </div>
            <div class="col-md-5">
                <input type="password" id="loginPassword" class="form-control"
                       value="password" placeholder="Password">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" id="loginBtn">Login</button>
            </div>
        </div>
        <div id="tokenBox" class="alert alert-success d-none">
            <strong>Token:</strong>
            <small id="tokenText" class="d-block mt-1" style="word-break:break-all;"></small>
        </div>
    </div>

    <!-- Test Endpoints -->
    <div class="card p-4 mb-4">
        <h5 class="fw-bold mb-3">Step 2 — Test Endpoints</h5>
        <div class="row g-2 mb-3">
            <div class="col-md-2">
                <select id="reqMethod" class="form-select">
                    <option>GET</option>
                    <option>POST</option>
                    <option>PUT</option>
                    <option>DELETE</option>
                </select>
            </div>
            <div class="col-md-7">
                <select id="endpointSelect" class="form-select">
                    <optgroup label="Clients">
                        <option value="/Codebytez/api/clients">GET /api/clients</option>
                        <option value="/Codebytez/api/clients/1">GET /api/clients/1</option>
                    </optgroup>
                    <optgroup label="Projects">
                        <option value="/Codebytez/api/projects">GET /api/projects</option>
                        <option value="/Codebytez/api/projects/1">GET /api/projects/1</option>
                    </optgroup>
                    <optgroup label="Tasks">
                        <option value="/Codebytez/api/tasks">GET /api/tasks</option>
                        <option value="/Codebytez/api/tasks/1">GET /api/tasks/1</option>
                    </optgroup>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-success w-100" id="sendBtn">Send Request</button>
            </div>
        </div>

        <!-- Request Body -->
        <div class="mb-3">
            <label class="form-label fw-bold">Request Body (JSON) — for POST/PUT</label>
            <textarea id="reqBody" class="form-control font-monospace" rows="4"
                      placeholder='{"key": "value"}'></textarea>
        </div>

        <!-- Response -->
        <label class="form-label fw-bold">Response</label>
        <div id="responseBox" class="bg-dark text-success p-3 rounded font-monospace"
             style="min-height:150px;white-space:pre-wrap;font-size:0.85rem;">
            // Response will appear here...
        </div>
    </div>

    <!-- API Docs -->
    <div class="card p-4">
        <h5 class="fw-bold mb-3">📚 API Endpoints Reference</h5>
        <table class="table table-sm table-bordered">
            <thead class="table-dark">
                <tr><th>Method</th><th>Endpoint</th><th>Description</th><th>Auth</th></tr>
            </thead>
            <tbody>
                <tr><td><span class="badge bg-success">POST</span></td><td>/api/auth</td><td>Login & get token</td><td>❌ No</td></tr>
                <tr><td><span class="badge bg-primary">GET</span></td><td>/api/clients</td><td>Get all clients</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-primary">GET</span></td><td>/api/clients/{id}</td><td>Get single client</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-success">POST</span></td><td>/api/clients</td><td>Create client</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-warning text-dark">PUT</span></td><td>/api/clients/{id}</td><td>Update client</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-danger">DELETE</span></td><td>/api/clients/{id}</td><td>Delete client</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-primary">GET</span></td><td>/api/projects</td><td>Get all projects</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-primary">GET</span></td><td>/api/projects/{id}</td><td>Get single project</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-success">POST</span></td><td>/api/projects</td><td>Create project</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-warning text-dark">PUT</span></td><td>/api/projects/{id}</td><td>Update project</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-danger">DELETE</span></td><td>/api/projects/{id}</td><td>Delete project</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-primary">GET</span></td><td>/api/tasks</td><td>Get all tasks</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-primary">GET</span></td><td>/api/tasks/{id}</td><td>Get single task</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-success">POST</span></td><td>/api/tasks</td><td>Create task</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-warning text-dark">PUT</span></td><td>/api/tasks/{id}</td><td>Update task</td><td>✅ Yes</td></tr>
                <tr><td><span class="badge bg-danger">DELETE</span></td><td>/api/tasks/{id}</td><td>Delete task</td><td>✅ Yes</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
var authToken = '';

// Login
$('#loginBtn').on('click', function() {
    $.ajax({
        url: '/Codebytez/api/auth',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            email: $('#loginEmail').val(),
            password: $('#loginPassword').val()
        }),
        success: function(res) {
            if (res.status === 'success') {
                authToken = res.data.token;
                $('#tokenText').text(authToken);
                $('#tokenBox').removeClass('d-none');
                $('#responseBox').text(JSON.stringify(res, null, 2));
            }
        },
        error: function(xhr) {
            $('#responseBox').text(JSON.stringify(JSON.parse(xhr.responseText), null, 2));
        }
    });
});

// Send Request
$('#sendBtn').on('click', function() {
    var method   = $('#reqMethod').val();
    var endpoint = $('#endpointSelect').val();
    var body     = $('#reqBody').val();

    var options = {
        url: endpoint,
        method: method,
        contentType: 'application/json',
        headers: {},
        success: function(res) {
            $('#responseBox').text(JSON.stringify(res, null, 2));
        },
        error: function(xhr) {
            try {
                $('#responseBox').text(JSON.stringify(JSON.parse(xhr.responseText), null, 2));
            } catch(e) {
                $('#responseBox').text(xhr.responseText);
            }
        }
    };

    if (authToken) {
        options.headers['Authorization'] = 'Bearer ' + authToken;
    }

    if ((method === 'POST' || method === 'PUT') && body) {
        options.data = body;
    }

    $.ajax(options);
});
</script>
</body>
</html>