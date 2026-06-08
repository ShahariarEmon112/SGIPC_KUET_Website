# SGIPC Complete Setup & Usage Guide

## ✅ System Status

Your SGIPC website is now **fully configured and running locally!**

### Current Setup:
- ✅ **PHP Server:** Running on `http://127.0.0.1:8000`
- ✅ **MySQL Database:** `sgipc_db` created with all tables
- ✅ **Admin User:** Created with default credentials
- ✅ **All Files:** Made executable
- ✅ **Registration:** Both admin and user registration enabled

---

## 🌐 Access Your Website

### **Main Entry Points:**

1. **Landing/Dashboard Page (Start Here!):**
   - URL: `http://127.0.0.1:8000/landing.php`
   - Shows admin portal and member registration options

2. **Main Website:**
   - URL: `http://127.0.0.1:8000/index.html`
   - View contests, rankings, and public information

3. **Admin Portal:**
   - URL: `http://127.0.0.1:8000/admin_login.php`
   - Default: `admin` / `Admin@123`

4. **Member Portal:**
   - URL: `http://127.0.0.1:8000/member_portal.php`
   - For registered members

---

## 🔐 Available Credentials

### **Default Admin Login:**
```
Username: admin
Password: Admin@123
```

### **Create New Admin:**
- Go to: `http://127.0.0.1:8000/admin_register.php`
- Fill in all required fields
- Minimum password: 8 characters

### **Register as Member:**
- Go to: `http://127.0.0.1:8000/user_register.php`
- Fill in profile, interests, and experience level
- Admin will review and approve

---

## 📁 Project Structure

```
/home/shahariar/Desktop/sgipc website/
│
├── landing.php                    ← START HERE (Main dashboard)
├── index.html                     ← Public website
├── index.php                      ← Alternative homepage
│
├── ADMIN PANEL FILES:
│   ├── admin_login.php            ← Admin login
│   ├── admin_register.php         ← Register new admin (NEW!)
│   ├── admin_dashboard.php        ← Admin dashboard
│   ├── admin_members.php          ← Manage members
│   ├── admin_requests.php         ← Member approval
│   ├── admin_contests.php         ← Manage contests
│   ├── admin_rankings.php         ← Manage rankings
│   ├── admin_achievements.php     ← Award badges
│   ├── admin_submissions.php      ← View submissions
│   └── admin_logout.php           ← Logout
│
├── USER PORTAL FILES:
│   ├── user_register.php          ← Member registration (NEW!)
│   ├── member_portal.php          ← Member dashboard
│   ├── contests.php               ← View contests
│   ├── rankings.php               ← View rankings
│   └── request_join.php           ← Join request
│
├── DATABASE & CONFIG:
│   ├── config.php                 ← Database connection
│   ├── setup.php                  ← Database setup
│   ├── database/                  ← SQL schema files
│   │   ├── sgipc_schema.sql
│   │   ├── member_requests.sql
│   │   └── contests.sql
│   └── mysql_data/                ← Local MySQL data
│
├── DOCUMENTATION:
│   ├── SETUP_GUIDE.md
│   ├── DOCUMENTATION.md
│   ├── HTML_CSS_GUIDE.md
│   ├── PRACTICAL_EXAMPLES.md
│   ├── TESTING_GUIDE.md
│   ├── START_HERE.md
│   └── COMPLETE_SETUP.md          ← THIS FILE
│
└── SCRIPTS:
    └── run_local.sh               ← Local server startup
```

---

## 🚀 Quick Start Commands

### **Start the Server (Already Running):**
```bash
cd ~/Desktop/sgipc\ website
php -S 127.0.0.1:8000
```

### **Stop the Server:**
```bash
# Press Ctrl+C in the terminal where server is running
```

### **View MySQL Database:**
```bash
sudo mysql sgipc_db -e "SHOW TABLES;"
```

### **View Admin Users:**
```bash
sudo mysql sgipc_db -e "SELECT username, email FROM admin_users;"
```

### **Make Files Executable:**
```bash
cd ~/Desktop/sgipc\ website
chmod +x *.php *.sh
```

---

## 📋 What You Can Do Now

### **As an Admin:**
1. ✅ Login to admin panel: `admin_login.php`
2. ✅ View dashboard with statistics
3. ✅ Manage members and their profiles
4. ✅ Review and approve member join requests
5. ✅ Create and manage contests
6. ✅ Manage team rankings
7. ✅ Award achievements/badges
8. ✅ View member submissions

### **As a Member:**
1. ✅ Register on the platform: `user_register.php`
2. ✅ View public contests: `contests.php`
3. ✅ Check team rankings: `rankings.php`
4. ✅ Access member portal: `member_portal.php`
5. ✅ Submit requests to join

### **As Visitor:**
1. ✅ View main website: `index.html`
2. ✅ See public contests and rankings
3. ✅ Learn about SGIPC
4. ✅ Request to join

---

## 🔄 Complete User Flow

### **Flow 1: Admin Registration & Login**
```
1. Visit: http://127.0.0.1:8000/landing.php
2. Click: "Register as Admin"
3. Fill form with credentials
4. Submit registration
5. Go to: http://127.0.0.1:8000/admin_login.php
6. Login with your credentials
7. Access admin dashboard
```

### **Flow 2: Member Registration & Portal Access**
```
1. Visit: http://127.0.0.1:8000/landing.php
2. Click: "Register as Member"
3. Fill registration form with interests
4. Submit registration
5. Admin reviews and approves
6. Member can access: member_portal.php
```

### **Flow 3: Contest Participation**
```
1. Admin creates contest from: admin_contests.php
2. Contest appears on: contests.php (public)
3. Members can view: contests.php
4. Rankings updated on: rankings.php
5. Admin manages rankings: admin_rankings.php
```

---

## 💾 Database Tables

The system includes these tables:

```
sgipc_db
├── admin_users              ← Admin accounts
├── members                  ← Registered members
├── member_requests          ← Join requests
├── contests                 ← Contest details
├── team_rankings            ← Team standings
├── achievements             ← Badges/achievements
├── submissions              ← Code submissions
├── member_achievements      ← Member badges
├── admin_logs               ← Activity logs
├── contest_registrations    ← Registration data
└── team_rankings (sample)   ← Sample data
```

---

## 🛠️ Troubleshooting

### **Problem: "Database connection error"**
**Solution:**
```bash
# Check MySQL is running
sudo service mysql status

# If not running:
sudo service mysql start

# Check user permissions
sudo mysql -e "SELECT user, host FROM mysql.user WHERE user='shahariar';"
```

### **Problem: "Page not found"**
**Solution:**
```bash
# Check PHP server is running
ps aux | grep "php -S"

# If not, start it:
cd ~/Desktop/sgipc\ website
php -S 127.0.0.1:8000
```

### **Problem: "403 Forbidden"**
**Solution:**
```bash
# Make files executable
chmod +x ~/Desktop/sgipc\ website/*.php
```

### **Problem: "Admin login fails"**
**Solution:**
```bash
# Check admin user exists
sudo mysql sgipc_db -e "SELECT * FROM admin_users;"

# If empty, recreate:
sudo mysql sgipc_db << 'EOF'
INSERT INTO admin_users (username, password, email, full_name) VALUES
('admin', '$2y$10$ZTBlMWI0OTZmZTMzNDY2ZeVxRWdsbyX6wV4O9V.0O6g3VH5lOzQ4W', 'admin@sgipc.com', 'Admin User');
EOF
```

---

## 🔐 Security Notes

1. **Change Default Password:** After login, update admin password
2. **Database Backup:** Regularly backup your database
3. **File Permissions:** Keep files executable for security
4. **HTTPS:** Use HTTPS in production (not needed for local)
5. **Password Hashing:** All passwords use bcrypt (secure)

---

## 📊 Test Data

Sample test data is included:

### **Sample Teams (Already in Database):**
- KUET_Team1 - Rank 1, Rating: 2450
- KUET_Team2 - Rank 2, Rating: 2385
- KUET_Team3 - Rank 3, Rating: 2310
- KUET_Team4 - Rank 4, Rating: 2240
- KUET_Team5 - Rank 5, Rating: 2185
- KUET_Team6 - Rank 6, Rating: 2100

You can add more data through the admin panel.

---

## ✨ Features Summary

| Feature | Available | Location |
|---------|-----------|----------|
| Admin Registration | ✅ | admin_register.php |
| Admin Login | ✅ | admin_login.php |
| Member Registration | ✅ | user_register.php |
| Contest Management | ✅ | admin_contests.php |
| Ranking Management | ✅ | admin_rankings.php |
| Member Management | ✅ | admin_members.php |
| Achievement System | ✅ | admin_achievements.php |
| Public Contests | ✅ | contests.php |
| Public Rankings | ✅ | rankings.php |
| Member Portal | ✅ | member_portal.php |
| Admin Dashboard | ✅ | admin_dashboard.php |
| Landing Page | ✅ | landing.php |

---

## 📚 Next Steps

1. **Test Admin Registration:**
   - Go to: `admin_register.php`
   - Create new admin account
   - Login and explore admin panel

2. **Test Member Registration:**
   - Go to: `user_register.php`
   - Register as new member
   - Login as admin and approve request

3. **Create Test Data:**
   - Login as admin
   - Create new contest
   - Add team rankings
   - Award achievements

4. **Explore All Features:**
   - Visit all admin pages
   - Check member portal
   - View public pages

5. **Customize for Your Needs:**
   - Modify contest details
   - Add more teams
   - Update member information
   - Create achievement badges

---

## 🎓 Learning Resources

Available documentation files:

1. **SETUP_GUIDE.md** - Installation and setup
2. **DOCUMENTATION.md** - CRUD operations explained
3. **HTML_CSS_GUIDE.md** - Frontend styling
4. **PRACTICAL_EXAMPLES.md** - Code examples
5. **TESTING_GUIDE.md** - Testing procedures
6. **START_HERE.md** - Getting started
7. **COMPLETE_SETUP.md** - This comprehensive guide

---

## 🆘 Support

If you encounter issues:

1. Check the troubleshooting section above
2. Review SETUP_GUIDE.md
3. Check TESTING_GUIDE.md for detailed testing steps
4. Verify database connection with config.php
5. Check MySQL user permissions

---

## 📞 Quick Reference

| Task | Command |
|------|---------|
| Start Server | `php -S 127.0.0.1:8000` |
| Stop Server | `Ctrl+C` |
| Check DB | `sudo mysql sgipc_db -e "SHOW TABLES;"` |
| Start MySQL | `sudo service mysql start` |
| Stop MySQL | `sudo service mysql stop` |
| Admin Login | `http://127.0.0.1:8000/admin_login.php` |
| Landing Page | `http://127.0.0.1:8000/landing.php` |
| Website | `http://127.0.0.1:8000/index.html` |

---

## ✅ System Checklist

- ✅ PHP installed and running
- ✅ MySQL installed and running
- ✅ Database created (sgipc_db)
- ✅ All tables created
- ✅ Admin user created
- ✅ Admin registration enabled
- ✅ Member registration enabled
- ✅ All files executable
- ✅ Server running on port 8000
- ✅ Database user (shahariar) has permissions

---

## 🎉 You're All Set!

Your SGIPC website is now **fully operational** with:
- ✅ Complete admin system
- ✅ Member registration
- ✅ Contest management
- ✅ Ranking system
- ✅ Achievement tracking
- ✅ Public website
- ✅ Responsive design
- ✅ Full backend integration

**Start here:** `http://127.0.0.1:8000/landing.php`

Enjoy managing your competitive programming club! 🚀
