# SGIPC Website - Quick Setup Guide

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache or Nginx web server
- Composer (optional, for future expansions)

## Installation Steps

### Step 1: Database Setup

```bash
# Navigate to the database directory
cd database/

# Create the database using the enhanced schema
mysql -u root -p < sgipc_schema_enhanced.sql
```

### Step 2: Configure Database Connection

Edit `config.php` and update credentials if needed:

```php
$host = '127.0.0.1';      # Your MySQL host
$user = 'shahariar';      # Your MySQL user
$password = '';           # Your MySQL password
$database = 'sgipc_db';   # Your database name
```

### Step 3: Verify File Permissions

Ensure the website directory is readable by the web server:

```bash
chmod 755 /path/to/sgipc
chmod 644 /path/to/sgipc/*.php
```

### Step 4: Access the Website

**Homepage:**

```
http://localhost/sgipc/index.php
```

**Admin Login:**

```
http://localhost/sgipc/admin_login.php
Default: admin / Admin@123
```

**Member Login:**

```
http://localhost/sgipc/member_portal.php
```

**Contests:**

```
http://localhost/sgipc/contests.php
```

**Rankings:**

```
http://localhost/sgipc/rankings.php
```

## First Time Setup Checklist

- [ ] Create database from enhanced schema
- [ ] Verify database credentials in config.php
- [ ] Login to admin panel with default account
- [ ] Change admin password immediately
- [ ] Create first contest
- [ ] Add team rankings
- [ ] Test member join request
- [ ] Review and approve join requests
- [ ] Award test achievements

## File Descriptions

### Core Files

- **config.php** - Database configuration and helper functions
- **index.php** - Main homepage with about, contests, rankings, and join form

### Admin Panel

- **admin_login.php** - Admin authentication
- **admin_dashboard.php** - Main dashboard with statistics
- **admin_members.php** - Member CRUD operations
- **admin_requests.php** - Join request management
- **admin_contests.php** - Contest management
- **admin_rankings.php** - Rankings management
- **admin_achievements.php** - Achievements/badges system
- **admin_submissions.php** - Submissions viewer
- **admin_logout.php** - Logout handler

### Public Pages

- **contests.php** - Display all contests with filters
- **rankings.php** - Display team rankings with podium
- **member_portal.php** - Member login and dashboard
- **request_join.php** - Process join requests
- **team-rankings.php** - Legacy rankings page (can be removed)

### Database

- **database/sgipc_schema_enhanced.sql** - Complete database schema
- **database/sgipc_schema.sql** - Original schema (kept for reference)

### Documentation

- **ADMIN_GUIDE.md** - Comprehensive admin guide
- **SETUP_GUIDE.md** - This file
- **README.md** - Original project readme

## Key Features Overview

### For Administrators

✅ Secure admin panel with role-based access
✅ Member request approval/rejection system
✅ Full CRUD for contests, members, rankings
✅ Achievement/badge awarding system
✅ Real-time dashboard with statistics
✅ Submission tracking and monitoring

### For Members

✅ Simple join request process
✅ Member portal with profile and stats
✅ View all available contests
✅ Team rankings with detailed statistics
✅ Achievement tracking
✅ Submission history

### For Public

✅ View available contests with details
✅ See team rankings and statistics
✅ Join club via request form
✅ Responsive mobile-friendly design

## Database Schema Overview

```sql
-- Core Tables
admin_users          # Admin accounts with roles
members              # Approved members
member_requests      # Pending join requests
contest_registrations # Legacy contest registrations

-- Contest Management
contests             # Contest details
contest_problems     # Problems in contests
submissions          # Member code submissions

-- Rankings & Achievements
team_rankings        # Team statistics and rankings
achievements         # Badges and achievements awarded
```

## User Roles & Permissions

### Admin (Default)

- ✅ Create/Edit/Delete contests
- ✅ Manage members and their profiles
- ✅ Review and approve join requests
- ✅ Manage rankings
- ✅ Award achievements
- ✅ View all submissions

### Member (After Approval)

- ✅ View profile and statistics
- ✅ See achievements
- ✅ View contests
- ✅ View rankings
- ❌ Cannot access admin panel

### Visitor

- ✅ View contests
- ✅ View rankings
- ✅ Submit join request
- ❌ Cannot access member features

## Troubleshooting

### Database Connection Error

```
Error: Database connection failed
Solution:
1. Check MySQL is running
2. Verify credentials in config.php
3. Ensure database 'sgipc_db' exists
```

### Admin Login Fails

```
Error: Invalid password
Solution:
1. Verify admin user exists: SELECT * FROM admin_users;
2. Password is bcrypt hashed
3. Default: admin / Admin@123
```

### Member Cannot Join

```
Error: Request submitted error
Solution:
1. Check member_requests table exists
2. Verify email doesn't already exist
3. Check database permissions
```

### Rankings Not Showing

```
Solution:
1. Ensure data in team_rankings table
2. Check team_name isn't NULL or empty
3. Verify overall_rank is sequential
```

## Performance Tips

1. **Database Indexing**: Indexes are already created on:
   - member.status
   - contest.status
   - team_rankings.overall_rank
   - submissions.member_id

2. **Caching**: Consider caching:
   - Contests (rarely change)
   - Rankings (update periodically)
   - Achievements (read-heavy)

3. **Pagination**: For large datasets, implement pagination in:
   - admin_submissions.php
   - member submissions list
   - achievements list

## Security Best Practices

1. **Change Default Password**: Update admin password after first login
2. **Use HTTPS**: Deploy with SSL/TLS in production
3. **Environment Variables**: Use for sensitive data
4. **Input Validation**: All forms validate and sanitize input
5. **SQL Injection Protection**: Prepared statements used throughout
6. **Session Management**: Sessions expire automatically

## Backup & Maintenance

### Database Backup

```bash
# Backup database
mysqldump -u root -p sgipc_db > sgipc_backup.sql

# Restore database
mysql -u root -p sgipc_db < sgipc_backup.sql
```

### Regular Maintenance

- Monitor admin_dashboard.php for statistics
- Review member requests weekly
- Update contest statuses regularly
- Clean up old submissions periodically

## Support & Resources

For issues, check:

1. **ADMIN_GUIDE.md** - Comprehensive documentation
2. **Database logs** - MySQL error logs
3. **PHP error logs** - Server error logs
4. **Browser console** - JavaScript errors

## Version Information

- Version: 2.0
- Release Date: June 2026
- PHP: 7.4+
- MySQL: 5.7+
- Last Updated: June 2026

## Next Steps

1. ✅ Set up database
2. ✅ Configure credentials
3. ✅ Create first contest
4. ✅ Add team rankings
5. ✅ Test member join request
6. ✅ Deploy to production

Enjoy managing your SGIPC community!
