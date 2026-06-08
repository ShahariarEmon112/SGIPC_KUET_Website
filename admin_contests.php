<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$contest_id = (int)($_GET['id'] ?? 0);
$message = '';
$error = '';
$contest = null;

try {
    $connection = sgipc_db_connection();

    // Handle POST actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'create' || $action === 'edit') {
            $name = trim($_POST['contest_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $type = $_POST['contest_type'] ?? 'online';
            $start_time = $_POST['start_time'] ?? '';
            $end_time = $_POST['end_time'] ?? '';
            $duration = (int)($_POST['duration_minutes'] ?? 0);
            $difficulty = $_POST['difficulty_level'] ?? 'intermediate';
            $platform = trim($_POST['platform'] ?? '');
            $link = trim($_POST['registration_link'] ?? '');
            $prize = trim($_POST['prize_pool'] ?? '');
            $status = $_POST['status'] ?? 'upcoming';

            if (empty($name) || empty($start_time) || empty($end_time)) {
                $error = 'Contest name, start time, and end time are required.';
            } else {
                if ($action === 'create') {
                    $stmt = $connection->prepare(
                        'INSERT INTO contests (contest_name, description, contest_type, start_time, end_time, duration_minutes, difficulty_level, platform, registration_link, prize_pool, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                    );
                    $stmt->bind_param('sssssssssssi', $name, $description, $type, $start_time, $end_time, $duration, $difficulty, $platform, $link, $prize, $status, $_SESSION['admin_id']);
                    if ($stmt->execute()) {
                        $message = 'Contest created successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Error creating contest.';
                    }
                    $stmt->close();
                } else {
                    $stmt = $connection->prepare(
                        'UPDATE contests SET contest_name=?, description=?, contest_type=?, start_time=?, end_time=?, duration_minutes=?, difficulty_level=?, platform=?, registration_link=?, prize_pool=?, status=? WHERE id=?'
                    );
                    $stmt->bind_param('sssssssssssi', $name, $description, $type, $start_time, $end_time, $duration, $difficulty, $platform, $link, $prize, $status, $contest_id);
                    if ($stmt->execute()) {
                        $message = 'Contest updated successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Error updating contest.';
                    }
                    $stmt->close();
                }
            }
        } elseif ($action === 'delete' && $contest_id > 0) {
            $stmt = $connection->prepare('DELETE FROM contests WHERE id = ?');
            $stmt->bind_param('i', $contest_id);
            if ($stmt->execute()) {
                $message = 'Contest deleted successfully!';
                $action = 'list';
            } else {
                $error = 'Error deleting contest.';
            }
            $stmt->close();
        }
    }

    // Get contest for edit
    if ($action === 'edit' && $contest_id > 0) {
        $stmt = $connection->prepare('SELECT * FROM contests WHERE id = ?');
        $stmt->bind_param('i', $contest_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $contest = $result->fetch_assoc();
        $stmt->close();
    }

    // Get all contests
    $contests = [];
    if ($action === 'list') {
        $result = $connection->query('SELECT id, contest_name, start_time, difficulty_level, status, platform FROM contests ORDER BY start_time DESC');
        while ($row = $result->fetch_assoc()) {
            $contests[] = $row;
        }
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
    <title>Contests Management - Admin</title>
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
        }

        .btn-create {
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-create:hover {
            background-color: #45a049;
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
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-submit {
            padding: 12px 30px;
            background-color: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-right: 10px;
        }

        .btn-submit:hover {
            background-color: #5568d3;
        }

        .btn-cancel {
            padding: 12px 30px;
            background-color: #999;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }

        .btn-cancel:hover {
            background-color: #777;
        }

        .contests-table {
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

        .badge-upcoming {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .badge-ongoing {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .badge-completed {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-small {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-edit {
            background-color: #2196f3;
            color: white;
        }

        .btn-edit:hover {
            background-color: #0b7dda;
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
        }

        .btn-delete:hover {
            background-color: #da190b;
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

            .form-row {
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
                <li><a href="admin_contests.php" class="active">Contests</a></li>
                <li><a href="admin_rankings.php">Rankings</a></li>
                <li><a href="admin_achievements.php">Achievements</a></li>
                <li><a href="admin_submissions.php">Submissions</a></li>
                <li style="border-top: 1px solid rgba(255,255,255,0.2); margin-top: 20px; padding-top: 20px;">
                    <a href="admin_logout.php">Logout</a>
                </li>
            </ul>
        </aside>

        <div class="content">
            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo sgipc_h($message); ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo sgipc_h($error); ?></div>
            <?php endif; ?>

            <?php if ($action === 'create' || $action === 'edit'): ?>
                <div class="topbar">
                    <h1><?php echo $action === 'create' ? 'Create Contest' : 'Edit Contest'; ?></h1>
                </div>

                <div class="form-container">
                    <form method="POST" action="?action=<?php echo $action; ?><?php echo $action === 'edit' ? '&id=' . $contest_id : ''; ?>">
                        <div class="form-group">
                            <label for="name">Contest Name *</label>
                            <input type="text" id="name" name="contest_name" required value="<?php echo sgipc_h($contest['contest_name'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description"><?php echo sgipc_h($contest['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="type">Contest Type</label>
                                <select id="type" name="contest_type">
                                    <option value="online" <?php echo ($contest['contest_type'] ?? '') === 'online' ? 'selected' : ''; ?>>Online</option>
                                    <option value="offline" <?php echo ($contest['contest_type'] ?? '') === 'offline' ? 'selected' : ''; ?>>Offline</option>
                                    <option value="virtual" <?php echo ($contest['contest_type'] ?? '') === 'virtual' ? 'selected' : ''; ?>>Virtual</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="difficulty">Difficulty Level</label>
                                <select id="difficulty" name="difficulty_level">
                                    <option value="beginner" <?php echo ($contest['difficulty_level'] ?? '') === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                    <option value="intermediate" <?php echo ($contest['difficulty_level'] ?? '') === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                    <option value="advanced" <?php echo ($contest['difficulty_level'] ?? '') === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="start">Start Time *</label>
                                <input type="datetime-local" id="start" name="start_time" required value="<?php echo sgipc_h($contest['start_time'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="end">End Time *</label>
                                <input type="datetime-local" id="end" name="end_time" required value="<?php echo sgipc_h($contest['end_time'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="duration">Duration (minutes)</label>
                                <input type="number" id="duration" name="duration_minutes" value="<?php echo $contest['duration_minutes'] ?? '120'; ?>">
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <option value="upcoming" <?php echo ($contest['status'] ?? '') === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                    <option value="ongoing" <?php echo ($contest['status'] ?? '') === 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                    <option value="completed" <?php echo ($contest['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo ($contest['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="platform">Platform</label>
                            <input type="text" id="platform" name="platform" placeholder="e.g., Codeforces, AtCoder, HackerRank" value="<?php echo sgipc_h($contest['platform'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="link">Registration Link</label>
                            <input type="url" id="link" name="registration_link" value="<?php echo sgipc_h($contest['registration_link'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="prize">Prize Pool</label>
                            <input type="text" id="prize" name="prize_pool" value="<?php echo sgipc_h($contest['prize_pool'] ?? ''); ?>">
                        </div>

                        <div>
                            <button type="submit" class="btn-submit">Save Contest</button>
                            <a href="admin_contests.php" class="btn-cancel">Cancel</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="topbar">
                    <h1>Contests Management</h1>
                    <a href="?action=create" class="btn-create">+ Create Contest</a>
                </div>

                <div class="contests-table">
                    <?php if (!empty($contests)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Start Date</th>
                                    <th>Difficulty</th>
                                    <th>Platform</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contests as $c): ?>
                                    <tr>
                                        <td><?php echo sgipc_h($c['contest_name']); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($c['start_time'])); ?></td>
                                        <td><?php echo ucfirst($c['difficulty_level']); ?></td>
                                        <td><?php echo sgipc_h($c['platform'] ?? '-'); ?></td>
                                        <td>
                                            <span class="status-badge badge-<?php echo $c['status']; ?>">
                                                <?php echo ucfirst($c['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?action=edit&id=<?php echo $c['id']; ?>" class="btn-small btn-edit">Edit</a>
                                                <a href="?action=delete&id=<?php echo $c['id']; ?>" class="btn-small btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-message">
                            <p>No contests yet. <a href="?action=create" style="color: #667eea; text-decoration: underline;">Create one now</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
