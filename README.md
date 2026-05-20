# BytezERP Suite

A comprehensive ERP and Billing solution consisting of a Desktop application, a Node.js Bridge API, and dual PHP backends (Core PHP and Laravel).

## 📥 Download & Install

**Looking for the installer?**  
Download the latest Windows executable from the [Releases](https://github.com/Little-Vampire-in-White/BytezERP-Suite--Laravel-Billing/releases) section. Just download, install, and run!

## 🏗️ Project Architecture

This suite is composed of four main components:
1.  **Desktop (`/desktop`):** Electron-based shell that orchestrates and displays all services.
2.  **Bridge API (`/backend`):** Node.js/Express/SQLite service handling shared data and authentication.
3.  **ERP Core (`/bytez-erp`):** The primary PHP-based ERP system.
4.  **Billing System (`/laravel-invoice-billing-system`):** A Laravel-based invoice management system.

## 🛠️ Prerequisites

Before running the suite, ensure you have the following installed:
- **Node.js** (v18 or higher)
- **PHP** (v8.1 or higher)
- **Composer** (for Laravel dependencies)
- **SQLite3**
- **Git**

## 🚀 Installation & Setup

### 1. Clone the Repository
```bash
git clone https://github.com/Little-Vampire-in-White/BytezERP-Suite--Laravel-Billing.git
cd BytezERP-Suite--Laravel-Billing
```

### 2. Environment Configuration
Create a `.env` file in the `desktop/` directory. You can use the following template:

```env
# PHP Configuration
PHP_BINARY=php # Or path to your php.exe (e.g., C:\xampp\php\php.exe)

# Laravel APP_KEY (Generate with 'php artisan key:generate' in Laravel folder)
APP_KEY=base64:YOUR_APP_KEY_HERE

# Ports
BYTEZ_ERP_PORT=8080
LARAVEL_INVOICE_PORT=8000
NODE_BACKEND_PORT=5000

# Database
# NODE_BACKEND_DB_PATH is now managed automatically by Electron to a persistent location.
```

### 3. Install Dependencies

**Backend (Node.js API):**
```bash
cd backend
npm install
```


**Billing System (Laravel):**
```bash
cd ../laravel-invoice-billing-system
composer install
php artisan migrate
php artisan storage:link
```

## 🏃 How to Run

You only need to start the Desktop application. It is configured to automatically launch the PHP servers and the Node.js backend.

```bash
cd desktop
npm start
```

## 📦 Building for Production (EXE)

To package the entire suite into a single Windows executable:

```bash
cd desktop
npm run dist
```
The installer will be generated in the `desktop/dist` folder.

## 📝 Logging
In production, the application captures all service logs (PHP, Node, Electron) into a file for debugging:
`%AppData%\bytez-desktop\app-logs.txt`

## 👨‍💻 Author
**Neko aka Kokofish** - *Initial Work*
