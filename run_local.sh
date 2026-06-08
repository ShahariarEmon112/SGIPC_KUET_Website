#!/usr/bin/env bash
MYSQL_CMD="mysql -u root"
$MYSQL_CMD -e "CREATE DATABASE IF NOT EXISTS sgipc_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php setup.php || { echo "❌ setup.php failed – aborting"; exit 1; }
echo "🚀 Starting development server at http://127.0.0.1:8000"
php -S 127.0.0.1:8000
