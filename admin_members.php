<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$member_id = (int)($_GET['id'] ?? 0);
$message = '';
$error = '';
$member = null;

try {
    $connection = sgipc_db_connection();

    // Handle POST actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'edit' && $member_id > 0) {
            $full_name = trim($_POST['full_name'] ?? '');
            $student_id = trim($_POST['student_id'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $department = trim($_POST['department'] ?? '');
            $batch = (int)($_POST['batch'] ?? 0);
            $status = $_POST['status'] ?? 'approved';

            if (empty($full_name)) {
                $error = 'Full name is required.';
            } else {
                $stmt = $connection->prepare(
                    'UPDATE members SET full_name=?, student_id=?, phone=?, department=?, batch=?, status=? WHERE id=?'
                );
                $stmt->bind_param('ssssisi', $full_name, $student_id, $phone, $department, $batch, $status, $member_id);
                if ($stmt->execute()) {
                    $message = 'Member updated successfully!';
                    $action = 'list';
                } else {
                    $error = 'Error updating member.';
                }
                $stmt->close();
            }
        } elseif ($action === 'delete' && $member_id > 0) {
            $stmt = $connection->prepare('DELETE FROM members WHERE id = ?');
            $stmt->bind_param('i', $member_id);
            if ($stmt->execute()) {
                $message = 'Member deleted successfully!';
                $action = 'list';
            } else {
                $error = 'Error deleting member.';
            }
            $stmt->close();
        }
    }

    // Get member for edit
    if ($action === 'edit' && $member_id > 0) {
        $stmt = $connection->prepare('SELECT * FROM members WHERE id = ?');
        $stmt->bind_param('i', $member_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $member = $result->fetch_assoc();
        $stmt->close();
    }

    // Get all members
    $members = [];
    if ($action === 'list') {
        $status_filter = $_GET['status'] ?? 'all';
        $query = 'SELECT id, full_name, email, student_id, department, batch, status, joining_date FROM members';
        
        if ($status_filter !== 'all' && in_array($status_filter, ['pending', 'approved', 'rejected', 'suspended'])) {
            $query .= ' WHERE status = "' . $connection->real_escape_string($status_filter) . '"';
        }
        
        $query .= ' ORDER BY joining_date DESC';
        $result = $connection->query($query);
        while ($row = $result->fetch_assoc()) {
            $members[] = $row;
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
    <title>Members Management - Admin</title>
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

        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
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

        .members-table {
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

        .badge-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-suspended {
            background-color: #f5f5f5;
            color: #666;
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
                <li><a href="admin_members.php" class="active">Members</a></li>
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
            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo sgipc_h($message); ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo sgipc_h($error); ?></div>
            <?php endif; ?>

            <?php if ($action === 'edit' && $member): ?>
                <div class="topbar">
                    <h1>Edit Member</h1>
                </div>

                <div class="form-container">
                    <form method="POST" action="?action=edit&id=<?php echo $member_id; ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="full_name" required value="<?php echo sgipc_h($member['full_name']); ?>">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" disabled value="<?php echo sgipc_h($member['email']); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="student_id">Student ID</label>
                                <input type="text" id="student_id" name="student_id" value="<?php echo sgipc_h($member['student_id'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo sgipc_h($member['phone'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="department">Department</label>
                                <input type="text" id="department" name="department" value="<?php echo sgipc_h($member['department'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="batch">Batch</label>
                                <input type="number" id="batch" name="batch" value="<?php echo $member['batch'] ?? ''; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="pending" <?php echo ($member['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo ($member['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo ($member['status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                <option value="suspended" <?php echo ($member['status'] === 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="btn-submit">Save Changes</button>
                            <a href="admin_members.php" class="btn-cancel">Cancel</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="topbar">
                    <h1>Members Management</h1>
                </div>

                <div class="filters">
                    <a href="?status=all" class="filter-btn <?php echo ($_GET['status'] ?? 'all') === 'all' ? 'active' : ''; ?>">All</a>
                    <a href="?status=approved" class="filter-btn <?php echo ($_GET['status'] ?? 'all') === 'approved' ? 'active' : ''; ?>">Approved</a>
                    <a href="?status=pending" class="filter-btn <?php echo ($_GET['status'] ?? 'all') === 'pending' ? 'active' : ''; ?>">Pending</a>
                    <a href="?status=suspended" class="filter-btn <?php echo ($_GET['status'] ?? 'all') === 'suspended' ? 'active' : ''; ?>">Suspended</a>
                </div>

                <div class="members-table">
                    <?php if (!empty($members)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Student ID</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $m): ?>
                                    <tr>
                                        <td><?php echo sgipc_h($m['full_name']); ?></td>
                                        <td><?php echo sgipc_h($m['email']); ?></td>
                                        <td><?php echo sgipc_h($m['student_id'] ?? '-'); ?></td>
                                        <td><?php echo sgipc_h($m['department'] ?? '-'); ?></td>
                                        <td>
                                            <span class="status-badge badge-<?php echo $m['status']; ?>">
                                                <?php echo ucfirst($m['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($m['joining_date'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?action=edit&id=<?php echo $m['id']; ?>" class="btn-small btn-edit">Edit</a>
                                                <a href="?action=delete&id=<?php echo $m['id']; ?>" class="btn-small btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-message">
                            <p>No members found</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
