<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

$dbError = null;
$teams = [];

try {
    $connection = sgipc_db_connection();
    $result = $connection->query(
        'SELECT team_name, overall_rank, rating, solved_count, contest_name, status FROM team_rankings ORDER BY overall_rank ASC, rating DESC'
    );

    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $teams[] = $row;
        }
        $result->free();
    }

    $connection->close();
} catch (Throwable $exception) {
    $dbError = 'Database is not available yet. Showing the seeded default ranking snapshot.';
    $teams = sgipc_default_team_rankings();
}

if ($teams === []) {
    $teams = sgipc_default_team_rankings();
}

if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $dbError ? 'fallback' : 'success',
        'error' => $dbError,
        'teams' => $teams
    ]);
    exit;
}

$totalTeams = count($teams);
$averageRating = $totalTeams > 0
    ? (int) round(array_sum(array_map(static fn (array $team): int => (int) $team['rating'], $teams)) / $totalTeams)
    : 0;
$highestRatedTeam = $teams[0] ?? null;
$strongestSolvedTeam = null;
foreach ($teams as $team) {
    if ($strongestSolvedTeam === null || (int) $team['solved_count'] > (int) $strongestSolvedTeam['solved_count']) {
        $strongestSolvedTeam = $team;
    }
}
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SGIPC Team Rankings | KUET Competitive Programming Community</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Sora:wght@700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="index.css" />
  </head>
  <body>
    <div class="topbar">
      <strong>SGIPC</strong> | Database-backed Team Formation Rankings
    </div>

    <header class="navbar">
      <div class="container nav-wrap">
        <a class="brand" href="index.html#home"
          ><span>SGIPC</span> - Team Formation Rankings</a
        >
        <a class="menu-btn" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none" href="index.html#home" aria-label="Back to home">←</a>
      </div>
    </header>

    <main class="container">
      <section class="reveal show">
        <p class="tagline">MySQL + PHP on XAMPP</p>
        <h1>Team Rankings</h1>
        <p class="muted">
          Live rankings are read from MySQL and styled with the same SGIPC visual language.
          This page is the backend-facing version of the team formation data.
        </p>
        <?php if ($dbError !== null): ?>
          <p style="margin-top: 12px; padding: 12px 14px; border-radius: 12px; background: rgba(217,177,95,0.12); border: 1px solid rgba(217,177,95,0.35); color: #f5e4b7;">
            <?php echo sgipc_h($dbError); ?>
          </p>
        <?php endif; ?>
      </section>

      <section class="reveal show">
        <div class="grid-3">
          <article class="card">
            <h3>Teams Stored</h3>
            <p class="muted">The current ranking set in the database.</p>
            <p style="font-size: 1.7rem; font-weight: 700; margin-top: 8px"><?php echo (int) $totalTeams; ?></p>
          </article>
          <article class="card">
            <h3>Average Rating</h3>
            <p class="muted">Across all active teams.</p>
            <p style="font-size: 1.7rem; font-weight: 700; margin-top: 8px"><?php echo (int) $averageRating; ?></p>
          </article>
          <article class="card">
            <h3>Top Team</h3>
            <p class="muted">Highest-rated group in the formation round.</p>
            <p style="font-size: 1.4rem; font-weight: 700; margin-top: 8px"><?php echo sgipc_h((string) ($highestRatedTeam['team_name'] ?? 'N/A')); ?></p>
          </article>
        </div>
      </section>

      <section class="reveal show">
        <h2>Overall Rankings</h2>
        <div class="table-wrap">
          <table class="styled-table" aria-label="Live SGIPC team ranking table">
            <thead>
              <tr>
                <th>Team</th>
                <th>Rank</th>
                <th>Rating</th>
                <th>Solved</th>
                <th>Contest</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($teams as $team): ?>
                <tr>
                  <td><?php echo sgipc_h($team['team_name'] ?? ''); ?></td>
                  <td><?php echo (int) ($team['overall_rank'] ?? 0); ?></td>
                  <td><?php echo (int) ($team['rating'] ?? 0); ?></td>
                  <td><?php echo (int) ($team['solved_count'] ?? 0); ?></td>
                  <td><?php echo sgipc_h($team['contest_name'] ?? 'Team Formation Round'); ?></td>
                  <td><?php echo sgipc_h($team['status'] ?? 'Confirmed'); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

      <section class="reveal show">
        <div class="grid-2 demo-grid">
          <article class="card">
            <h3>Most Solved</h3>
            <p class="muted">The team with the highest solved count in the current snapshot.</p>
            <p style="font-size: 1.2rem; font-weight: 700; margin-top: 10px">
              <?php echo sgipc_h((string) ($strongestSolvedTeam['team_name'] ?? 'N/A')); ?>
            </p>
          </article>
          <article class="card">
            <h3>Live Data Notes</h3>
            <ul class="resource-list">
              <li>Import the SQL schema into the sgipc_db database first</li>
              <li>Use XAMPP to start Apache and MySQL locally</li>
              <li>Open this page from the hosted server or localhost to verify live reads</li>
            </ul>
          </article>
        </div>
      </section>
    </main>

    <footer>
      <p>&copy; 2026 Special Group Interested In Programming Contest KUET | Competitive Programming Community</p>
      <p><a class="inline-link" href="index.html#resources">Back to Resources</a></p>
    </footer>
  </body>
</html>
