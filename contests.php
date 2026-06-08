<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

$contests = [];
$filter = $_GET['filter'] ?? 'upcoming';

try {
    $connection = sgipc_db_connection();
    
    $query = 'SELECT id, contest_name, description, contest_type, start_time, end_time, difficulty_level, platform, registration_link, prize_pool, status FROM contests';
    
    if ($filter === 'upcoming') {
        $query .= ' WHERE status = "upcoming"';
    } elseif ($filter === 'completed') {
        $query .= ' WHERE status = "completed"';
    } elseif ($filter === 'ongoing') {
        $query .= ' WHERE status = "ongoing"';
    }
    
    $query .= ' ORDER BY start_time DESC';
    
    $result = $connection->query($query);
    while ($row = $result->fetch_assoc()) {
        $contests[] = $row;
    }
    
    $connection->close();
} catch (Throwable $e) {
    // Fallback if database fails
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contests - SGIPC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./index.css">
    <style>
        .contests-section {
            padding: 60px 20px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            min-height: 80vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-header h1 {
            font-size: 40px;
            font-weight: 800;
            color: #333;
            margin-bottom: 10px;
        }

        .section-header p {
            font-size: 16px;
            color: #666;
        }

        .filter-tabs {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 12px 24px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 14px;
        }

        .filter-tab:hover,
        .filter-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }

        .contests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .contest-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        .contest-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.3);
        }

        .contest-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            position: relative;
        }

        .contest-status {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .contest-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .contest-type {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            margin-right: 8px;
        }

        .contest-body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .contest-description {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .contest-details {
            font-size: 13px;
            margin-bottom: 15px;
        }

        .detail-item {
            margin-bottom: 10px;
            color: #666;
            display: flex;
            align-items: center;
        }

        .detail-label {
            font-weight: 600;
            color: #667eea;
            min-width: 100px;
        }

        .difficulty-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .difficulty-beginner {
            background-color: #c8e6c9;
            color: #2e7d32;
        }

        .difficulty-intermediate {
            background-color: #ffe0b2;
            color: #f57c00;
        }

        .difficulty-advanced {
            background-color: #ffccbc;
            color: #d84315;
        }

        .contest-footer {
            padding: 0 20px 20px;
            margin-top: auto;
        }

        .contest-actions {
            display: flex;
            gap: 10px;
        }

        .btn-contest {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-secondary:hover {
            background: #e8e8e8;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #666;
        }

        @media (max-width: 768px) {
            .section-header h1 {
                font-size: 28px;
            }

            .contests-grid {
                grid-template-columns: 1fr;
            }

            .filter-tabs {
                justify-content: flex-start;
                overflow-x: auto;
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
            <a class="brand" href="index.php"><span>SGIPC</span> - Contests</a>
            <button class="menu-btn" id="menuBtn" aria-label="Open menu">☰</button>
            <ul class="menu" id="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php#about">About</a></li>
                <li><a href="contests.php" class="active">Contests</a></li>
                <li><a href="rankings.php">Rankings</a></li>
                <li><a href="index.php#form-demo">Join Us</a></li>
            </ul>
        </div>
    </header>

    <main class="contests-section">
        <div class="container">
            <div class="section-header">
                <h1>Upcoming Contests</h1>
                <p>Participate in competitive programming contests and challenges</p>
            </div>

            <div class="filter-tabs">
                <a href="?filter=upcoming" class="filter-tab <?php echo $filter === 'upcoming' ? 'active' : ''; ?>">Upcoming</a>
                <a href="?filter=ongoing" class="filter-tab <?php echo $filter === 'ongoing' ? 'active' : ''; ?>">Ongoing</a>
                <a href="?filter=completed" class="filter-tab <?php echo $filter === 'completed' ? 'active' : ''; ?>">Completed</a>
            </div>

            <?php if (!empty($contests)): ?>
                <div class="contests-grid">
                    <?php foreach ($contests as $contest): ?>
                        <div class="contest-card">
                            <div class="contest-header">
                                <div class="contest-status"><?php echo ucfirst($contest['status']); ?></div>
                                <div class="contest-name"><?php echo sgipc_h($contest['contest_name']); ?></div>
                                <div>
                                    <span class="contest-type"><?php echo ucfirst($contest['contest_type']); ?></span>
                                    <span class="difficulty-badge difficulty-<?php echo $contest['difficulty_level']; ?>">
                                        <?php echo ucfirst($contest['difficulty_level']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="contest-body">
                                <?php if (!empty($contest['description'])): ?>
                                    <div class="contest-description"><?php echo sgipc_h(substr($contest['description'], 0, 100)); ?>...</div>
                                <?php endif; ?>
                                <div class="contest-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Date:</span>
                                        <span><?php echo date('M d, Y H:i', strtotime($contest['start_time'])); ?></span>
                                    </div>
                                    <?php if (!empty($contest['platform'])): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Platform:</span>
                                            <span><?php echo sgipc_h($contest['platform']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($contest['prize_pool'])): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Prize:</span>
                                            <span><?php echo sgipc_h($contest['prize_pool']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="contest-footer">
                                <div class="contest-actions">
                                    <?php if (!empty($contest['registration_link'])): ?>
                                        <a href="<?php echo sgipc_h($contest['registration_link']); ?>" target="_blank" class="btn-contest btn-primary">Register</a>
                                    <?php else: ?>
                                        <button class="btn-contest btn-primary" disabled>Coming Soon</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h2>No Contests Available</h2>
                    <p>Check back soon for upcoming contests!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer style="background: #333; color: white; padding: 30px; text-align: center;">
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
