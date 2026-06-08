<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

$rankings = [];
$filter = $_GET['filter'] ?? 'all';

try {
    $connection = sgipc_db_connection();
    
    $query = 'SELECT id, team_name, overall_rank, rating, solved_count, total_points, status, wins FROM team_rankings';
    
    if ($filter === 'confirmed') {
        $query .= ' WHERE status = "Confirmed"';
    } elseif ($filter === 'standby') {
        $query .= ' WHERE status = "Standby"';
    }
    
    $query .= ' ORDER BY overall_rank ASC';
    
    $result = $connection->query($query);
    while ($row = $result->fetch_assoc()) {
        $rankings[] = $row;
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
    <title>Team Rankings - SGIPC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./index.css">
    <style>
        .rankings-section {
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
            margin-bottom: 50px;
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

        .podium {
            display: grid;
            grid-template-columns: 1fr 1.2fr 1fr;
            gap: 20px;
            margin-bottom: 50px;
            align-items: flex-end;
        }

        .podium-item {
            text-align: center;
        }

        .podium-rank {
            position: relative;
            border-radius: 12px 12px 0 0;
            color: white;
            padding: 30px 20px;
            margin-bottom: 15px;
        }

        .podium-rank.second {
            background: linear-gradient(135deg, #c0c0c0 0%, #a9a9a9 100%);
            min-height: 120px;
        }

        .podium-rank.first {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            min-height: 150px;
            order: 1;
        }

        .podium-rank.third {
            background: linear-gradient(135deg, #cd7f32 0%, #b87333 100%);
            min-height: 90px;
        }

        .podium-number {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .podium-team {
            font-size: 18px;
            font-weight: 700;
            word-break: break-word;
        }

        .podium-stats {
            font-size: 12px;
            margin-top: 10px;
            opacity: 0.9;
        }

        .trophy {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .rankings-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 20px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
        }

        td {
            padding: 18px 20px;
            border-bottom: 1px solid #eee;
            font-size: 15px;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            font-weight: 700;
            font-size: 16px;
        }

        .team-name {
            font-weight: 700;
            color: #333;
            font-size: 16px;
        }

        .stat-box {
            display: inline-block;
            background: #f0f0f0;
            padding: 8px 12px;
            border-radius: 6px;
            margin: 0 5px;
            font-size: 13px;
        }

        .stat-label {
            color: #999;
            font-size: 12px;
            display: block;
        }

        .stat-value {
            color: #667eea;
            font-weight: 700;
            font-size: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-confirmed {
            background-color: #c8e6c9;
            color: #2e7d32;
        }

        .badge-standby {
            background-color: #ffe0b2;
            color: #f57c00;
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

            .podium {
                grid-template-columns: 1fr;
                align-items: center;
            }

            .podium-rank.first {
                order: unset;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 12px;
            }

            .rank-badge {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .stat-box {
                display: block;
                margin: 5px 0;
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
            <a class="brand" href="index.php"><span>SGIPC</span> - Rankings</a>
            <button class="menu-btn" id="menuBtn" aria-label="Open menu">☰</button>
            <ul class="menu" id="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php#about">About</a></li>
                <li><a href="contests.php">Contests</a></li>
                <li><a href="rankings.php" class="active">Rankings</a></li>
                <li><a href="index.php#form-demo">Join Us</a></li>
            </ul>
        </div>
    </header>

    <main class="rankings-section">
        <div class="container">
            <div class="section-header">
                <h1>Team Rankings</h1>
                <p>Top performing teams in the SGIPC community</p>
            </div>

            <div class="filter-tabs">
                <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">All Teams</a>
                <a href="?filter=confirmed" class="filter-tab <?php echo $filter === 'confirmed' ? 'active' : ''; ?>">Confirmed</a>
                <a href="?filter=standby" class="filter-tab <?php echo $filter === 'standby' ? 'active' : ''; ?>">Standby</a>
            </div>

            <?php if (!empty($rankings)): ?>
                <!-- Podium for top 3 -->
                <?php 
                    $podium = [];
                    foreach ($rankings as $r) {
                        if ($r['overall_rank'] <= 3) {
                            $podium[$r['overall_rank']] = $r;
                        }
                    }
                ?>
                <?php if (count($podium) >= 3 || !empty($podium[1])): ?>
                    <div class="podium">
                        <!-- 2nd Place -->
                        <?php if (!empty($podium[2])): ?>
                            <div class="podium-item">
                                <div class="podium-rank second">
                                    <div class="trophy">🥈</div>
                                    <div class="podium-number">2</div>
                                    <div class="podium-team"><?php echo sgipc_h($podium[2]['team_name']); ?></div>
                                    <div class="podium-stats">Rating: <?php echo $podium[2]['rating']; ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 1st Place -->
                        <?php if (!empty($podium[1])): ?>
                            <div class="podium-item">
                                <div class="podium-rank first">
                                    <div class="trophy">🏆</div>
                                    <div class="podium-number">1</div>
                                    <div class="podium-team"><?php echo sgipc_h($podium[1]['team_name']); ?></div>
                                    <div class="podium-stats">Rating: <?php echo $podium[1]['rating']; ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 3rd Place -->
                        <?php if (!empty($podium[3])): ?>
                            <div class="podium-item">
                                <div class="podium-rank third">
                                    <div class="trophy">🥉</div>
                                    <div class="podium-number">3</div>
                                    <div class="podium-team"><?php echo sgipc_h($podium[3]['team_name']); ?></div>
                                    <div class="podium-stats">Rating: <?php echo $podium[3]['rating']; ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Full Rankings Table -->
                <div class="rankings-table">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 60px;">Rank</th>
                                <th>Team Name</th>
                                <th style="text-align: right;">Rating</th>
                                <th style="text-align: right;">Solved</th>
                                <th style="text-align: right;">Points</th>
                                <th style="text-align: right;">Wins</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rankings as $rank): ?>
                                <tr>
                                    <td>
                                        <span class="rank-badge"><?php echo $rank['overall_rank']; ?></span>
                                    </td>
                                    <td>
                                        <span class="team-name"><?php echo sgipc_h($rank['team_name']); ?></span>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="stat-box">
                                            <span class="stat-label">Rating</span>
                                            <span class="stat-value"><?php echo $rank['rating']; ?></span>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="stat-box">
                                            <span class="stat-label">Solved</span>
                                            <span class="stat-value"><?php echo $rank['solved_count']; ?></span>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="stat-box">
                                            <span class="stat-label">Points</span>
                                            <span class="stat-value"><?php echo $rank['total_points']; ?></span>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="stat-box">
                                            <span class="stat-label">Wins</span>
                                            <span class="stat-value"><?php echo $rank['wins']; ?></span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge badge-<?php echo strtolower(str_replace(' ', '', $rank['status'])); ?>">
                                            <?php echo $rank['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h2>No Rankings Available</h2>
                    <p>Rankings will be displayed once contests are completed.</p>
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
