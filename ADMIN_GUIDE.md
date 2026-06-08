# SGIPC Website - Admin Panel & Member Management System

A comprehensive PHP-based competitive programming club website with full admin panel, member management, contest management, rankings, and achievements system.

## Features

### Admin Panel

- **Admin Login System**: Secure authentication with role-based access
- **Dashboard**: Overview with real-time statistics
- **Member Management**: CRUD operations for approved members
- **Member Request System**: Review and approve/reject join requests
- **Contest Management**: Create, update, delete contests with full details
- **Rankings Management**: Manage team rankings and statistics
- **Achievements**: Award badges and achievements to members
- **Submissions Tracking**: View all user submissions and results

### Public Features

- **Dynamic Contests Page**: Browse all contests with filters (upcoming, ongoing, completed)
- **Premium Rankings Display**: Visual podium for top 3 teams + full leaderboard
- **Member Join Requests**: Dynamic request system with status tracking
- **Responsive Design**: Mobile-friendly interface throughout

## Database Setup

### 1. Create Database from Enhanced Schema

```bash
mysql -u root -p < database/sgipc_schema_enhanced.sql
```

The enhanced schema includes:

- `admin_users` - Admin accounts
- `members` - Approved members with profiles
- `member_requests` - Pending join requests
- `contests` - Contest details
- `contest_problems` - Problems per contest
- `team_rankings` - Team rankings and statistics
- `submissions` - Code submissions and results
- `achievements` - Badges and achievements
- `contest_registrations` - External contest registrations

### 2. Default Admin Account

```
Username: admin
Password: Admin@123
Email: admin@sgipc.com
```

**IMPORTANT**: Change the default password after first login!

## File Structure

```
/
├── admin_login.php           # Admin authentication
├── admin_dashboard.php       # Admin dashboard with stats
├── admin_members.php         # Member management (CRUD)
├── admin_requests.php        # Join request approval system
├── admin_contests.php        # Contest management (CRUD)
├── admin_rankings.php        # Rankings management
├── admin_achievements.php    # Achievements/badges system
├── admin_submissions.php     # Submissions viewer
├── admin_logout.php          # Logout handler
├── contests.php              # Public contests page
├── rankings.php              # Public rankings display
├── request_join.php          # Join request handler
├── config.php                # Database configuration
├── index.php                 # Homepage
├── index.css                 # Styling
├── database/
│   ├── sgipc_schema_enhanced.sql  # Enhanced database schema
│   └── ...
└── README.md                 # This file
```

## How to Use

### For Admins

#### 1. Login to Admin Panel

Navigate to: `http://localhost/sgipc/admin_login.php`

#### 2. Manage Join Requests

- Go to "Join Requests" in sidebar
- View pending requests from members
- Approve: Create member account with temporary password
- Reject: Decline request

#### 3. Create a Contest

- Click "Contests" → "Create Contest"
- Fill in contest details:
  - Name, description, type (online/offline/virtual)
  - Start and end time
  - Difficulty level
  - Platform (Codeforces, AtCoder, etc.)
  - Registration link
  - Prize pool
  - Status (upcoming, ongoing, completed, cancelled)

#### 4. Manage Members

- View all approved members
- Edit member details (department, batch, etc.)
- Change member status
- Delete members if needed

#### 5. Update Rankings

- Add new team rankings
- Edit team statistics (rating, solved problems, points, wins)
- Delete rankings
- Set status (Confirmed or Standby)

#### 6. Award Achievements

- Select member from dropdown
- Assign badge (e.g., "First AC", "Top Solver")
- Add description
- View all awarded achievements

#### 7. Monitor Submissions

- View all member submissions
- Track submission status
- See points awarded
- Filter by time

### For Members

#### 1. Join the Club

- Visit main website
- Fill out join request form
- Wait for admin approval
- Receive temporary password

#### 2. View Contests

- Go to "Contests" page
- Filter by status (upcoming, ongoing, completed)
- See contest details and register
- Different difficulty levels for guidance

#### 3. Check Rankings

- Visit "Team Rankings" page
- See podium with top 3 teams
- View full leaderboard with all stats
- Filter by status (confirmed/standby)

#### 4. Track Achievements

- View awarded badges in member dashboard
- See personal achievements and stats

## API Endpoints

### Authentication

- `POST /admin_login.php` - Admin login
- `GET /admin_logout.php` - Admin logout

### Requests

- `POST /request_join.php` - Submit join request
- `POST /admin_requests.php?action=approve&id={id}` - Approve request
- `POST /admin_requests.php?action=reject&id={id}` - Reject request

### Contests

- `GET /admin_contests.php` - List all contests
- `POST /admin_contests.php?action=create` - Create contest
- `POST /admin_contests.php?action=edit&id={id}` - Edit contest
- `POST /admin_contests.php?action=delete&id={id}` - Delete contest

### Members

- `GET /admin_members.php` - List all members
- `POST /admin_members.php?action=edit&id={id}` - Edit member

### Rankings

- `GET /admin_rankings.php` - List rankings
- `POST /admin_rankings.php?action=create` - Create ranking
- `POST /admin_rankings.php?action=edit&id={id}` - Edit ranking
- `POST /admin_rankings.php?action=delete&id={id}` - Delete ranking

### Achievements

- `POST /admin_achievements.php` - Award achievement

## Database Configuration

Edit `config.php` to configure database connection:

```php
$host = getenv('SGIPC_DB_HOST') ?: '127.0.0.1';
$user = getenv('SGIPC_DB_USER') ?: 'shahariar';
$password = getenv('SGIPC_DB_PASSWORD') ?: '';
$database = getenv('SGIPC_DB_NAME') ?: 'sgipc_db';
$port = (int)(getenv('SGIPC_DB_PORT') ?: '3307');
```

Or set environment variables:

```bash
export SGIPC_DB_HOST=localhost
export SGIPC_DB_USER=root
export SGIPC_DB_PASSWORD=yourpassword
export SGIPC_DB_NAME=sgipc_db
export SGIPC_DB_PORT=3306
```

## Security Notes

1. **Change Default Password**: Log in and change admin password immediately
2. **Use HTTPS**: Deploy with SSL/TLS in production
3. **Input Validation**: All inputs are validated and sanitized
4. **SQL Injection Protection**: Uses prepared statements throughout
5. **CSRF Protection**: Implement CSRF tokens for sensitive operations
6. **Session Management**: Sessions are properly managed with timeouts
7. **Password Hashing**: Uses bcrypt for password hashing

## Customization

### Add New Contest Types

Edit `admin_contests.php` and update the contest_type field:

```php
<option value="new_type">New Type</option>
```

### Add New Difficulty Levels

Update in both admin and public pages:

```php
<option value="new_level">New Level</option>
```

### Add New Achievement Types

Create badges and assign to members via Achievements page

### Customize Styling

Edit colors in CSS within individual `.php` files or create `custom.css`

## Troubleshooting

### Database Connection Failed

- Check database credentials in `config.php`
- Ensure MySQL server is running
- Check database name and permissions

### Admin Login Not Working

- Verify admin user exists in database:
  ```sql
  SELECT * FROM admin_users WHERE username = 'admin';
  ```
- Reset password if needed:
  ```sql
  UPDATE admin_users SET password_hash = '$2y$10$...' WHERE username = 'admin';
  ```

### Members Not Appearing

- Check member status in database (should be 'approved')
- Verify member_requests were approved

### Rankings Not Showing

- Ensure team_rankings table has data
- Check team names for special characters
- Verify ranking numbers are sequential

## Performance Optimization

- Database queries are optimized with indexes
- Use caching for frequently accessed data
- Consider pagination for large datasets
- Optimize image sizes for profile pictures

## Future Enhancements

- [ ] Member profile customization
- [ ] Direct messaging between members
- [ ] Online judge integration
- [ ] Real-time submission tracking
- [ ] Advanced analytics and statistics
- [ ] Email notifications for approvals
- [ ] API for mobile app
- [ ] Discussion forum
- [ ] Resource sharing platform
- [ ] Mentor-mentee matching

## Support & Maintenance

For issues or questions:

1. Check database integrity
2. Review error logs
3. Verify all required files are present
4. Test with sample data

## License

This project is part of SGIPC (Special Group Interested In Programming Contest) at KUET.

## Version

Version 2.0 - Enhanced with Admin Panel and Full DBMS Integration

Last Updated: June 2026
