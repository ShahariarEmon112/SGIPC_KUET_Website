<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

try {
    $connection = sgipc_db_connection();

    // Get statistics
    $stats = [];

    // Total members
    $result = $connection->query('SELECT COUNT(*) as count FROM members WHERE status = "approved"');
    $stats['total_members'] = $result->fetch_assoc()['count'] ?? 0;

    // Pending requests
    $result = $connection->query('SELECT COUNT(*) as count FROM member_requests WHERE status = "pending"');
    $stats['pending_requests'] = $result->fetch_assoc()['count'] ?? 0;

    // Total contests
    $result = $connection->query('SELECT COUNT(*) as count FROM contests');
    $stats['total_contests'] = $result->fetch_assoc()['count'] ?? 0;

    // Upcoming contests
    $result = $connection->query('SELECT COUNT(*) as count FROM contests WHERE status = "upcoming"');
    $stats['upcoming_contests'] = $result->fetch_assoc()['count'] ?? 0;

    // Total submissions
    $result = $connection->query('SELECT COUNT(*) as count FROM submissions');
    $stats['total_submissions'] = $result->fetch_assoc()['count'] ?? 0;

    // Accepted submissions
    $result = $connection->query('SELECT COUNT(*) as count FROM submissions WHERE status = "accepted"');
    $stats['accepted_submissions'] = $result->fetch_assoc()['count'] ?? 0;

    $connection->close();
} catch (Throwable $e) {
    $stats = ['total_members' => 0, 'pending_requests' => 0, 'total_contests' => 0, 'upcoming_contests' => 0, 'total_submissions' => 0, 'accepted_submissions' => 0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SGIPC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 15px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 14px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .content {
            margin-left: 250px;
            flex: 1;
            padding: 30px;
        }

        .topbar {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .topbar h1 {
            font-size: 24px;
            color: #333;
        }

        .user-info {
            text-align: right;
        }

        .user-info p {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .btn-logout {
            padding: 8px 16px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
        }

        .btn-logout:hover {
            background-color: #da190b;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
        }

        .stat-card.members {
            border-left-color: #4caf50;
        }

        .stat-card.requests {
            border-left-color: #ff9800;
        }

        .stat-card.contests {
            border-left-color: #2196f3;
        }

        .stat-card.submissions {
            border-left-color: #9c27b0;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-card.members .stat-number {
            color: #4caf50;
        }

        .stat-card.requests .stat-number {
            color: #ff9800;
        }

        .stat-card.contests .stat-number {
            color: #2196f3;
        }

        .stat-card.submissions .stat-number {
            color: #9c27b0;
        }

        .stat-label {
            font-size: 14px;
            color: #999;
            font-weight: 600;
        }

        .quick-actions {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .quick-actions h2 {
            margin-bottom: 20px;
            font-size: 18px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .btn-action {
            padding: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: transform 0.2s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background-color: #667eea;
            color: white;
        }

        .btn-success {
            background-color: #4caf50;
            color: white;
        }

        .btn-warning {
            background-color: #ff9800;
            color: white;
        }

        .btn-info {
            background-color: #2196f3;
            color: white;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">SGIPC Admin</div>
            <ul class="sidebar-menu">
                <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="admin_members.php">Members</a></li>
                <li><a href="admin_requests.php">Join Requests</a></li>
                <li><a href="admin_contests.php">Contests</a></li>
                <li><a href="admin_rankings.php">Rankings</a></li>
                <li><a href="admin_achievements.php">Achievements</a></li>
                <li><a href="admin_submissions.php">Submissions</a></li>
                <li style="border-top: 1px solid rgba(255,255,255,0.2); margin-top: 20px; padding-top: 20px;">
                    <a href="admin_logout.php">Logout</a>
                </li>
            </ul>
        </aside>

        <div class="content">
            <div class="topbar">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <p>Welcome, <strong><?php echo sgipc_h($_SESSION['admin_username']); ?></strong></p>
                    <a href="admin_logout.php" class="btn-logout">Logout</a>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card members">
                    <div class="stat-number"><?php echo $stats['total_members']; ?></div>
                    <div class="stat-label">Total Members</div>
                </div>

                <div class="stat-card requests">
                    <div class="stat-number"><?php echo $stats['pending_requests']; ?></div>
                    <div class="stat-label">Pending Requests</div>
                </div>

                <div class="stat-card contests">
                    <div class="stat-number"><?php echo $stats['total_contests']; ?></div>
                    <div class="stat-label">Total Contests</div>
                </div>

                <div class="stat-card contests">
                    <div class="stat-number"><?php echo $stats['upcoming_contests']; ?></div>
                    <div class="stat-label">Upcoming Contests</div>
                </div>

                <div class="stat-card submissions">
                    <div class="stat-number"><?php echo $stats['total_submissions']; ?></div>
                    <div class="stat-label">Total Submissions</div>
                </div>

                <div class="stat-card submissions">
                    <div class="stat-number"><?php echo $stats['accepted_submissions']; ?></div>
                    <div class="stat-label">Accepted Solutions</div>
                </div>
            </div>

            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="admin_requests.php" class="btn-action btn-warning">Review Join Requests</a>
                    <a href="admin_contests.php?action=create" class="btn-action btn-success">Create Contest</a>
                    <a href="admin_members.php" class="btn-action btn-primary">Manage Members</a>
                    <a href="admin_rankings.php" class="btn-action btn-info">Update Rankings</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
