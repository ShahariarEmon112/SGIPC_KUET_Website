<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config.php';

$login_message = '';
$error_message = '';
$member = null;
$achievements = [];
$submissions = [];
$is_logged_in = false;

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error_message = 'Email and password are required.';
    } else {
        try {
            $connection = sgipc_db_connection();
            $stmt = $connection->prepare('SELECT id, full_name, email, password_hash, status, joining_date FROM members WHERE email = ? LIMIT 1');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if ($row['status'] !== 'approved') {
                    $error_message = 'Your account is not approved yet.';
                } elseif (password_verify($password, $row['password_hash'])) {
                    $_SESSION['member_id'] = $row['id'];
                    $_SESSION['member_email'] = $row['email'];
                    $_SESSION['member_name'] = $row['full_name'];
                    $is_logged_in = true;
                    $member = $row;
                } else {
                    $error_message = 'Invalid password.';
                }
            } else {
                $error_message = 'Member not found.';
            }

            $stmt->close();
            $connection->close();
        } catch (Throwable $e) {
            $error_message = 'Login error. Please try again.';
        }
    }
}

// Check if already logged in
if (isset($_SESSION['member_id']) && !$is_logged_in) {
    try {
        $connection = sgipc_db_connection();
        
        $stmt = $connection->prepare('SELECT id, full_name, email, status, joining_date, department, batch, student_id FROM members WHERE id = ?');
        $stmt->bind_param('i', $_SESSION['member_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $member = $result->fetch_assoc();
        $stmt->close();

        if ($member && $member['status'] === 'approved') {
            $is_logged_in = true;

            // Get achievements
            $ach_stmt = $connection->prepare('SELECT badge_name, description, achievement_date FROM achievements WHERE member_id = ? ORDER BY achievement_date DESC');
            $ach_stmt->bind_param('i', $_SESSION['member_id']);
            $ach_stmt->execute();
            $ach_result = $ach_stmt->get_result();
            while ($row = $ach_result->fetch_assoc()) {
                $achievements[] = $row;
            }
            $ach_stmt->close();

            // Get submissions
            $sub_stmt = $connection->prepare('SELECT s.status, s.points, s.submitted_at, c.contest_name, p.problem_name FROM submissions s JOIN contests c ON s.contest_id = c.id JOIN contest_problems p ON s.problem_id = p.id WHERE s.member_id = ? ORDER BY s.submitted_at DESC LIMIT 10');
            $sub_stmt->bind_param('i', $_SESSION['member_id']);
            $sub_stmt->execute();
            $sub_result = $sub_stmt->get_result();
            while ($row = $sub_result->fetch_assoc()) {
                $submissions[] = $row;
            }
            $sub_stmt->close();
        } else {
            unset($_SESSION['member_id']);
            $is_logged_in = false;
        }

        $connection->close();
    } catch (Throwable $e) {
        $is_logged_in = false;
    }
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: member_portal.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_logged_in ? 'Member Dashboard' : 'Member Login'; ?> - SGIPC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./index.css">
    <style>
        .member-section {
            min-height: 80vh;
            padding: 40px 20px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }

        .member-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .login-form {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 400px;
            margin: 0 auto;
        }

        .login-title {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .dashboard {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .member-info h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .member-info p {
            font-size: 14px;
            opacity: 0.9;
        }

        .btn-logout {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid white;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .dashboard-content {
            padding: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #f5f7fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: #999;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .profile-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .profile-item {
            padding: 10px;
        }

        .profile-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-value {
            font-size: 15px;
            color: #333;
            font-weight: 600;
        }

        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .achievement-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .badge-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .badge-name {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .badge-date {
            font-size: 12px;
            opacity: 0.8;
        }

        .submissions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .submissions-table thead {
            background: #f5f7fa;
        }

        .submissions-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #667eea;
            font-size: 12px;
        }

        .submissions-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-accepted {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-wrong_answer {
            background-color: #f8d7da;
            color: #721c24;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .submissions-table {
                font-size: 12px;
            }

            .submissions-table th,
            .submissions-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <strong>SGIPC</strong> | Special Group Interested In Programming Contest
    </div>
    
    <header class="navbar">
        <div class="container nav-wrap">
            <a class="brand" href="index.php"><span>SGIPC</span> - Member Portal</a>
            <button class="menu-btn" id="menuBtn" aria-label="Open menu">☰</button>
            <ul class="menu" id="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="contests.php">Contests</a></li>
                <li><a href="rankings.php">Rankings</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="member_portal.php" class="active">Dashboard</a></li>
                    <li><a href="?action=logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="member_portal.php" class="active">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <main class="member-section">
        <div class="member-container">
            <?php if (!$is_logged_in): ?>
                <div class="login-form">
                    <h2 class="login-title">Member Login</h2>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-error"><?php echo sgipc_h($error_message); ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <button type="submit" name="login" class="btn-login">Login</button>
                    </form>

                    <div style="margin-top: 20px; text-align: center; font-size: 14px; color: #666;">
                        <p>New to SGIPC?</p>
                        <a href="index.php#form-demo" style="color: #667eea; text-decoration: underline;">Submit a join request</a>
                    </div>
                </div>

            <?php else: ?>
                <div class="dashboard">
                    <div class="dashboard-header">
                        <div class="member-info">
                            <h1>Welcome, <?php echo sgipc_h($member['full_name']); ?></h1>
                            <p>Member since <?php echo date('M d, Y', strtotime($member['joining_date'])); ?></p>
                        </div>
                        <a href="?action=logout" class="btn-logout">Logout</a>
                    </div>

                    <div class="dashboard-content">
                        <!-- Stats -->
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-value"><?php echo count($achievements); ?></div>
                                <div class="stat-label">Achievements</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo count($submissions); ?></div>
                                <div class="stat-label">Submissions</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?php echo count(array_filter($submissions, fn($s) => $s['status'] === 'accepted')); ?></div>
                                <div class="stat-label">Accepted</div>
                            </div>
                        </div>

                        <!-- Profile Section -->
                        <div class="section">
                            <h3 class="section-title">Profile Information</h3>
                            <div class="profile-section">
                                <div class="profile-item">
                                    <div class="profile-label">Full Name</div>
                                    <div class="profile-value"><?php echo sgipc_h($member['full_name']); ?></div>
                                </div>
                                <div class="profile-item">
                                    <div class="profile-label">Email</div>
                                    <div class="profile-value"><?php echo sgipc_h($member['email']); ?></div>
                                </div>
                                <div class="profile-item">
                                    <div class="profile-label">Student ID</div>
                                    <div class="profile-value"><?php echo sgipc_h($member['student_id'] ?? '-'); ?></div>
                                </div>
                                <div class="profile-item">
                                    <div class="profile-label">Department</div>
                                    <div class="profile-value"><?php echo sgipc_h($member['department'] ?? '-'); ?></div>
                                </div>
                                <div class="profile-item">
                                    <div class="profile-label">Batch</div>
                                    <div class="profile-value"><?php echo $member['batch'] ?? '-'; ?></div>
                                </div>
                                <div class="profile-item">
                                    <div class="profile-label">Member Since</div>
                                    <div class="profile-value"><?php echo date('M d, Y', strtotime($member['joining_date'])); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Achievements Section -->
                        <div class="section">
                            <h3 class="section-title">Achievements & Badges</h3>
                            <?php if (!empty($achievements)): ?>
                                <div class="achievements-grid">
                                    <?php foreach ($achievements as $achievement): ?>
                                        <div class="achievement-badge">
                                            <div class="badge-icon">🏆</div>
                                            <div class="badge-name"><?php echo sgipc_h($achievement['badge_name']); ?></div>
                                            <div class="badge-date"><?php echo date('M d, Y', strtotime($achievement['achievement_date'])); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <p>No achievements yet. Keep coding!</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Submissions Section -->
                        <div class="section">
                            <h3 class="section-title">Recent Submissions</h3>
                            <?php if (!empty($submissions)): ?>
                                <table class="submissions-table">
                                    <thead>
                                        <tr>
                                            <th>Contest</th>
                                            <th>Problem</th>
                                            <th>Status</th>
                                            <th>Points</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $sub): ?>
                                            <tr>
                                                <td><?php echo sgipc_h($sub['contest_name']); ?></td>
                                                <td><?php echo sgipc_h($sub['problem_name']); ?></td>
                                                <td>
                                                    <span class="status-badge status-<?php echo strtolower($sub['status']); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $sub['status'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $sub['points']; ?> pts</td>
                                                <td><?php echo date('M d H:i', strtotime($sub['submitted_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="empty-state">
                                    <p>No submissions yet. Start coding!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer style="background: #333; color: white; padding: 30px; text-align: center; margin-top: 40px;">
        <p>&copy; 2026 SGIPC - Special Group Interested In Programming Contest | KUET</p>
    </footer>

    <script>
        document.getElementById('menuBtn').addEventListener('click', () => {
            const menu = document.getElementById('menu');
            menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
        });
    </script>
</body>
</html>
