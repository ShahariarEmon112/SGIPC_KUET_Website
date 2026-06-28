# SGIPC - Special Group Interested In Programming Contest (KUET)

Responsive website for the SGIPC (Special Group Interested In Programming Contest) competitive programming club at KUET. The platform includes a MySQL backend for team formation standings, participant registration, and an interactive admin panel.

## What is included

- `index.html` - main frontend landing page with team rankings and student registration
- `index.css` - premium visual styling (dark mode, glassmorphism, responsive grids)
- `config.php` - shared MySQL connection with auto-detection for user-space and standard XAMPP servers
- `submit_registration.php` - processes and saves form submissions to MySQL
- `team-rankings.php` - live database-backed rankings page and JSON endpoint
- `admin.php` - club administrator dashboard to manage teams and view registrations
- `database/sgipc_schema.sql` - database tables and sample seed data

## Database & Server Auto-Detection

The project contains a smart database connection function (`sgipc_db_connection`) in `config.php` that automatically tries connection strategies in sequence:
1. Custom environment variables (if set)
2. User-space local MariaDB daemon (bound to port `3307` and Unix socket `/home/shahariar/sgipc_mysql_data/mysql.sock` as user `shahariar` with no password)
3. Standard XAMPP server (bound to port `3306` as user `root` with no password)

This ensures the website runs seamlessly locally, inside developer environments, and inside XAMPP directories without needing manual configuration changes.

## Database Setup

1. Start Apache and MySQL in XAMPP (or run a custom MySQL server).
2. Import the `database/sgipc_schema.sql` file into your MySQL database server (e.g. via phpMyAdmin or MySQL shell).
3. The schema creates the `sgipc_db` database, setting up the `team_rankings` and `contest_registrations` tables.

## Club Administrator Dashboard

The application features a secure, premium admin dashboard at `admin.php` to let club coordinators manage the portal:
- **Demo Username**: `admin`
- **Demo Password**: `admin123`
- **Features**:
  - View overall registration metrics (total registrations, active teams, average rating).
  - Manage live standings (Add new teams, Edit rating/solved counts/status, Delete teams).
  - Manage applications (View registration details, delete registrations).

## Local Preview

1. Start your local web and database servers.
2. Place the project files inside your local web root directory (e.g. `htdocs` for XAMPP).
3. Open `index.html` (or serve via PHP local development server `php -S 127.0.0.1:8080`).
