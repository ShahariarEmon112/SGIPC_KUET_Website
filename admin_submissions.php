<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

try {
    $connection = sgipc_db_connection();

    // Get submissions with member and contest info
    $submissions = [];
    $query = 'SELECT s.id, s.status, s.points, s.submitted_at, m.full_name, c.contest_name, p.problem_name
              FROM submissions s
              JOIN members m ON s.member_id = m.id
              JOIN contests c ON s.contest_id = c.id
              JOIN contest_problems p ON s.problem_id = p.id
              ORDER BY s.submitted_at DESC
              LIMIT 100';
    
    $result = $connection->query($query);
    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }

    $connection->close();
} catch (Throwable $e) {
    $submissions = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submissions - Admin</title>
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
            margin-bottom: 30px;
        }

        .topbar h1 {
            font-size: 24px;
        }

        .submissions-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f5f7fa;
            border-bottom: 2px solid #ddd;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #667eea;
            font-size: 13px;
            text-transform: uppercase;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-accepted {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-wrong_answer {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-time_limit_exceeded {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-runtime_error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-compilation_error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .empty-message {
            text-align: center;
            padding: 40px;
            color: #999;
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

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">SGIPC Admin</div>
            <ul class="sidebar-menu">
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_members.php">Members</a></li>
                <li><a href="admin_requests.php">Join Requests</a></li>
                <li><a href="admin_contests.php">Contests</a></li>
                <li><a href="admin_rankings.php">Rankings</a></li>
                <li><a href="admin_achievements.php">Achievements</a></li>
                <li><a href="admin_submissions.php" class="active">Submissions</a></li>
                <li style="border-top: 1px solid rgba(255,255,255,0.2); margin-top: 20px; padding-top: 20px;">
                    <a href="admin_logout.php">Logout</a>
                </li>
            </ul>
        </aside>

        <div class="content">
            <div class="topbar">
                <h1>Recent Submissions</h1>
            </div>

            <div class="submissions-table">
                <?php if (!empty($submissions)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Contest</th>
                                <th>Problem</th>
                                <th>Status</th>
                                <th>Points</th>
                                <th>Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $sub): ?>
                                <tr>
                                    <td><?php echo sgipc_h($sub['full_name']); ?></td>
                                    <td><?php echo sgipc_h($sub['contest_name']); ?></td>
                                    <td><?php echo sgipc_h($sub['problem_name']); ?></td>
                                    <td>
                                        <span class="status-badge badge-<?php echo str_replace(' ', '_', strtolower($sub['status'])); ?>">
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
                    <div class="empty-message">
                        <p>No submissions yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
