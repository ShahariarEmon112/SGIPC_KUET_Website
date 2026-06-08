<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$action = $_GET['action'] ?? '';
$request_id = (int)($_GET['id'] ?? 0);
$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'approve' && $request_id > 0) {
        try {
            $connection = sgipc_db_connection();

            // Get request details
            $stmt = $connection->prepare('SELECT full_name, email, interests, message FROM member_requests WHERE id = ? AND status = "pending"');
            $stmt->bind_param('i', $request_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $request = $result->fetch_assoc();
            $stmt->close();

            if ($request) {
                // Create member account
                $password_hash = password_hash('TempPass@123', PASSWORD_BCRYPT);
                $insert_stmt = $connection->prepare(
                    'INSERT INTO members (full_name, email, password_hash, interests, status, joining_date) VALUES (?, ?, ?, ?, "approved", CURRENT_TIMESTAMP)'
                );
                $insert_stmt->bind_param('ssss', $request['full_name'], $request['email'], $password_hash, $request['interests']);
                $insert_stmt->execute();
                $insert_stmt->close();

                // Update request status
                $update_stmt = $connection->prepare('UPDATE member_requests SET status = "approved", reviewed_by = ?, review_date = CURRENT_TIMESTAMP WHERE id = ?');
                $update_stmt->bind_param('ii', $_SESSION['admin_id'], $request_id);
                $update_stmt->execute();
                $update_stmt->close();

                $message = 'Member request approved successfully!';
            } else {
                $error = 'Request not found or already processed.';
            }

            $connection->close();
        } catch (Throwable $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } elseif ($action === 'reject' && $request_id > 0) {
        try {
            $connection = sgipc_db_connection();

            $update_stmt = $connection->prepare('UPDATE member_requests SET status = "rejected", reviewed_by = ?, review_date = CURRENT_TIMESTAMP WHERE id = ?');
            $update_stmt->bind_param('ii', $_SESSION['admin_id'], $request_id);
            $update_stmt->execute();
            $update_stmt->close();

            $message = 'Request rejected.';
            $connection->close();
        } catch (Throwable $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}

// Get all requests
try {
    $connection = sgipc_db_connection();

    $status_filter = $_GET['status'] ?? 'pending';
    if (!in_array($status_filter, ['pending', 'approved', 'rejected', 'all'])) {
        $status_filter = 'pending';
    }

    $query = 'SELECT id, full_name, email, interests, message, status, created_at FROM member_requests';
    if ($status_filter !== 'all') {
        $query .= ' WHERE status = "' . $connection->real_escape_string($status_filter) . '"';
    }
    $query .= ' ORDER BY created_at DESC';

    $result = $connection->query($query);
    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }

    $connection->close();
} catch (Throwable $e) {
    $requests = [];
    $error = 'Database error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Requests - Admin</title>
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

        .filters {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 16px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
        }

        .filter-btn.active {
            background-color: #667eea;
            color: white;
            border-color: #667eea;
        }

        .filter-btn:hover {
            border-color: #667eea;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .requests-table {
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

        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-rejected {
            background-color: #f8d7da;
            color: #721c24;
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
            transition: all 0.3s;
        }

        .btn-approve {
            background-color: #4caf50;
            color: white;
        }

        .btn-approve:hover {
            background-color: #45a049;
        }

        .btn-reject {
            background-color: #f44336;
            color: white;
        }

        .btn-reject:hover {
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

            .topbar {
                flex-direction: column;
                gap: 15px;
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
                <li><a href="admin_requests.php" class="active">Join Requests</a></li>
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
                <h1>Member Join Requests</h1>
                <div class="filters">
                    <a href="?status=pending" class="filter-btn <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
                    <a href="?status=approved" class="filter-btn <?php echo $status_filter === 'approved' ? 'active' : ''; ?>">Approved</a>
                    <a href="?status=rejected" class="filter-btn <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>">Rejected</a>
                    <a href="?status=all" class="filter-btn <?php echo $status_filter === 'all' ? 'active' : ''; ?>">All</a>
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo sgipc_h($message); ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo sgipc_h($error); ?></div>
            <?php endif; ?>

            <div class="requests-table">
                <?php if (!empty($requests)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Interests</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td><?php echo sgipc_h($req['full_name']); ?></td>
                                    <td><?php echo sgipc_h($req['email']); ?></td>
                                    <td><?php echo sgipc_h(substr($req['interests'], 0, 30)); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($req['created_at'])); ?></td>
                                    <td>
                                        <span class="status-badge badge-<?php echo $req['status']; ?>">
                                            <?php echo ucfirst($req['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($req['status'] === 'pending'): ?>
                                            <div class="action-buttons">
                                                <form method="POST" action="?action=approve&id=<?php echo $req['id']; ?>" style="display: inline;">
                                                    <button type="submit" class="btn-small btn-approve">Approve</button>
                                                </form>
                                                <form method="POST" action="?action=reject&id=<?php echo $req['id']; ?>" style="display: inline;">
                                                    <button type="submit" class="btn-small btn-reject">Reject</button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 12px;">Processed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-message">
                        <p>No requests found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
