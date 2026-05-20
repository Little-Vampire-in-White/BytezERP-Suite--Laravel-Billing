const path = require('path');
const fs = require('fs');

// Only load dotenv if it hasn't been passed through from the parent process
if (!process.env.PORT) {
    require('dotenv').config();
}

const express = require('express');
const sqlite3 = require('sqlite3').verbose();
const cors = require('cors');

const app = express();
const PORT = process.env.PORT || 5000;

// Middleware
app.use(cors());
app.use(express.json());

// Promisified Database Helpers
const dbGet = (sql, params = []) => new Promise((res, rej) => {
    db.get(sql, params, (err, row) => err ? rej(err) : res(row));
});

const dbAll = (sql, params = []) => new Promise((res, rej) => {
    db.all(sql, params, (err, rows) => err ? rej(err) : res(rows));
});

const dbRun = (sql, params = []) => new Promise((res, rej) => {
    db.run(sql, params, function(err) { err ? rej(err) : res(this); });
});

// Database Connection
// If DB_PATH isn't set, default to a local SQLite file named ./database.sqlite
const dbPath = process.env.DB_PATH && path.isAbsolute(process.env.DB_PATH) 
    ? process.env.DB_PATH 
    : path.resolve(__dirname, process.env.DB_PATH || './database.sqlite');


const db = new sqlite3.Database(dbPath, (err) => {
    if (err) {
        console.error('Error connecting to SQLite database:', err.message);
    } else {
        console.log('Connected to the SQLite database:', dbPath);
        
        // Initialize tables and seed default users
        initializeDatabase();
    }
});

function initializeDatabase() {
    db.serialize(() => {
        // 1. Create Table
        db.run(`CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT UNIQUE,
            password TEXT,
            role TEXT
        )`);

        db.run(`CREATE TABLE IF NOT EXISTS clients (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company TEXT,
            name TEXT,
            email TEXT,
            phone TEXT,
            address TEXT,
            industry TEXT,
            status TEXT DEFAULT 'active',
            created_at DATETIME,
            updated_at DATETIME
        )`);

        db.run(`CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            description TEXT,
            client_id INTEGER,
            manager_id INTEGER,
            start_date DATE,
            deadline DATE,
            budget DECIMAL(10,2) DEFAULT 0,
            status TEXT DEFAULT 'pending',
            progress INTEGER DEFAULT 0,
            created_at DATETIME,
            updated_at DATETIME
        )`);

        db.run(`CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            project_id INTEGER,
            status TEXT DEFAULT 'todo'
        )`);

        // 2. Migration: Ensure 'role' column exists
        db.all("PRAGMA table_info(users)", (err, columns) => {
            if (!err && columns && !columns.some(col => col.name === 'role')) {
                db.run("ALTER TABLE users ADD COLUMN role TEXT DEFAULT 'employee'");
            }
            
            // 3. Ensure Default Admin exists (Check by email specifically)
            db.get("SELECT id FROM users WHERE email = 'admin@bytez.com' COLLATE NOCASE", (err, row) => {
                if (!err && !row) {
                    db.run("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)", 
                        ['Admin User', 'admin@bytez.com', 'password', 'admin']);
                    console.log('Default admin account created: admin@bytez.com / password');
                }
            });

            db.get("SELECT id FROM users WHERE email = 'sync-admin@bytez.com' COLLATE NOCASE", (err, row) => {
                if (!err && !row) {
                    db.run("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)", 
                        ['Sync Service', 'sync-admin@bytez.com', 'sync-admin-password', 'admin']);
                }
            });
        });

        // Migration: Ensure 'industry', 'status', and 'contact_name' columns exist in clients table
        db.all("PRAGMA table_info(clients)", (err, columns) => {
            if (!err && columns) {
                if (!columns.some(col => col.name === 'company')) {
                    db.run("ALTER TABLE clients ADD COLUMN company TEXT");
                }
                if (!columns.some(col => col.name === 'user_id')) {
                    db.run("ALTER TABLE clients ADD COLUMN user_id INTEGER DEFAULT 1");
                }
                if (!columns.some(col => col.name === 'industry')) {
                    db.run("ALTER TABLE clients ADD COLUMN industry TEXT");
                }
                if (!columns.some(col => col.name === 'status')) {
                    db.run("ALTER TABLE clients ADD COLUMN status TEXT DEFAULT 'active'");
                }
                if (!columns.some(col => col.name === 'created_at')) {
                    db.run("ALTER TABLE clients ADD COLUMN created_at DATETIME");
                }
                if (!columns.some(col => col.name === 'updated_at')) {
                    db.run("ALTER TABLE clients ADD COLUMN updated_at DATETIME");
                }
            }
        });

        // Migration: Ensure 'projects' table has all necessary columns
        db.all("PRAGMA table_info(projects)", (err, columns) => {
            if (!err && columns) {
                const addColumn = (name, type) => {
                    if (!columns.some(col => col.name === name)) {
                        db.run(`ALTER TABLE projects ADD COLUMN ${name} ${type}`);
                    }
                };

                addColumn('description', 'TEXT');
                addColumn('manager_id', 'INTEGER');
                addColumn('start_date', 'DATE');
                addColumn('deadline', 'DATE');
                addColumn('budget', 'DECIMAL(10,2) DEFAULT 0');
                addColumn('created_at', 'DATETIME');
                addColumn('updated_at', 'DATETIME');
                // Fallback for older versions using 'title' instead of 'name'
                if (!columns.some(col => col.name === 'title')) {
                    db.run("ALTER TABLE projects ADD COLUMN title TEXT");
                }
            }
        });
    });
}

/**
 * Auth helpers
 */
app.post(['/api/auth', '/api'], (req, res) => {
    // Handle legacy PHP-style calls where url is passed as a query param
    if (req.path === '/api' && req.query.url !== 'auth') {
        return res.status(404).json({ status: 'error', message: 'Endpoint not found' });
    }

    const { email, password } = req.body;
    console.log(`Login attempt for: ${email}`);

    // Use COLLATE NOCASE to make email comparison case-insensitive
    const sql = `SELECT id, name, email, role FROM users WHERE email = ? COLLATE NOCASE AND password = ?`;
    
    db.get(sql, [email, password], (err, user) => {
        if (err) return res.status(500).json({ status: 'error', message: err.message });
        if (!user) {
            console.warn(`Auth failed: No matching user for ${email}`);
            return res.status(401).json({ status: 'error', message: 'Invalid email or password.' });
        }
        
        const token = Buffer.from(JSON.stringify({id: user.id, exp: Date.now() + 86400000})).toString('base64');
        res.json({
            status: 'success',
            data: { token, user }
        });
    });
});



/**
 * Get all invoices (Shared with Bytez-ERP)
 */
app.get('/api/invoices', (req, res) => {
    const sql = `
        SELECT i.*, c.name as client_name, c.email as client_email 
        FROM invoices i 
        JOIN clients c ON i.client_id = c.id 
        ORDER BY i.created_at DESC
    `;
    
    db.all(sql, [], (err, rows) => {
        if (err) {
            return res.status(500).json({ error: err.message });
        }
        res.json({
            status: 'success',
            data: rows
        });
    });
});

/**
 * Get specific invoice details with items
 */
app.get('/api/invoices/:id', (req, res) => {
    const invoiceId = req.params.id;
    const invoiceSql = `SELECT * FROM invoices WHERE id = ?`;
    const itemsSql = `SELECT * FROM invoice_items WHERE invoice_id = ?`;

    db.get(invoiceSql, [invoiceId], (err, invoice) => {
        if (err) return res.status(500).json({ error: err.message });
        if (!invoice) return res.status(404).json({ error: 'Invoice not found' });

        db.all(itemsSql, [invoiceId], (err, items) => {
            if (err) return res.status(500).json({ error: err.message });
            
            res.json({
                status: 'success',
                data: {
                    ...invoice,
                    items: items
                }
            });
        });
    });
});

/**
 * Get all clients (Shared with Bytez-ERP)
 */
app.get('/api/clients', (req, res) => {
    // Explicitly select columns to avoid 'name' collision from SELECT *
    const sql = `SELECT id, company, name as contact_person, email, phone, address, industry, status FROM clients ORDER BY company ASC`;
    
    db.all(sql, [], (err, rows) => {
        if (err) {
            return res.status(500).json({ error: err.message });
        }
        // Map rows to ensure 'name' is always the primary display field for the ERP dropdowns
        const mappedRows = rows.map(row => ({
            ...row,
            name: row.company || row.contact_person || 'Unknown Client',
            contact_name: row.contact_person,
            company_name: row.company // Added for legacy Bytez-ERP PHP support
        }));
        res.json({
            status: 'success',
            data: mappedRows
        });
    });
});

/**
 * Get single client
 */
app.get('/api/clients/:id', (req, res) => {
    const sql = `SELECT id, company, name as contact_person, email, phone, address, industry, status FROM clients WHERE id = ?`;
    db.get(sql, [req.params.id], (err, row) => {
        if (err) return res.status(500).json({ status: 'error', message: err.message });
        if (!row) {
            return res.status(404).json({ status: 'error', message: 'Client not found' });
        }
        res.json({ 
            status: 'success', 
            data: { 
                ...row, 
                name: row.company || row.contact_person,
                contact_name: row.contact_person,
                company_name: row.company // Added for legacy Bytez-ERP PHP support
            } 
        });
    });
});

/**
 * Create/Update clients (Bridge support for Laravel Sync)
 */
app.post('/api/clients', (req, res) => {
    const { company_name, contact_name, email, phone, address, industry, status } = req.body;
    const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
    // Assign user_id = 1 (Admin) by default so Laravel "User-Only" logic sees these clients
    const sql = `INSERT INTO clients (company, name, email, phone, address, industry, status, user_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?)`;
    db.run(sql, [company_name || req.body.company || req.body.name, contact_name || req.body.name, email, phone, address, industry, status, now, now], function(err) {
        if (err) return res.status(500).json({ success: false, status: 'error', message: err.message });
        res.json({ success: true, status: 'success', data: { id: this.lastID } });
    });
});

app.put('/api/clients/:id', (req, res) => {
    const { company_name, company, contact_name, email, phone, address, industry, status } = req.body;
    const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
    const sql = `UPDATE clients SET company = ?, name = ?, email = ?, phone = ?, address = ?, industry = ?, status = ?, updated_at = ? WHERE id = ?`;
    db.run(sql, [company || company_name || req.body.name, contact_name || req.body.name, email, phone, address, industry, status, now, req.params.id], function(err) {
        if (err) return res.status(500).json({ success: false, status: 'error', message: err.message });
        res.json({ success: true, status: 'success', message: 'Client updated' });
    });
});

app.delete('/api/clients/:id', (req, res) => {
    const sql = `DELETE FROM clients WHERE id = ?`;
    db.run(sql, [req.params.id], function(err) {
        if (err) {
            return res.status(500).json({ success: false, status: 'error', message: err.message });
        }
        res.json({ success: true, status: 'success', message: 'Client deleted', changes: this.changes });
    });
});

/**
 * Dashboard Statistics (Bridge for Bytez-ERP)
 */
app.get('/api/dashboard', async (req, res) => {
    try {
        console.log('[Bridge API] Dashboard: Fetching statistics and recent projects');
        const [clients, projects, tasks, users, recentProjects, projectChart] = await Promise.all([
            dbGet("SELECT COUNT(*) as count FROM clients"),
            dbGet("SELECT COUNT(*) as count FROM projects"),
            dbGet("SELECT COUNT(*) as count FROM tasks"),
            dbGet("SELECT COUNT(*) as count FROM users"),
            dbAll(`SELECT p.*, p.title as name, p.title as project_name, c.company as client_name, c.company as customer_name 
                   FROM projects p LEFT JOIN clients c ON p.client_id = c.id ORDER BY p.id DESC LIMIT 5`),
            dbAll("SELECT status, COUNT(*) as count FROM projects GROUP BY status")
        ]);

        // Map recent projects with comprehensive aliases for PHP support
        const mappedRecentProjects = recentProjects.map(p => ({ 
            ...p, 
            name: p.title || p.project_name || 'Untitled Project', 
            project_name: p.title || p.project_name || 'Untitled Project',
            customer_name: p.client_name || p.customer_name || 'N/A',
            client_name: p.client_name || p.customer_name || 'N/A'
        }));

        const projectStatusMap = { pending: 0, in_progress: 0, completed: 0 };
        projectChart.forEach(row => {
            if (projectStatusMap.hasOwnProperty(row.status)) {
                projectStatusMap[row.status] = row.count;
            }
        });

        res.json({
            status: 'success',
            data: {
                stats: {
                    total_clients: clients.count,
                    total_projects: projects.count,
                    total_tasks: tasks.count,
                    total_users: users.count
                },
                recent_projects: mappedRecentProjects,
                recent_tasks: [],
                task_chart: { todo: 0, in_progress: 0, completed: 0 },
                project_chart: projectStatusMap
            }
        });
    } catch (err) {
        res.status(500).json({ status: 'error', message: err.message });
    }
});

/**
 * Projects API
 */
app.get('/api/projects', (req, res) => {
    console.log('[Bridge API] GET /api/projects - Listing all projects');
    const sql = `
        SELECT p.*, 
               p.title as name, 
               p.title as project_name, 
               c.company as client_name, 
               c.company as customer_name,
               c.name as contact_person,
               c.email as client_email
        FROM projects p 
        LEFT JOIN clients c ON p.client_id = c.id 
        ORDER BY p.id DESC
    `;
    db.all(sql, [], (err, rows) => {
        if (err) {
            console.error('[Bridge API] Error fetching projects:', err.message);
            return res.status(500).json({ status: 'error', message: err.message });
        }
        
        const mappedRows = rows.map(r => ({ 
            ...r, 
            name: r.title || r.project_name,
            project_name: r.title || r.project_name,
            customer_name: r.client_name || r.customer_name
        }));
        console.log(`[Bridge API] Successfully retrieved ${mappedRows.length} projects`);
        res.json({ status: 'success', data: mappedRows });
    });
});

app.get('/api/projects/:id', (req, res) => {
    const sql = `SELECT p.*, c.company as customer_name FROM projects p LEFT JOIN clients c ON p.client_id = c.id WHERE p.id = ?`;
    db.get(sql, [req.params.id], (err, row) => {
        if (err) return res.status(500).json({ status: 'error', message: err.message });
        if (!row) return res.status(404).json({ status: 'error', message: 'Project not found' });
        res.json({ 
            status: 'success', 
            data: { ...row, name: row.title, project_name: row.title } 
        });
    });
});

app.post('/api/projects', (req, res) => {
    console.log('[Bridge API] POST /api/projects - Data received:', JSON.stringify(req.body));

    // Handle multiple possible key names from legacy Bytez-ERP frontend
    const title = req.body.title || req.body.name || req.body.project_name;
    const client_id = req.body.client_id || req.body.customer_id || req.body.client;
    const description = req.body.description || '';
    const manager_id = req.body.manager_id || null;
    const start_date = req.body.start_date || null;
    const deadline = req.body.deadline || null;
    const budget = req.body.budget || 0;
    const status = req.body.status || 'pending';
    const progress = req.body.progress || 0;

    if (!title) return res.status(400).json({ status: 'error', message: 'Project title is required' });

    const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
    const sql = `INSERT INTO projects (title, description, client_id, manager_id, start_date, deadline, budget, status, progress, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`;
    
    db.run(sql, [title, description, client_id, manager_id, start_date, deadline, budget, status, progress, now, now], function(err) {
        if (err) {
            console.error('[Bridge API] Failed to insert project:', err.message);
            return res.status(500).json({ status: 'error', message: err.message });
        }
        console.log(`[Bridge API] Project created successfully with ID: ${this.lastID}`);
        res.json({ status: 'success', data: { id: this.lastID } });
    });
});

app.put('/api/projects/:id', (req, res) => {
    const { title, description, client_id, manager_id, start_date, deadline, budget, status, progress } = req.body;
    const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
    const sql = `UPDATE projects SET 
                 title = ?, description = ?, client_id = ?, manager_id = ?, 
                 start_date = ?, deadline = ?, budget = ?, status = ?, 
                 progress = ?, updated_at = ? 
                 WHERE id = ?`;
    db.run(sql, [
        title || req.body.name || req.body.project_name,
        description,
        client_id || req.body.customer_id || req.body.client,
        manager_id,
        start_date,
        deadline,
        budget,
        status,
        progress,
        now,
        req.params.id
    ], function(err) {
        if (err) return res.status(500).json({ status: 'error', message: err.message });
        res.json({ status: 'success', message: 'Project updated' });
    });
});

app.delete('/api/projects/:id', (req, res) => {
    const sql = `DELETE FROM projects WHERE id = ?`;
    db.run(sql, [req.params.id], function(err) {
        if (err) return res.status(500).json({ status: 'error', message: err.message });
        res.json({ status: 'success', message: 'Project deleted' });
    });
});

/**
 * Get projects for a specific client (helper for billing)
 */
app.get('/api/clients/:id/projects', (req, res) => {
    const sql = `SELECT * FROM projects WHERE client_id = ? ORDER BY id DESC`;
    db.all(sql, [req.params.id], (err, rows) => {
        if (err) return res.status(500).json({ status: 'error', message: err.message });
        res.json({ status: 'success', data: rows });
    });
});

app.get('/', (req, res) => {
    res.send('Bytez-ERP Bridge API is running...');
});

const server = app.listen(PORT, '127.0.0.1', () => {
    console.log(`Server is running on http://127.0.0.1:${PORT}`);
});

// Handle server errors, specifically port conflicts
server.on('error', (err) => {
    if (err.code === 'EADDRINUSE') {
        console.error(`\x1b[31m[Node Backend ERR]\x1b[0m Port ${PORT} is already in use.`);
        console.error(`To fix this on Windows, run: netstat -ano | findstr :${PORT}`);
        console.error(`Then run: taskkill /F /PID <found_pid>`);
        process.exit(1);
    }
});

// Graceful shutdown: Ensure database connection is closed
process.on('SIGTERM', () => {
    db.close(() => {
        console.log('SQLite database connection closed.');
        process.exit(0);
    });
});