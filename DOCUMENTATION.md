# SGIPC Website - Complete CRUD Documentation for Beginners

## Table of Contents

1. [What is CRUD?](#what-is-crud)
2. [How CRUD Works in SGIPC](#how-crud-works-in-sgipc)
3. [Step-by-Step CRUD Examples](#step-by-step-crud-examples)
4. [Frontend & Backend Communication](#frontend--backend-communication)
5. [Local Setup & Testing](#local-setup--testing)
6. [Troubleshooting](#troubleshooting)

---

## What is CRUD?

CRUD stands for **Create, Read, Update, Delete** - the four basic operations for managing data in a database.

### The Four Operations:

| Operation  | What it does                     | Example                |
| ---------- | -------------------------------- | ---------------------- |
| **Create** | Add new data to database         | Create a new contest   |
| **Read**   | Retrieve/view data from database | View all contests list |
| **Update** | Modify existing data             | Edit contest details   |
| **Delete** | Remove data from database        | Delete a contest       |

### Real-Life Analogy:

Think of your notebook:

- **Create**: Write a new note
- **Read**: Read a note you wrote
- **Update**: Edit a note to fix mistakes
- **Delete**: Tear out a page you don't need

---

## How CRUD Works in SGIPC

### The Flow Diagram:

```
USER (Browser)
    ↓ (sends data via form)
FRONTEND (HTML Form)
    ↓ (submits to)
PHP FILE (admin_*.php)
    ↓ (processes with)
DATABASE FUNCTIONS
    ↓ (sends SQL queries to)
MYSQL DATABASE
    ↓ (returns results)
PHP FILE (processes response)
    ↓ (displays)
BROWSER (shows result to user)
```

### Example Flow for Creating a Contest:

```
1. Admin opens contests.php?action=create
2. Form appears with fields (name, start time, etc.)
3. Admin fills form and clicks "Save Contest"
4. Form data sent to admin_contests.php via POST
5. PHP code validates the data
6. Prepared statement created: INSERT INTO contests (...)
7. Query executes in MySQL database
8. Database confirms success
9. PHP redirects user back to contests list
10. Success message displayed
11. New contest appears in the list
```

---

## Step-by-Step CRUD Examples

### Example 1: CREATE OPERATION - Adding a Contest

#### Step 1: User Opens Form

```
URL: http://localhost/sgipc/admin_contests.php?action=create
```

#### Step 2: HTML Form (Frontend)

```html
<!-- admin_contests.php displays this form -->
<form method="POST" action="?action=create">
  <input type="text" name="contest_name" required />
  <input type="datetime-local" name="start_time" required />
  <button type="submit">Save Contest</button>
</form>
```

#### Step 3: PHP Processing (Backend)

```php
// When form is submitted (POST request):
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Get data from form
    $name = trim($_POST['contest_name'] ?? '');
    $start_time = $_POST['start_time'] ?? '';

    // Step 2: Validate data
    if (empty($name) || empty($start_time)) {
        $error = 'Contest name and start time required.';
    } else {
        // Step 3: Connect to database
        $connection = sgipc_db_connection();

        // Step 4: Prepare SQL statement (prevents hackers)
        $stmt = $connection->prepare(
            'INSERT INTO contests (contest_name, start_time, created_by)
             VALUES (?, ?, ?)'
        );

        // Step 5: Bind values safely
        $stmt->bind_param('ssi', $name, $start_time, $_SESSION['admin_id']);

        // Step 6: Execute query
        if ($stmt->execute()) {
            $message = 'Contest created successfully!';
            // Redirect back to list
            header('Location: admin_contests.php');
        } else {
            $error = 'Error creating contest.';
        }

        $stmt->close();
        $connection->close();
    }
}
```

#### Step 4: Database (MySQL)

```sql
-- SQL query that gets executed:
INSERT INTO contests (contest_name, start_time, created_by)
VALUES ('SGIPC Practice Round', '2026-06-15 10:00:00', 1);

-- New row added to table:
| id | contest_name         | start_time          | created_by |
|----|----------------------|---------------------|-----------|
| 1  | SGIPC Practice Round | 2026-06-15 10:00:00 | 1         |
```

#### Step 5: User Sees Result

- Form closes
- Redirects to contests list
- Success message appears: "Contest created successfully!"
- New contest visible in the table

---

### Example 2: READ OPERATION - Viewing Contests

#### Step 1: User Opens Page

```
URL: http://localhost/sgipc/admin_contests.php
```

#### Step 2: PHP Queries Database

```php
// In admin_contests.php:
$result = $connection->query(
    'SELECT id, contest_name, start_time, status FROM contests
     ORDER BY start_time DESC'
);

// Get results
$contests = [];
while ($row = $result->fetch_assoc()) {
    $contests[] = $row;  // Add each contest to array
}
```

#### Step 3: Database Returns Data

```sql
-- Query result:
| id | contest_name         | start_time          | status   |
|----|----------------------|---------------------|----------|
| 1  | SGIPC Practice Round | 2026-06-15 10:00:00 | upcoming |
| 2  | Qualification Round  | 2026-06-22 14:00:00 | upcoming |
```

#### Step 4: PHP Displays Data in HTML Table

```php
// In admin_contests.php:
<?php foreach ($contests as $c): ?>
    <tr>
        <td><?php echo $c['contest_name']; ?></td>
        <td><?php echo $c['start_time']; ?></td>
        <td><?php echo $c['status']; ?></td>
    </tr>
<?php endforeach; ?>
```

#### Step 5: Browser Shows Table

```
✓ Contest list displayed with all contests
✓ User can see contest details
✓ User can click Edit or Delete buttons
```

---

### Example 3: UPDATE OPERATION - Editing a Contest

#### Step 1: User Clicks Edit Button

```html
<a href="?action=edit&id=1">Edit</a>
```

URL becomes: `admin_contests.php?action=edit&id=1`

#### Step 2: PHP Loads Contest Data

```php
// Fetch the contest to edit
$contest_id = 1;
$stmt = $connection->prepare(
    'SELECT * FROM contests WHERE id = ?'
);
$stmt->bind_param('i', $contest_id);
$stmt->execute();
$contest = $stmt->get_result()->fetch_assoc();
```

#### Step 3: Form Pre-filled with Old Data

```html
<form method="POST" action="?action=edit&id=1">
  <input name="contest_name" value="SGIPC Practice Round" />
  <input name="start_time" value="2026-06-15T10:00:00" />
  <button>Save Changes</button>
</form>
```

#### Step 4: User Makes Changes and Submits

- Changes contest name: "SGIPC Practice Round v2"
- Clicks "Save Changes"
- Form submitted via POST

#### Step 5: PHP Updates Database

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit') {
    $new_name = $_POST['contest_name'];
    $contest_id = 1;

    // Prepare UPDATE statement
    $stmt = $connection->prepare(
        'UPDATE contests SET contest_name = ? WHERE id = ?'
    );
    $stmt->bind_param('si', $new_name, $contest_id);
    $stmt->execute();
}
```

#### Step 6: Database Updates

```sql
-- Before:
| id | contest_name         |
|----|----------------------|
| 1  | SGIPC Practice Round |

-- After UPDATE:
| id | contest_name         |
|----|----------------------|
| 1  | SGIPC Practice Round v2 |
```

#### Step 7: User Sees Updated Data

- Redirects to contests list
- Contest name now shows "SGIPC Practice Round v2"

---

### Example 4: DELETE OPERATION - Removing a Contest

#### Step 1: User Clicks Delete Button

```html
<a href="?action=delete&id=1" onclick="return confirm('Are you sure?');"
  >Delete</a
>
```

#### Step 2: User Confirms Deletion

- Browser shows: "Are you sure?"
- User clicks "OK"

#### Step 3: PHP Deletes from Database

```php
if ($action === 'delete' && $contest_id > 0) {
    $stmt = $connection->prepare('DELETE FROM contests WHERE id = ?');
    $stmt->bind_param('i', $contest_id);
    $stmt->execute();
}
```

#### Step 4: Database Deletes Row

```sql
-- Before DELETE:
| id | contest_name         |
|----|----------------------|
| 1  | SGIPC Practice Round |

-- After DELETE:
(empty - row is gone)
```

#### Step 5: User Sees Result

- Contest disappears from list
- Success message: "Contest deleted successfully!"

---

## Frontend & Backend Communication

### How Form Data Gets to Backend:

#### Method 1: POST (Secure - for Create/Update/Delete)

```html
<!-- In admin_contests.php -->
<form method="POST" action="?action=create">
  <input type="text" name="contest_name" value="My Contest" />
  <button type="submit">Save</button>
</form>
```

When submitted:

- Data sent in request body (hidden from URL)
- Server receives at: `$_POST['contest_name']`
- Used for sensitive operations

#### Method 2: GET (Visible - for Read/Link Operations)

```html
<!-- In admin_contests.php -->
<a href="?action=edit&id=1">Edit Contest 1</a>
<a href="?action=delete&id=1">Delete Contest 1</a>
```

When clicked:

- Data shown in URL: `admin_contests.php?action=edit&id=1`
- Server receives at: `$_GET['action']` and `$_GET['id']`
- Used for filtering and navigation

### The Complete Request-Response Cycle:

```
┌─────────────────────────────────────────────────────────┐
│ STEP 1: USER INTERACTION                                 │
│ User fills form and clicks submit                        │
└────────────────────┬────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 2: FRONTEND SENDS REQUEST                          │
│ Browser sends data to admin_contests.php via POST       │
│ Data: {contest_name: "My Contest", start_time: "..."}   │
└────────────────────┬────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 3: BACKEND RECEIVES REQUEST                        │
│ PHP reads $_POST variables                              │
│ Validates data (check empty, format, etc.)              │
└────────────────────┬────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 4: DATABASE QUERY                                   │
│ PHP creates prepared statement                          │
│ Executes INSERT/UPDATE/DELETE/SELECT                    │
└────────────────────┬────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 5: DATABASE PROCESSES                              │
│ MySQL performs operation                                │
│ Returns success/error status                            │
└────────────────────┬────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 6: BACKEND RESPONDS                                │
│ PHP receives database response                          │
│ Generates HTML with new/updated data                    │
└────────────────────┬────────────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 7: FRONTEND DISPLAYS                               │
│ Browser renders HTML                                    │
│ User sees updated page with new data                    │
└─────────────────────────────────────────────────────────┘
```

---

## Local Setup & Testing

### Prerequisites

- **XAMPP** (includes Apache + MySQL + PHP)
- **Web Browser** (Chrome, Firefox, Safari)
- **Text Editor** (VS Code recommended)

### Step 1: Install XAMPP

1. Download from: https://www.apachefriends.org/
2. Run installer
3. Start XAMPP Control Panel
4. Start **Apache** and **MySQL** (should turn green)

### Step 2: Place Project Files

```bash
# Copy project to htdocs (XAMPP's web root)
cp -r "sgipc website" "C:\xampp\htdocs\"
# OR on macOS/Linux:
cp -r "sgipc website" ~/applications/xampp/htdocs/
```

### Step 3: Create Database

#### Method 1: Using phpMyAdmin (Easy)

```
1. Open: http://localhost/phpmyadmin
2. Click "SQL" tab
3. Paste content from: database/sgipc_schema_enhanced.sql
4. Click "Go"
```

#### Method 2: Using Terminal

```bash
# On Windows (in Command Prompt):
cd C:\xampp\mysql\bin
mysql -u root < "C:\path\to\sgipc_schema_enhanced.sql"

# On macOS/Linux:
mysql -u root < ~/path/to/sgipc_schema_enhanced.sql
```

### Step 4: Update Database Credentials (if needed)

Edit `config.php`:

```php
$host = '127.0.0.1';      // Usually localhost
$user = 'root';           // XAMPP default
$password = '';           // XAMPP default (empty)
$database = 'sgipc_db';   // From schema file
$port = 3306;             // XAMPP default
```

### Step 5: Start Testing

#### Test 1: Homepage

```
URL: http://localhost/sgipc/index.php
Expected: You see the SGIPC homepage
```

#### Test 2: Admin Login

```
URL: http://localhost/sgipc/admin_login.php
Username: admin
Password: Admin@123
Expected: Redirects to admin_dashboard.php with stats
```

#### Test 3: Create Contest (CREATE Operation)

```
1. Go to: http://localhost/admin_dashboard.php
2. Click "Contests" in sidebar
3. Click "+ Create Contest"
4. Fill form:
   - Name: "Test Contest"
   - Start: Pick a date/time
   - End: Pick a later date/time
5. Click "Save Contest"

Expected Results:
✓ Form closes
✓ Redirects to contests list
✓ Success message appears
✓ New contest visible in table
✓ Database has new row in contests table
```

#### Test 4: View Contests (READ Operation)

```
1. Go to: http://localhost/admin_dashboard.php
2. Click "Contests" in sidebar

Expected Results:
✓ All contests displayed in table
✓ Shows contest name, start date, difficulty
✓ Can see Edit and Delete buttons
```

#### Test 5: Edit Contest (UPDATE Operation)

```
1. Go to: http://localhost/admin_dashboard.php
2. Click "Contests"
3. Click "Edit" next to a contest
4. Change contest name to: "Updated Test Contest"
5. Click "Save Ranking"

Expected Results:
✓ Form pre-filled with old data
✓ You can change values
✓ Redirects to list after save
✓ New name appears in the table
```

#### Test 6: Delete Contest (DELETE Operation)

```
1. Go to: http://localhost/admin_dashboard.php
2. Click "Contests"
3. Click "Delete" next to a contest
4. Confirm deletion in popup

Expected Results:
✓ Confirmation popup appears
✓ Contest removed from table after confirmation
✓ Success message shown
```

#### Test 7: Join Requests (Frontend - Backend Communication)

```
1. Go to: http://localhost/sgipc/index.php
2. Scroll to "Join Us" section
3. Fill request form:
   - Full Name: "John Doe"
   - Email: "john@example.com"
   - Select interests
   - Write message
4. Click "Submit Request"

Expected Results:
✓ Form validates (checks empty fields)
✓ Request stored in database
✓ Success message displayed
✓ Admin can see request in admin_requests.php
```

#### Test 8: Approve Member Request (Admin)

```
1. Go to: http://localhost/admin_dashboard.php
2. Click "Join Requests"
3. See pending request
4. Click "Approve"

Expected Results:
✓ New member created in database
✓ Request status changes to "Approved"
✓ Temporary password generated
✓ Member can now login
```

---

## Troubleshooting

### Issue: "Database connection failed"

**Cause**: MySQL not running or wrong credentials

**Solution**:

```
1. Open XAMPP Control Panel
2. Make sure MySQL shows "Running" (green)
3. Check config.php credentials match your setup
4. Test in phpMyAdmin (http://localhost/phpmyadmin)
```

### Issue: "Admin login doesn't work"

**Cause**: Wrong password or admin account not in database

**Solution**:

```php
// Check if admin exists in phpMyAdmin:
// Go to: http://localhost/phpmyadmin
// Select sgipc_db → admin_users table
// Should see: username=admin, email=admin@sgipc.com

// If missing, insert via phpMyAdmin:
INSERT INTO admin_users VALUES
(NULL, 'admin', '$2y$10$6xLk5NnIWc6p5VbNe4b8Ou5QNzJU6VWzCTqKtJlJZ3FdKn2kZJi4G',
'admin@sgipc.com', 'superadmin', 1, NOW(), NULL);
```

### Issue: "Contests don't appear after creation"

**Cause**: Data created but not displayed

**Solution**:

```
1. Check phpMyAdmin → sgipc_db → contests table
2. Verify row exists with your data
3. Check status field (should be 'upcoming' or similar)
4. Clear browser cache (Ctrl+Shift+Delete)
5. Refresh page
```

### Issue: "Forms don't submit/stay blank"

**Cause**: JavaScript or form validation issue

**Solution**:

```
1. Open browser Console (F12)
2. Check for JavaScript errors
3. Fill all required fields (marked with *)
4. Check date format (should be YYYY-MM-DD HH:MM)
5. Try different browser
```

### Issue: "404 Not Found" errors

**Cause**: Wrong file path or file not in htdocs

**Solution**:

```
1. Verify sgipc folder is in: C:\xampp\htdocs\
2. Check URL: http://localhost/sgipc/admin_login.php
3. Make sure all .php files exist in folder
4. File names are case-sensitive on Linux/Mac
```

---

## File Structure Reference

```
sgipc website/
├── index.php                          (Homepage - READ operation)
├── request_join.php                   (Join requests - CREATE operation)
├── contests.php                       (Public contests - READ operation)
├── rankings.php                       (Public rankings - READ operation)
├── member_portal.php                  (Member dashboard - READ operation)
│
├── admin_login.php                    (Authentication)
├── admin_dashboard.php                (Stats - READ operation)
├── admin_members.php                  (Members CRUD - all 4 operations)
├── admin_requests.php                 (Requests - CREATE/READ/UPDATE)
├── admin_contests.php                 (Contests - all 4 operations)
├── admin_rankings.php                 (Rankings - all 4 operations)
├── admin_achievements.php             (Achievements - CREATE/READ)
├── admin_submissions.php              (Submissions - READ operation)
├── admin_logout.php                   (Logout)
│
├── config.php                         (Database connection functions)
├── index.css                          (Styling)
│
├── database/
│   ├── sgipc_schema_enhanced.sql     (Complete database schema)
│   └── sgipc_schema.sql              (Original schema)
│
├── DOCUMENTATION.md                   (This file)
├── ADMIN_GUIDE.md                     (Full admin guide)
├── SETUP_GUIDE.md                     (Setup instructions)
└── README.md                          (Original readme)
```

---

## Quick CRUD Cheat Sheet

### Files with CRUD Operations:

| File                   | CREATE | READ | UPDATE | DELETE |
| ---------------------- | ------ | ---- | ------ | ------ |
| admin_members.php      | ✗      | ✓    | ✓      | ✓      |
| admin_requests.php     | ✓      | ✓    | ✓      | ✗      |
| admin_contests.php     | ✓      | ✓    | ✓      | ✓      |
| admin_rankings.php     | ✓      | ✓    | ✓      | ✓      |
| admin_achievements.php | ✓      | ✓    | ✗      | ✗      |
| admin_submissions.php  | ✗      | ✓    | ✗      | ✗      |
| request_join.php       | ✓      | ✗    | ✗      | ✗      |
| member_portal.php      | ✗      | ✓    | ✗      | ✗      |

### How to Identify CRUD Operations in Code:

**CREATE** (INSERT):

```php
$connection->prepare('INSERT INTO table_name (...) VALUES (?, ?, ?)');
```

**READ** (SELECT):

```php
$connection->query('SELECT * FROM table_name');
$result->fetch_assoc();
```

**UPDATE** (UPDATE):

```php
$connection->prepare('UPDATE table_name SET column=? WHERE id=?');
```

**DELETE** (DELETE):

```php
$connection->prepare('DELETE FROM table_name WHERE id=?');
```

---

## Common Questions

### Q: How does data get from form to database?

**A**: Form → POST Request → PHP ($\_POST) → Database Query → MySQL stores data → PHP confirms → Browser shows result

### Q: What's the difference between GET and POST?

**A**:

- GET: Data in URL (visible, less secure) - used for links and filters
- POST: Data in body (hidden, more secure) - used for forms creating/modifying data

### Q: Why use prepared statements?

**A**: They prevent SQL injection attacks where hackers try to manipulate queries. Example:

```php
// UNSAFE - don't do this:
$query = "SELECT * FROM users WHERE name = '" . $_POST['name'] . "'";

// SAFE - prepared statement:
$stmt = $connection->prepare("SELECT * FROM users WHERE name = ?");
$stmt->bind_param("s", $_POST['name']);
```

### Q: Can I test without XAMPP?

**A**: You can use other tools:

- **WAMP** (Windows)
- **MAMP** (macOS)
- **Docker** (Advanced)
- **Local PHP server**: `php -S localhost:8000`

### Q: How do I test frontend and backend together?

**A**: Everything works together automatically:

1. Open page in browser (Frontend)
2. Fill form and submit
3. Browser sends to PHP file (Backend)
4. PHP talks to MySQL (Database)
5. Backend sends HTML back
6. Browser displays result (Frontend)

It all happens in seconds!

---

## Next Steps

1. ✅ Read this documentation
2. ✅ Install XAMPP
3. ✅ Copy project to htdocs
4. ✅ Create database using schema file
5. ✅ Test each CRUD operation using the tests above
6. ✅ Explore the code in each admin file
7. ✅ Try creating real data (contests, members, etc.)
8. ✅ Study how the SQL queries work

**Congratulations!** You now understand how the SGIPC website works!

---

## Additional Resources

- **MySQL Tutorial**: https://www.w3schools.com/sql/
- **PHP Tutorial**: https://www.w3schools.com/php/
- **HTML Forms**: https://www.w3schools.com/html/html_forms.asp
- **XAMPP Documentation**: https://www.apachefriends.org/
- **Database Basics**: https://www.w3schools.com/sql/sql_intro.asp
