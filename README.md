# Task Management System (Laravel 11)

## Description
A robust Task Management API built with Laravel 11, featuring full CRUD capabilities, priority-based sorting, and category management. This project is optimized for high-performance data handling and is deployed on Railway with a MySQL database.

---

## Features
- Create tasks with priority levels
- View all tasks with category filters
- Update task status and details
- Delete tasks
- Category management for task organization
- Full Eloquent ORM and MySQL integration

---

## Technologies Used
- Laravel 11
- PHP 8.2+
- MySQL
- Composer
- Tailwind CSS
- Railway (Deployment)

---

## Installation

### 1. Clone the Repository
```bash
git clone https://github.com/mercynjambi/cytonn-task-manager.git
cd cytonn-task-manager
```

### 2. Install Dependencies (Composer & NPM)
```bash
composer install
npm install && npm run build
```

---

## Environment Setup

### 1. Create Environment File
```bash
cp .env.example .env
php artisan key:generate
```

### 2. Configure Database
Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## Database Setup

### 1. Create Database
```sql
CREATE DATABASE task_db;
```

---

## Database Migration

### Run Migrations and Seeders
```bash
php artisan migrate --seed
```

### Example Table Schema
```sql
CREATE TABLE tasks (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('pending', 'completed') DEFAULT 'pending',
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## Running the Application

### Start Local Server
```bash
php artisan serve
```

Open in browser:
```
http://127.0.0.1:8000
```

---

## Deployment Instructions (Railway)

### 1. Environment Variables
Set the following in your Railway dashboard:

- `APP_KEY` = your generated app key  
- `DB_CONNECTION` = mysql  
- `RAILWAY_START_COMMAND` = php artisan serve --host 0.0.0.0 --port $PORT  

---

### 2. Run Migrations Online
```bash
php artisan migrate --force
```

---

## API Endpoints

### Get All Tasks
```bash
GET /api/tasks
```

### Create Task
```bash
POST /api/tasks
Content-Type: application/json

{
  "title": "Complete Cytonn Assessment",
  "description": "Submit before 2pm deadline",
  "priority": "high",
  "category_id": 1
}
```

### Update Task
```bash
PUT /api/tasks/{id}
Content-Type: application/json

{
  "status": "completed"
}
```

### Delete Task
```bash
DELETE /api/tasks/{id}
```

---

## Project Structure
```
cytonn-task-manager/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── .env
├── composer.json
└── README.md
```

---

## Troubleshooting

### Check PHP and Composer
```bash
php -v
composer --version
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

---

## Author
Mercy Njambi