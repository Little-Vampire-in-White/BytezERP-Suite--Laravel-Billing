const { app, BrowserWindow, session, dialog } = require('electron');
const { spawn, fork } = require('child_process');
const path = require('path');
const fs = require('fs');

// Load .env reliably in both dev and prod
const envPath = app.isPackaged 
    ? path.join(process.resourcesPath, '.env') 
    : path.resolve(__dirname, '.env');
require('dotenv').config({ path: envPath });

const net = require('net');

let mainWindow;
let phpServer; // For Bytez-ERP (Core PHP)
let laravelPhpServer; // For Laravel Invoice System
let nodeBackend; // For Node.js Bridge API

// Helper to resolve paths for external services correctly in both dev and prod
const getServicePath = (envPath, defaultRelPath) => {
    if (app.isPackaged) {
        const folderName = (envPath || defaultRelPath).replace(/^(\.\.\/|\.\.\\)/, '');
        // First check if it's in extraResources (common for bundled backends)
        const extraPath = path.join(process.resourcesPath, folderName);
        if (fs.existsSync(extraPath)) return extraPath;
        
        // Fallback to app.asar.unpacked
        return path.join(process.resourcesPath, 'app.asar.unpacked', folderName);
    }
    // In development, resolve relative to the desktop folder
    return path.resolve(__dirname, envPath || defaultRelPath);
};

function checkServerReady(port, serviceName, callback, timeout = 20000) {
    const startTime = Date.now();
    const interval = setInterval(() => {
        if (Date.now() - startTime > timeout) {
            clearInterval(interval);
            console.error(`Timeout: ${serviceName} on port ${port} failed to start.`);
            dialog.showErrorBox("Startup Timeout", 
                `The ${serviceName} on port ${port} failed to respond within ${timeout/1000} seconds.\n\n` +
                `Tip: Ensure no other service is using this port and that your antivirus isn't blocking the application.`
            );
            return;
        }

        const socket = new net.Socket();
        socket.connect(port, '127.0.0.1', () => { // Always use 127.0.0.1 for internal connections
            socket.destroy();
            clearInterval(interval);
            callback();
        });
        socket.on('error', () => {
            socket.destroy();
        });
    }, 500);
}

function createWindow() {
    // 1. Start the PHP Built-in Server for Bytez-ERP
    const erpCwd = getServicePath(process.env.BYTEZ_ERP_PATH, '../bytez-erp');
    // Allow specifying full path to php.exe in .env, e.g., PHP_BINARY=C:\xampp\php\php.exe
    const phpBinary = process.env.PHP_BINARY || 'php';
    
    if (!fs.existsSync(erpCwd)) {
        dialog.showErrorBox("Missing Folder", `Bytez-ERP folder not found at: ${erpCwd}`);
    }

    phpServer = spawn(phpBinary, [ 
        '-S', `127.0.0.1:${process.env.BYTEZ_ERP_PORT || 8080}`, 
        '-t', '.',
        'index.php' // Optional: Use index.php as a router if your framework requires it
    ], {
        cwd: erpCwd,
        env: { ...process.env },
        shell: true, // Helps finding the binary on Windows
        stdio: 'pipe' // Use 'pipe' so we can capture output and add prefixes
    });
    phpServer.on('error', (err) => {
        if (app.isPackaged) {
            dialog.showErrorBox("PHP Error", `Failed to start Bytez-ERP PHP server.\n\nCommand: ${phpBinary}\nError: ${err.message}\n\nPlease ensure PHP is installed and in your system PATH, or define PHP_BINARY in your .env file.`);
        }
    });
    phpServer.stdout.on('data', (data) => console.log(`[Bytez-ERP PHP] ${data}`));
    phpServer.stderr.on('data', (data) => console.error(`[Bytez-ERP PHP ERR] ${data}`));

    // Start the PHP Built-in Server for Laravel Invoice System
    laravelPhpServer = spawn(phpBinary, [
        'artisan', 'serve',
        `--port=${process.env.LARAVEL_INVOICE_PORT || 8000}`,
        '--host=127.0.0.1'
    ], {
        cwd: getServicePath(process.env.LARAVEL_INVOICE_PATH, '../laravel-invoice-billing-system'),
        env: { ...process.env },
        shell: true,
        stdio: 'pipe' // Use 'pipe' so we can capture output and add prefixes
    });
    laravelPhpServer.on('error', (err) => {
        console.error('Failed to start Laravel PHP server:', err);
    });
    laravelPhpServer.stdout.on('data', (data) => console.log(`[Laravel PHP] ${data}`));
    laravelPhpServer.stderr.on('data', (data) => console.error(`[Laravel PHP ERR] ${data}`));

    // Start the Node.js Bridge API
    const backendDir = getServicePath(process.env.NODE_BACKEND_PATH, '../backend');
    const backendScript = path.join(backendDir, 'index.js');

    console.log(`[Main] Attempting to start backend from: ${backendScript}`);

    if (!fs.existsSync(backendDir)) {
        dialog.showErrorBox("Missing Backend Folder", `Node.js backend folder not found at: ${backendDir}`);
        return; // Prevent further execution if critical folder is missing
    }
    if (!fs.existsSync(backendScript)) {
        dialog.showErrorBox("Missing Backend Script", `Node.js backend script not found at: ${backendScript}`);
        return; // Prevent further execution if critical script is missing
    }

    // Prepare environment. Fix absolute path leaks from development .env
    const backendEnv = { ...process.env };
    if (app.isPackaged) {
        backendEnv.ELECTRON_RUN_AS_NODE = '1';
        // If DB_PATH is an absolute path from your dev machine, it won't work in prod.
        if (backendEnv.NODE_BACKEND_DB_PATH && path.isAbsolute(backendEnv.NODE_BACKEND_DB_PATH)) {
            backendEnv.NODE_BACKEND_DB_PATH = './database.sqlite';
        }

        // Tell the backend where to find the bundled modules
        const appPath = app.getAppPath();
        const asarModules = path.join(appPath, 'node_modules');
        const unpackedModules = path.join(appPath + '.unpacked', 'node_modules');
        backendEnv.NODE_PATH = asarModules + path.delimiter + unpackedModules;
    }

    nodeBackend = fork(backendScript, [], {
        execPath: process.execPath, // Explicitly use Electron's binary for the fork
        cwd: backendDir,
        env: {
            ...backendEnv,
            PORT: backendEnv.NODE_BACKEND_PORT || 5000,
            DB_PATH: backendEnv.NODE_BACKEND_DB_PATH || './database.sqlite'
        },
        stdio: 'pipe' // Simplifies child process management in some Electron versions
    });

    nodeBackend.stdout.on('data', (data) => console.log(`[Node Backend] ${data}`));
    nodeBackend.stderr.on('data', (data) => {
        const errString = data.toString();
        console.error(`[Node Backend ERR] ${errString}`);
        // Show actual crash errors in a dialog so you can debug production
        if (app.isPackaged && (errString.includes('Error:') || errString.includes('Exception'))) {
            dialog.showErrorBox("Backend Startup Error", errString);
        }
    });

    nodeBackend.on('error', (err) => {
        if (app.isPackaged) {
            dialog.showErrorBox("Backend Error", `Failed to start Node.js backend: ${err.message}`);
        }
    });

    // 3. Create the browser window
    mainWindow = new BrowserWindow({
        width: 1280,
        height: 900,
        title: "Bytez ERP System",
        autoHideMenuBar: true,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true
        }
    });

    // Open DevTools automatically in development only
    if (!app.isPackaged) {
        mainWindow.webContents.openDevTools();
    }

    // Wait for the primary ERP server to be ready before loading the URL
    const erpPort = process.env.BYTEZ_ERP_PORT || 8080;
    const backendPort = process.env.NODE_BACKEND_PORT || 5000;

    checkServerReady(erpPort, "Bytez-ERP PHP Server", async () => {
        checkServerReady(backendPort, "Node.js Bridge API", async () => {
            // Clear storage data to prevent redirect loops caused by stale sessions or cookies
            await session.defaultSession.clearStorageData();
            const startUrl = `http://127.0.0.1:${erpPort}/Codebytez/auth/login`;
            mainWindow.loadURL(startUrl).catch(err => {
                console.error("Failed to load URL:", err);
            });
        });
    });

    mainWindow.on('closed', () => {
        mainWindow = null;
    });
}

app.on('ready', createWindow);

// Kill the background servers when the app closes
app.on('will-quit', () => {
    console.log('Quitting application. Killing child processes...');
    
    const processes = [
        { proc: phpServer, name: 'Bytez-ERP PHP' },
        { proc: laravelPhpServer, name: 'Laravel PHP' },
        { proc: nodeBackend, name: 'Node.js Backend' }
    ];

    processes.forEach(({ proc, name }) => {
        if (proc) {
            proc.kill('SIGTERM');
            console.log(`${name} server killed.`);
        }
    });
});

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});