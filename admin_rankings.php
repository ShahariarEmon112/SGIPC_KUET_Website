<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$ranking_id = (int)($_GET['id'] ?? 0);
$message = '';
$error = '';
$ranking = null;

try {
    $connection = sgipc_db_connection();

    // Handle POST actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'create' || $action === 'edit') {
            $team_name = trim($_POST['team_name'] ?? '');
            $rank = (int)($_POST['overall_rank'] ?? 0);
            $rating = (int)($_POST['rating'] ?? 0);
            $solved = (int)($_POST['solved_count'] ?? 0);
            $points = (int)($_POST['total_points'] ?? 0);
            $contest = trim($_POST['contest_name'] ?? '');
            $status = $_POST['status'] ?? 'Confirmed';
            $wins = (int)($_POST['wins'] ?? 0);

            if (empty($team_name) || $rank <= 0) {
                $error = 'Team name and rank are required.';
            } else {
                if ($action === 'create') {
                    $member_ids = json_encode([]);
                    $stmt = $connection->prepare(
                        'INSERT INTO team_rankings (team_name, member_ids, overall_rank, rating, solved_count, total_points, contest_name, status, wins) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
                    );
                    $stmt->bind_param('ssiiiiisi', $team_name, $member_ids, $rank, $rating, $solved, $points, $contest, $status, $wins);
                    if ($stmt->execute()) {
                        $message = 'Ranking created successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Error creating ranking.';
                    }
                    $stmt->close();
                } else {
                    $stmt = $connection->prepare(
                        'UPDATE team_rankings SET team_name=?, overall_rank=?, rating=?, solved_count=?, total_points=?, contest_name=?, status=?, wins=? WHERE id=?'
                    );
                    $stmt->bind_param('siiiissi', $team_name, $rank, $rating, $solved, $points, $contest, $status, $wins, $ranking_id);
                    if ($stmt->execute()) {
                        $message = 'Ranking updated successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Error updating ranking.';
                    }
                    $stmt->close();
                }
            }
        } elseif ($action === 'delete' && $ranking_id > 0) {
            $stmt = $connection->prepare('DELETE FROM team_rankings WHERE id = ?');
            $stmt->bind_param('i', $ranking_id);
            if ($stmt->execute()) {
                $message = 'Ranking deleted successfully!';
                $action = 'list';
            } else {
                $error = 'Error deleting ranking.';
            }
            $stmt->close();
        }
    }

    // Get ranking for edit
    if ($action === 'edit' && $ranking_id > 0) {
        $stmt = $connection->prepare('SELECT * FROM team_rankings WHERE id = ?');
        $stmt->bind_param('i', $ranking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ranking = $result->fetch_assoc();
        $stmt->close();
    }

    // Get all rankings
    $rankings = [];
    if ($action === 'list') {
        $result = $connection->query('SELECT id, team_name, overall_rank, rating, solved_count, total_points, status FROM team_rankings ORDER BY overall_rank ASC');
        while ($row = $result->fetch_assoc()) {
            $rankings[] = $row;
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
    <title>Rankings Management - Admin</title>
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
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
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

        .rankings-table {
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

        .rank-badge {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: 700;
            font-size: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-standby {
            background-color: #fff3cd;
            color: #856404;
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
                <li><a href="admin_contests.php">Contests</a></li>
                <li><a href="admin_rankings.php" class="active">Rankings</a></li>
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
                    <h1><?php echo $action === 'create' ? 'Create Team Ranking' : 'Edit Team Ranking'; ?></h1>
                </div>

                <div class="form-container">
                    <form method="POST" action="?action=<?php echo $action; ?><?php echo $action === 'edit' ? '&id=' . $ranking_id : ''; ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="team">Team Name *</label>
                                <input type="text" id="team" name="team_name" required value="<?php echo sgipc_h($ranking['team_name'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="rank">Overall Rank *</label>
                                <input type="number" id="rank" name="overall_rank" required min="1" value="<?php echo $ranking['overall_rank'] ?? ''; ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="rating">Rating</label>
                                <input type="number" id="rating" name="rating" value="<?php echo $ranking['rating'] ?? '0'; ?>">
                            </div>

                            <div class="form-group">
                                <label for="solved">Solved Problems</label>
                                <input type="number" id="solved" name="solved_count" value="<?php echo $ranking['solved_count'] ?? '0'; ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="points">Total Points</label>
                                <input type="number" id="points" name="total_points" value="<?php echo $ranking['total_points'] ?? '0'; ?>">
                            </div>

                            <div class="form-group">
                                <label for="wins">Wins</label>
                                <input type="number" id="wins" name="wins" value="<?php echo $ranking['wins'] ?? '0'; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contest">Contest Name</label>
                            <input type="text" id="contest" name="contest_name" value="<?php echo sgipc_h($ranking['contest_name'] ?? 'Team Formation Round'); ?>">
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="Confirmed" <?php echo ($ranking['status'] ?? '') === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="Standby" <?php echo ($ranking['status'] ?? '') === 'Standby' ? 'selected' : ''; ?>>Standby</option>
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="btn-submit">Save Ranking</button>
                            <a href="admin_rankings.php" class="btn-cancel">Cancel</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="topbar">
                    <h1>Team Rankings</h1>
                    <a href="?action=create" class="btn-create">+ Add Ranking</a>
                </div>

                <div class="rankings-table">
                    <?php if (!empty($rankings)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Team Name</th>
                                    <th>Rating</th>
                                    <th>Solved</th>
                                    <th>Points</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rankings as $r): ?>
                                    <tr>
                                        <td>
                                            <span class="rank-badge"><?php echo $r['overall_rank']; ?></span>
                                        </td>
                                        <td><strong><?php echo sgipc_h($r['team_name']); ?></strong></td>
                                        <td><?php echo $r['rating']; ?></td>
                                        <td><?php echo $r['solved_count']; ?></td>
                                        <td><?php echo $r['total_points']; ?></td>
                                        <td>
                                            <span class="status-badge badge-<?php echo strtolower(str_replace(' ', '', $r['status'])); ?>">
                                                <?php echo $r['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?action=edit&id=<?php echo $r['id']; ?>" class="btn-small btn-edit">Edit</a>
                                                <a href="?action=delete&id=<?php echo $r['id']; ?>" class="btn-small btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-message">
                            <p>No rankings yet. <a href="?action=create" style="color: #667eea; text-decoration: underline;">Create one now</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
