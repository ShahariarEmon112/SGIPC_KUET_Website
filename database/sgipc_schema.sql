CREATE DATABASE IF NOT EXISTS sgipc_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sgipc_db;

CREATE TABLE IF NOT EXISTS team_rankings (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  team_name VARCHAR(100) NOT NULL,
  overall_rank INT UNSIGNED NOT NULL,
  rating INT UNSIGNED NOT NULL,
  solved_count INT UNSIGNED NOT NULL,
  contest_name VARCHAR(150) NOT NULL DEFAULT 'Team Formation Round',
  status VARCHAR(50) NOT NULL DEFAULT 'Confirmed',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_team_name (team_name),
  UNIQUE KEY uniq_overall_rank (overall_rank)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contest_registrations (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  gender ENUM('male', 'female', 'other') NOT NULL,
  interests TEXT NOT NULL,
  level VARCHAR(30) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO team_rankings (team_name, overall_rank, rating, solved_count, contest_name, status) VALUES
  ('KUET_Team1', 1, 2450, 11, 'Team Formation Round', 'Confirmed'),
  ('KUET_Team2', 2, 2385, 10, 'Team Formation Round', 'Confirmed'),
  ('KUET_Team3', 3, 2310, 10, 'Team Formation Round', 'Confirmed'),
  ('KUET_Team4', 4, 2240, 9, 'Team Formation Round', 'Confirmed'),
  ('KUET_Team5', 5, 2185, 8, 'Team Formation Round', 'Standby'),
  ('KUET_Team6', 6, 2100, 8, 'Team Formation Round', 'Standby')
ON DUPLICATE KEY UPDATE
  rating = VALUES(rating),
  solved_count = VALUES(solved_count),
  contest_name = VALUES(contest_name),
  status = VALUES(status),
  updated_at = CURRENT_TIMESTAMP;
