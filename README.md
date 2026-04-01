Task Management System (Laravel 11)
A robust Task Management API built with Laravel 11, featuring full CRUD capabilities, priority-based sorting, and category management. This project is optimized for high-performance data handling and is deployed on Railway with a MySQL database.

Live Demo
Link: https://web-production-9eac3.up.railway.app

(Note: Use the Railway Console to run migrations if the database appears empty during testing.)

Features
Full CRUD: Create, Read, Update, and Delete tasks.

Database: MySQL integration with Eloquent ORM.

Validation: Strict request validation for data integrity.

Optimization: Downgraded Symfony and PHPUnit components for broad cloud compatibility (PHP 8.2+).

Category Support: Organize tasks into distinct categories for better management.

Local Setup Instructions
Prerequisites
PHP 8.2 or 8.3

Composer

MySQL / MariaDB

Node.js & NPM

Installation
Clone the repository:

Bash
git clone https://github.com/mercynjambi/cytonn-task-manager.git
cd cytonn-task-manager
Install dependencies:

Bash
composer install
npm install && npm run build
Environment Setup:

Bash
cp .env.example .env
php artisan key:generate
Configure Database:
Update your .env file with your local MySQL credentials:

Code snippet
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_db
DB_USERNAME=root
DB_PASSWORD=
Run Migrations and Seeders:

Bash
php artisan migrate --seed
Start Server:

Bash
php artisan serve
Deployment Instructions (Railway)
This project is configured for seamless deployment on Railway using the Railpack builder.

Database Provisioning:

Create a MySQL instance on Railway.

Connect the MySQL service to the Web service using Railway's shared environment variables.

Environment Variables:
Required variables in the Railway dashboard:

APP_KEY: (Generated via CLI)

DB_CONNECTION: mysql

RAILWAY_START_COMMAND: php artisan serve --host 0.0.0.0 --port $PORT

Database Migration:
Access the Railway SSH/Console and execute:

Bash
php artisan migrate --force
Example API Requests
1. Get All Tasks
GET /api/tasks

Response: 200 OK (JSON list of tasks)

2. Create a Task
POST /api/tasks

JSON
{
    "title": "Complete Cytonn Assessment",
    "description": "Submit the repo link before the 2pm deadline",
    "priority": "high",
    "category_id": 1
}
3. Delete a Task
DELETE /api/tasks/{id}

Response: 204 No Content

Author
Mercy Njambi