# FlowTrack

FlowTrack is a project management and time tracking web application built with pure PHP 8+ and OOP principles — no frameworks used.

## Features

- **Projects** — create, edit, delete projects with color labels, hourly rates, and deadlines
- **Tasks** — full CRUD, user assignment, priority (low / medium / high / critical), status, deadline
- **Kanban board** — drag & drop tasks between columns, custom board columns per project
- **Comments** — add and delete comments on any task
- **Time tracking** — log work entries with billable / non-billable flag
- **Billing** — overview of billed hours and revenue per project by month / week / year
- **Team** — manage workspace members, invite new members
- **Activity log** — full history of all actions within the workspace
- **Authentication** — register, login, logout, remember-me cookie (30 days)

## Tech stack

| Layer        | Technology                              |
|--------------|-----------------------------------------|
| Backend      | PHP 8.3, pure OOP, MVC architecture     |
| Database     | MySQL 8.0 / MariaDB 10.5, PDO           |
| Autoloading  | PSR-4 (custom implementation)           |
| Frontend     | HTML5, CSS3, vanilla JavaScript         |
| Web server   | Apache 2 with mod_rewrite               |

## Requirements

- PHP 8.0 or higher
- MySQL 8.0+ or MariaDB 10.5+
- Apache with `mod_rewrite` enabled

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/<your-username>/FlowTrack.git
cd FlowTrack
```

### 2. Create the database

In MySQL / phpMyAdmin create a new database:

```sql
CREATE DATABASE flowtrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then import the schema:

```bash
mysql -u root -p flowtrack < database/schema.sql
```

### 3. Configure the database connection

Edit `config/database.php`:

```php
return [
    'host'     => '127.0.0.1',
    'port'     => 3306,          // MAMP uses 8889
    'dbname'   => 'flowtrack',
    'charset'  => 'utf8mb4',
    'username' => 'root',
    'password' => 'root',
];
```

### 4. Configure the web server (Apache / MAMP)

If you are using **MAMP**, place the project in `htdocs/FlowTrack` and open:

```
http://localhost:8888/FlowTrack/
```

For an **Apache virtual host** on a custom domain, add to `httpd-vhosts.conf`:

```apacheconf
<VirtualHost *:80>
    ServerName flowtrack.test
    DocumentRoot "/path/to/FlowTrack"
    DirectoryIndex index.php

    <Directory "/path/to/FlowTrack">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 5. Create an account

Open the application in your browser and click **Register** — a workspace is created automatically on registration.

## Project structure

```
FlowTrack/
├── app/
│   ├── controllers/        # Controllers (AuthController, ProjectController, …)
│   ├── core/               # Framework core (Router, Database, View, Controller)
│   ├── models/             # Models (ProjectModel, TaskModel, …)
│   └── functions.php       # Helper functions (auth_guard, redirect, config, …)
├── config/
│   ├── app.php             # Application config
│   └── database.php        # Database credentials
├── public/
│   ├── assets/             # CSS, JS, images
│   └── index.php           # Front controller
├── templates/              # PHP view templates
│   └── partials/           # Shared partials (header, footer, sidebar, …)
├── .htaccess               # Rewrite all requests to index.php
├── composer.json           # PSR-4 autoload definition
├── index.php               # Entry point (delegates to public/index.php)
└── routes.php              # All route definitions
```

## Architecture

The project uses a custom MVC mini-framework with **no external dependencies**:

- **Router** — maps GET/POST requests to controller methods using regex patterns
- **Database** — singleton PDO connection with `ERRMODE_EXCEPTION`
- **View** — renders PHP templates using `extract()` to inject data
- **Controller** — abstract base class for all controllers
- **PSR-4 autoloader** — registered via `spl_autoload_register`, maps `App\` → `app/`

## Security

- Passwords hashed with `password_hash()` using `PASSWORD_BCRYPT`
- All SQL queries use PDO prepared statements (SQL injection protection)
- All output escaped with `htmlspecialchars()` (XSS protection)
- Session-based authentication + optional remember-me token (SHA-256 hash stored in DB)
- Directories `app/`, `config/`, `templates/` protected via `.htaccess` (403 Forbidden)

## Author

Jozef Kušnierik — school project, Scripting Languages course
