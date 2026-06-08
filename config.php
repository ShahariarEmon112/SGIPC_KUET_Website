<?php

declare(strict_types=1);

function sgipc_db_connection(): mysqli
{
    $host = getenv('SGIPC_DB_HOST') ?: '127.0.0.1';
    $user = getenv('SGIPC_DB_USER') ?: 'shahariar';
    $password = getenv('SGIPC_DB_PASSWORD') ?: '';
    $database = getenv('SGIPC_DB_NAME') ?: 'sgipc_db';
    $port = (int)(getenv('SGIPC_DB_PORT') ?: '3307');
    $socket = getenv('SGIPC_DB_SOCKET') ?: '/home/shahariar/sgipc_mysql_data/mysql.sock';

    // If socket doesn't exist, try connecting standard TCP with default XAMPP
    if (!file_exists($socket)) {
        $socket = null;
        $port = 3306;
        $user = 'root';
    }

    $connection = @new mysqli($host, $user, $password, $database, $port, $socket);

    if ($connection->connect_errno) {
        throw new RuntimeException('Database connection failed: ' . $connection->connect_error);
    }

    $connection->set_charset('utf8mb4');

    return $connection;
}

function sgipc_h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function sgipc_default_team_rankings(): array
{
    return [
        [
            'team_name' => 'KUET_Team1',
            'overall_rank' => 1,
            'rating' => 2450,
            'solved_count' => 11,
            'contest_name' => 'Team Formation Round',
            'status' => 'Confirmed',
        ],
        [
            'team_name' => 'KUET_Team2',
            'overall_rank' => 2,
            'rating' => 2385,
            'solved_count' => 10,
            'contest_name' => 'Team Formation Round',
            'status' => 'Confirmed',
        ],
        [
            'team_name' => 'KUET_Team3',
            'overall_rank' => 3,
            'rating' => 2310,
            'solved_count' => 10,
            'contest_name' => 'Team Formation Round',
            'status' => 'Confirmed',
        ],
        [
            'team_name' => 'KUET_Team4',
            'overall_rank' => 4,
            'rating' => 2240,
            'solved_count' => 9,
            'contest_name' => 'Team Formation Round',
            'status' => 'Confirmed',
        ],
        [
            'team_name' => 'KUET_Team5',
            'overall_rank' => 5,
            'rating' => 2185,
            'solved_count' => 8,
            'contest_name' => 'Team Formation Round',
            'status' => 'Standby',
        ],
        [
            'team_name' => 'KUET_Team6',
            'overall_rank' => 6,
            'rating' => 2100,
            'solved_count' => 8,
            'contest_name' => 'Team Formation Round',
            'status' => 'Standby',
        ],
    ];
}
