# Bytez ERP System
A Mini Digital Agency ERP System built with Core PHP, MySQL, Bootstrap & jQuery.

## 🔧 Tech Stack
- **Backend:** Core PHP (MVC Architecture)
- **Frontend:** HTML5, CSS3, Bootstrap 5, jQuery
- **Database:** MySQL
- **AI:** Groq API (LLaMA 3)
- **Charts:** Chart.js

## ✅ Modules
- Authentication (Role-based: Admin, Manager, Employee)
- Client Management (CRUD)
- Project Management (CRUD + Team Members)
- Task Management (Kanban + Comments + Attachments)
- AI Content Generator & Project Insights
- Dashboard with Charts & Analytics
- Users Management (Admin only)
- REST API with Token Authentication

## ⚙️ Setup Instructions

### Requirements
- XAMPP (Apache + PHP 8.x)
- MySQL 8.x
- MySQL Workbench (optional)

### Installation Steps

**1. Clone the repository**
git clone https://github.com/yourusername/bytez-erp.git

**2. Move to htdocs**
Copy the folder to: C:\xampp\htdocs\Codebytez

**3. Create Database**
Open MySQL Workbench and run:
CREATE DATABASE bytez_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

**4. Import Database**
Run the SQL file: database/bytez_erp.sql

**5. Configure Database**
Open config/database.php and update:
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

**6. Start Apache**
Open XAMPP Control Panel → Start Apache

**7. Open in Browser**
http://localhost/Codebytez

## 🔑 Default Login Credentials
| Role     | Email                  | Password |
|----------|------------------------|----------|
| Admin    | admin@bytez.com        | password |
| Manager  | manager@bytez.com      | password |
| Employee | employee@bytez.com     | password |

## 🌐 API Endpoints
| Method | Endpoint          | Description        | Auth |
|--------|-------------------|--------------------|------|
| POST   | /api/auth         | Login & get token  | No   |
| GET    | /api/clients      | Get all clients    | Yes  |
| POST   | /api/clients      | Create client      | Yes  |
| PUT    | /api/clients/{id} | Update client      | Yes  |
| DELETE | /api/clients/{id} | Delete client      | Yes  |
| GET    | /api/projects     | Get all projects   | Yes  |
| POST   | /api/projects     | Create project     | Yes  |
| GET    | /api/tasks        | Get all tasks      | Yes  |
| POST   | /api/tasks        | Create task        | Yes  |

## 📁 Project Structure
Codebytez/
├── app/
│   ├── controllers/
│   ├── models/
│   ├── helpers/
│   └── services/
├── api/
│   ├── controllers/
│   ├── middleware/
│   └── helpers/
├── config/
├── public/
├── views/
│   ├── layouts/
│   ├── auth/
│   ├── dashboard/
│   ├── clients/
│   ├── projects/
│   ├── tasks/
│   ├── users/
│   └── ai/
└── routes/