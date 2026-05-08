# SGIPC - Special Group Interested In Programming Contest

Responsive SGIPC website for KUET with a MySQL/XAMPP backend for team formation rankings and registration handling.

## What is included

- `sgipc.html` - main frontend with the team rankings section and registration form
- `sgipc.css` - existing premium visual styling
- `config.php` - shared MySQL connection and helper functions
- `submit_registration.php` - saves form submissions to MySQL
- `team-rankings.php` - live database-backed ranking page
- `database/sgipc_schema.sql` - database, tables, and seed data

## Database setup

1. Start Apache and MySQL in XAMPP.
2. Import `database/sgipc_schema.sql` into phpMyAdmin.
3. The schema creates the `sgipc_db` database.
4. The seeded team names are `KUET_Team1` through `KUET_Team6`.

## Backend behavior

- The registration form posts to `submit_registration.php`.
- Passwords are stored as hashes, not plain text.
- Team rankings are read from the `team_rankings` table.
- If the database is unavailable, the rankings page falls back to seeded sample data.

## Local preview

1. Place the project inside XAMPP `htdocs`.
2. Open `sgipc.html` for the main page.
3. Open `team-rankings.php` to view the live ranking page.
4. Use the Resources section for MySQL, XAMPP, Workbench, and SQL practice links.

## Core sections on the page

- Home and achievements
- About SGIPC
- Highlights and useful links
- Contest performance
- Practice roadmap
- Join the SGIPC community
- Team formation update
- Team formation rankings
- Learning roadmap
- Resources
- Guidelines

## Notes

- The UI is built with gradients, cards, and responsive grids.
- The rankings section uses KUET-style team names and overall ratings.
