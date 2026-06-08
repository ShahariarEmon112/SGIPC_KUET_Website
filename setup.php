<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

echo "Initializing SGIPC database...\n";

$queries = [];
$queries[] = "CREATE TABLE IF NOT EXISTS contests (\n    id INT AUTO_INCREMENT PRIMARY KEY,\n    title VARCHAR(255) NOT NULL,\n    contest_date DATE NOT NULL,\n    description TEXT,\n    status ENUM('Upcoming','Ongoing','Finished') NOT NULL DEFAULT 'Upcoming',\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$queries[] = "CREATE TABLE IF NOT EXISTS member_requests (\n    id INT AUTO_INCREMENT PRIMARY KEY,\n    full_name VARCHAR(255) NOT NULL,\n    email VARCHAR(255) NOT NULL,\n    interests VARCHAR(255),\n    message TEXT,\n    status ENUM('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending',\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n    reviewed_at TIMESTAMP NULL\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

try {
    $conn = sgipc_db_connection();
    foreach ($queries as $sql) {
        if ($conn->query($sql) === true) {
            echo "✔︎ " . substr($sql, 0, strpos($sql, '(')) . " created successfully.\n";
        } else {
            echo "✘ Error creating table: " . $conn->error . "\n";
        }
    }
    $conn->close();
    echo "Database initialization finished.\n";
} catch (Throwable $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
