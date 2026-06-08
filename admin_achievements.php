<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$message = '';
$error = '';

try {
    $connection = sgipc_db_connection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $member_id = (int)($_POST['member_id'] ?? 0);
        $badge_name = trim($_POST['badge_name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($member_id <= 0 || empty($badge_name)) {
            $error = 'Member and badge name are required.';
        } else {
            $stmt = $connection->prepare('INSERT INTO achievements (member_id, badge_name, description) VALUES (?, ?, ?)');
            $stmt->bind_param('iss', $member_id, $badge_name, $description);
            if ($stmt->execute()) {
                $message = 'Achievement awarded successfully!';
            } else {
                $error = 'Error awarding achievement.';
            }
            $stmt->close();
        }
    }

    // Get members for dropdown
    $members = [];
    $result = $connection->query('SELECT id, full_name FROM members WHERE status = "approved" ORDER BY full_name');
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }

    // Get achievements
    $achievements = [];
    $result = $connection->query('SELECT a.id, a.badge_name, a.description, a.achievement_date, m.full_name FROM achievements a JOIN members m ON a.member_id = m.id ORDER BY a.achievement_date DESC LIMIT 100');
    while ($row = $result->fetch_assoc()) {
        $achievements[] = $row;
    }

    $connection->close();
} catch (Throwable $e) {
    $error = 'Database error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements - Admin</title>
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

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-submit {
            padding: 12px 30px;
            background-color: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-submit:hover {
            background-color: #5568d3;
        }

        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .achievement-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .badge-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .badge-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 8px;
            color: #667eea;
        }

        .badge-member {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }

        .badge-date {
            font-size: 12px;
            color: #999;
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

            .achievements-grid {
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
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_members.php">Members</a></li>
                <li><a href="admin_requests.php">Join Requests</a></li>
                <li><a href="admin_contests.php">Contests</a></li>
                <li><a href="admin_rankings.php">Rankings</a></li>
                <li><a href="admin_achievements.php" class="active">Achievements</a></li>
                <li><a href="admin_submissions.php">Submissions</a></li>
                <li style="border-top: 1px solid rgba(255,255,255,0.2); margin-top: 20px; padding-top: 20px;">
                    <a href="admin_logout.php">Logout</a>
                </li>
            </ul>
        </aside>

        <div class="content">
            <div class="topbar">
                <h1>Achievements & Badges</h1>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo sgipc_h($message); ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo sgipc_h($error); ?></div>
            <?php endif; ?>

            <div class="form-container">
                <h2 style="margin-bottom: 20px; font-size: 18px;">Award Achievement</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="member">Member *</label>
                        <select id="member" name="member_id" required>
                            <option value="">Select a member...</option>
                            <?php foreach ($members as $m): ?>
                                <option value="<?php echo $m['id']; ?>"><?php echo sgipc_h($m['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="badge">Badge Name *</label>
                        <input type="text" id="badge" name="badge_name" placeholder="e.g., First AC, Top Solver" required>
                    </div>

                    <div class="form-group">
                        <label for="desc">Description</label>
                        <textarea id="desc" name="description" placeholder="Brief description of the achievement..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Award Achievement</button>
                </form>
            </div>

            <div>
                <h2 style="margin-bottom: 20px; font-size: 18px;">Recent Achievements</h2>
                <?php if (!empty($achievements)): ?>
                    <div class="achievements-grid">
                        <?php foreach ($achievements as $a): ?>
                            <div class="achievement-card">
                                <div class="badge-icon">🏆</div>
                                <div class="badge-title"><?php echo sgipc_h($a['badge_name']); ?></div>
                                <div class="badge-member"><?php echo sgipc_h($a['full_name']); ?></div>
                                <?php if (!empty($a['description'])): ?>
                                    <div style="font-size: 13px; color: #666; margin-bottom: 10px;">
                                        <?php echo sgipc_h(substr($a['description'], 0, 50)); ?>...
                                    </div>
                                <?php endif; ?>
                                <div class="badge-date"><?php echo date('M d, Y', strtotime($a['achievement_date'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-message">
                        <p>No achievements awarded yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
