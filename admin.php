<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

$connection = sgipc_db_connection();
$message = '';
$messageType = 'success';

    // Handle Actions (Create, Update, Delete, Member Requests)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        // -- Team Rankings Actions --
        if ($action === 'add_team' || $action === 'update_team') {
            $team_name = trim($_POST['team_name'] ?? '');
            $overall_rank = (int)($_POST['overall_rank'] ?? 0);
            $rating = (int)($_POST['rating'] ?? 0);
            $solved_count = (int)($_POST['solved_count'] ?? 0);
            $contest_name = trim($_POST['contest_name'] ?? 'Team Formation Round');
            $status = trim($_POST['status'] ?? 'Confirmed');
            
            if ($action === 'add_team') {
                $stmt = $connection->prepare('INSERT INTO team_rankings (team_name, overall_rank, rating, solved_count, contest_name, status) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('siiiss', $team_name, $overall_rank, $rating, $solved_count, $contest_name, $status);
            } else {
                $id = (int)($_POST['id'] ?? 0);
                $stmt = $connection->prepare('UPDATE team_rankings SET team_name=?, overall_rank=?, rating=?, solved_count=?, contest_name=?, status=? WHERE id=?');
                $stmt->bind_param('siiissi', $team_name, $overall_rank, $rating, $solved_count, $contest_name, $status, $id);
            }
            if ($stmt->execute()) {
                $message = $action === 'add_team' ? "Team added successfully." : "Team updated successfully.";
                $messageType = 'success';
            } else {
                $message = "Error: " . $stmt->error;
                $messageType = 'error';
            }
            $stmt->close();
        } elseif ($action === 'delete_team') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $connection->prepare('DELETE FROM team_rankings WHERE id=?');
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                $message = "Team deleted successfully.";
                $messageType = 'success';
            } else {
                $message = "Error: " . $stmt->error;
                $messageType = 'error';
            }
            $stmt->close();
        }
        // -- Registration Actions --
        elseif ($action === 'delete_registration') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $connection->prepare('DELETE FROM contest_registrations WHERE id=?');
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                $message = "Registration deleted successfully.";
                $messageType = 'success';
            } else {
                $message = "Error: " . $stmt->error;
                $messageType = 'error';
            }
            $stmt->close();
        }
        // -- Contest Actions --
        elseif ($action === 'add_contest' || $action === 'update_contest') {
            $title = trim($_POST['title'] ?? '');
            $date = $_POST['date'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $status = trim($_POST['status'] ?? 'Upcoming');
            if ($action === 'add_contest') {
                $stmt = $connection->prepare('INSERT INTO contests (title, contest_date, description, status) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('ssss', $title, $date, $description, $status);
            } else {
                $id = (int)($_POST['id'] ?? 0);
                $stmt = $connection->prepare('UPDATE contests SET title=?, contest_date=?, description=?, status=? WHERE id=?');
                $stmt->bind_param('ssssi', $title, $date, $description, $status, $id);
            }
            if ($stmt->execute()) {
                $message = $action === 'add_contest' ? "Contest added successfully." : "Contest updated successfully.";
                $messageType = 'success';
            } else {
                $message = "Error: " . $stmt->error;
                $messageType = 'error';
            }
            $stmt->close();
        } elseif ($action === 'delete_contest') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $connection->prepare('DELETE FROM contests WHERE id=?');
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                $message = "Contest deleted successfully.";
                $messageType = 'success';
            } else {
                $message = "Error: " . $stmt->error;
                $messageType = 'error';
            }
            $stmt->close();
        }
        // -- Member Request Actions --
        elseif ($action === 'accept_request' || $action === 'reject_request') {
            $id = (int)($_POST['id'] ?? 0);
            $newStatus = $action === 'accept_request' ? 'Accepted' : 'Rejected';
            $stmt = $connection->prepare('UPDATE member_requests SET status=?, reviewed_at=NOW() WHERE id=?');
            $stmt->bind_param('si', $newStatus, $id);
            if ($stmt->execute()) {
                $message = "Request {$newStatus} successfully.";
                $messageType = 'success';
            } else {
                $message = "Error: " . $stmt->error;
                $messageType = 'error';
            }
            $stmt->close();
        }
    }
        $team_name = trim($_POST['team_name'] ?? '');
        $overall_rank = (int)($_POST['overall_rank'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $solved_count = (int)($_POST['solved_count'] ?? 0);
        $contest_name = trim($_POST['contest_name'] ?? 'Team Formation Round');
        $status = trim($_POST['status'] ?? 'Confirmed');

        if ($action === 'add_team') {
            $stmt = $connection->prepare('INSERT INTO team_rankings (team_name, overall_rank, rating, solved_count, contest_name, status) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('siiiss', $team_name, $overall_rank, $rating, $solved_count, $contest_name, $status);
        } else {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $connection->prepare('UPDATE team_rankings SET team_name=?, overall_rank=?, rating=?, solved_count=?, contest_name=?, status=? WHERE id=?');
            $stmt->bind_param('siiissi', $team_name, $overall_rank, $rating, $solved_count, $contest_name, $status, $id);
        }
        
        if ($stmt->execute()) {
            $message = $action === 'add_team' ? "Team added successfully." : "Team updated successfully.";
            $messageType = 'success';
        } else {
            $message = "Error: " . $stmt->error;
            $messageType = 'error';
        }
        $stmt->close();
    } elseif ($action === 'delete_team') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $connection->prepare('DELETE FROM team_rankings WHERE id=?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $message = "Team deleted successfully.";
            $messageType = 'success';
        } else {
            $message = "Error: " . $stmt->error;
            $messageType = 'error';
        }
        $stmt->close();
    }
    
    // -- Registration Actions --
    elseif ($action === 'delete_registration') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $connection->prepare('DELETE FROM contest_registrations WHERE id=?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $message = "Registration deleted successfully.";
            $messageType = 'success';
        } else {
            $message = "Error: " . $stmt->error;
            $messageType = 'error';
        }
        $stmt->close();
    }
}

// Fetch Teams
$teams = [];
$res = $connection->query('SELECT * FROM team_rankings ORDER BY overall_rank ASC');
while ($row = $res->fetch_assoc()) {
    $teams[] = $row;
}
$res->free();

// Fetch Registrations
$registrations = [];
$res = $connection->query('SELECT * FROM contest_registrations ORDER BY created_at DESC');
while ($row = $res->fetch_assoc()) {
    $registrations[] = $row;
}
$res->free();

// Fetch Contests
$contests = [];
$res = $connection->query('SELECT * FROM contests ORDER BY contest_date DESC');
while ($row = $res->fetch_assoc()) {
    $contests[] = $row;
}
$res->free();

// Fetch Member Requests (pending only)
$member_requests = [];
$res = $connection->query("SELECT * FROM member_requests WHERE status='Pending' ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) {
    $member_requests[] = $row;
}
$res->free();

<?php
// Edit Mode detection for teams
$edit_team = null;
if (isset($_GET['edit_team'])) {
    $edit_id = (int)$_GET['edit_team'];
    $stmt = $connection->prepare('SELECT * FROM team_rankings WHERE id=?');
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $edit_team = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Edit Mode detection for contests
$edit_contest = null;
if (isset($_GET['edit_contest'])) {
    $edit_id = (int)$_GET['edit_contest'];
    $stmt = $connection->prepare('SELECT * FROM contests WHERE id=?');
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $edit_contest = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
$connection->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SGIPC Admin Panel</title>
  <link rel="stylesheet" href="./index.css" />
  <style>
    .admin-container { padding: 40px 0; }
    .btn-small { padding: 5px 10px; font-size: 0.8rem; border-radius: 6px; cursor: pointer; border:none; margin-right: 5px; }
    .btn-edit { background: var(--primary); color: #fff; text-decoration: none; display: inline-block;}
    .btn-danger { background: #ef4444; color: #fff; }
    .message-box { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
    .msg-success { background: rgba(72,187,120,0.12); border: 1px solid rgba(72,187,120,0.4); color: #b9f5c8; }
    .msg-error { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.35); color: #f5d0d0; }
    form { display: inline; }
  </style>
</head>
<body>
  <div class="topbar"><strong>SGIPC</strong> | Administration Panel</div>
  <header class="navbar">
    <div class="container nav-wrap">
      <a class="brand" href="index.php"><span>SGIPC</span> - Return to Home</a>
    </div>
  </header>
  
  <main class="container admin-container">
    <section class="reveal show" style="padding-top: 0; border: none; background: none;">
      <h1>Admin Dashboard</h1>
      <p class="muted">Manage SGIPC Team Rankings and Contest Registrations dynamically.</p>
      
      <?php if ($message): ?>
        <div class="message-box <?php echo $messageType === 'success' ? 'msg-success' : 'msg-error'; ?>" style="margin-top: 20px;">
            <?php echo sgipc_h($message); ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="reveal show">
      <h2>Team Rankings (CRUD)</h2>
      
      <div class="card" style="margin-bottom: 20px;">
        <h3><?php echo $edit_team ? 'Edit Team' : 'Add New Team'; ?></h3>
        <form action="admin.php" method="POST" class="form-grid" style="display:grid; margin-top: 15px;">
            <input type="hidden" name="action" value="<?php echo $edit_team ? 'update_team' : 'add_team'; ?>">
            <?php if ($edit_team): ?>
                <input type="hidden" name="id" value="<?php echo $edit_team['id']; ?>">
            <?php endif; ?>
            
            <div class="field">
                <label>Team Name</label>
                <input type="text" name="team_name" value="<?php echo sgipc_h($edit_team['team_name'] ?? ''); ?>" required>
            </div>
            <div class="field">
                <label>Overall Rank</label>
                <input type="number" name="overall_rank" value="<?php echo (int)($edit_team['overall_rank'] ?? 0); ?>" required>
            </div>
            <div class="field">
                <label>Rating</label>
                <input type="number" name="rating" value="<?php echo (int)($edit_team['rating'] ?? 0); ?>" required>
            </div>
            <div class="field">
                <label>Solved Count</label>
                <input type="number" name="solved_count" value="<?php echo (int)($edit_team['solved_count'] ?? 0); ?>" required>
            </div>
            <div class="field">
                <label>Contest Name</label>
                <input type="text" name="contest_name" value="<?php echo sgipc_h($edit_team['contest_name'] ?? 'Team Formation Round'); ?>" required>
            </div>
            <div class="field">
                <label>Status</label>
                <select name="status">
                    <option <?php echo ($edit_team['status'] ?? '') === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option <?php echo ($edit_team['status'] ?? '') === 'Standby' ? 'selected' : ''; ?>>Standby</option>
                </select>
            </div>
            <div class="full-width" style="margin-top: 10px;">
                <button type="submit" class="submit-btn"><?php echo $edit_team ? 'Update Team' : 'Add Team'; ?></button>
                <?php if ($edit_team): ?>
                    <a href="admin.php" class="inline-link" style="margin-left:15px;">Cancel Edit</a>
                <?php endif; ?>
            </div>
        </form>
      </div>

      <div class="table-wrap">
        <table class="styled-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Team</th>
              <th>Rank</th>
              <th>Rating</th>
              <th>Solved</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($teams as $t): ?>
            <tr>
              <td><?php echo (int)$t['id']; ?></td>
              <td><?php echo sgipc_h($t['team_name']); ?></td>
              <td><?php echo (int)$t['overall_rank']; ?></td>
              <td><?php echo (int)$t['rating']; ?></td>
              <td><?php echo (int)$t['solved_count']; ?></td>
              <td><?php echo sgipc_h($t['status']); ?></td>
              <td>
                <a href="admin.php?edit_team=<?php echo $t['id']; ?>" class="btn-small btn-edit">Edit</a>
                <form action="admin.php" method="POST" onsubmit="return confirm('Delete this team?');">
                    <input type="hidden" name="action" value="delete_team">
                    <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                    <button type="submit" class="btn-small btn-danger">Delete</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="reveal show">
      <h2>Contest Registrations (Read & Delete)</h2>
      <div class="table-wrap">
        <table class="styled-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Gender</th>
              <th>Level</th>
              <th>Interests</th>
              <th>Message</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($registrations as $r): ?>
            <tr>
              <td><?php echo (int)$r['id']; ?></td>
              <td><?php echo sgipc_h($r['full_name']); ?></td>
              <td><?php echo sgipc_h($r['gender']); ?></td>
              <td><?php echo sgipc_h($r['level']); ?></td>
              <td><?php echo sgipc_h($r['interests']); ?></td>
              <td><?php echo sgipc_h($r['message']); ?></td>
              <td><?php echo sgipc_h($r['created_at']); ?></td>
              <td>
                <form action="admin.php" method="POST" onsubmit="return confirm('Delete this registration?');">
                    <input type="hidden" name="action" value="delete_registration">
                    <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                    <button type="submit" class="btn-small btn-danger">Delete</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($registrations)): ?>
            <tr><td colspan="8">No registrations found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>
</body>
</html>
