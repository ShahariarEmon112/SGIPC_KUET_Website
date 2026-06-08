CREATE DATABASE IF NOT EXISTS sgipc_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sgipc_db;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  role ENUM('superadmin', 'admin', 'moderator') DEFAULT 'admin',
  is_active TINYINT DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login TIMESTAMP NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Members Table
CREATE TABLE IF NOT EXISTS members (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  student_id VARCHAR(50),
  phone VARCHAR(20),
  department VARCHAR(50),
  batch INT,
  interests TEXT,
  profile_picture VARCHAR(255),
  status ENUM('pending', 'approved', 'rejected', 'suspended') DEFAULT 'pending',
  joining_date TIMESTAMP NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_status (status),
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Member Requests (Join Requests)
CREATE TABLE IF NOT EXISTS member_requests (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(120) NOT NULL,
  interests TEXT,
  message TEXT,
  status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  reviewed_by INT UNSIGNED,
  review_date TIMESTAMP NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_status (status),
  FOREIGN KEY (reviewed_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contests Table
CREATE TABLE IF NOT EXISTS contests (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  contest_name VARCHAR(200) NOT NULL,
  description TEXT,
  contest_type ENUM('online', 'offline', 'virtual') DEFAULT 'online',
  start_time DATETIME NOT NULL,
  end_time DATETIME NOT NULL,
  duration_minutes INT,
  difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'intermediate',
  platform VARCHAR(100),
  registration_link VARCHAR(500),
  prize_pool VARCHAR(255),
  max_participants INT,
  status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
  created_by INT UNSIGNED,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  INDEX idx_status (status),
  INDEX idx_start_time (start_time),
  FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contest Problems
CREATE TABLE IF NOT EXISTS contest_problems (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  contest_id INT UNSIGNED NOT NULL,
  problem_name VARCHAR(200) NOT NULL,
  problem_code CHAR(5),
  difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
  points INT DEFAULT 100,
  solved_count INT DEFAULT 0,
  sample_input TEXT,
  sample_output TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (contest_id) REFERENCES contests(id) ON DELETE CASCADE,
  INDEX idx_contest (contest_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Team Rankings Table
CREATE TABLE IF NOT EXISTS team_rankings (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  team_name VARCHAR(100) NOT NULL,
  member_ids JSON,
  overall_rank INT UNSIGNED NOT NULL,
  rating INT UNSIGNED DEFAULT 0,
  solved_count INT UNSIGNED DEFAULT 0,
  total_points INT UNSIGNED DEFAULT 0,
  contest_name VARCHAR(150),
  status VARCHAR(50) DEFAULT 'Confirmed',
  wins INT DEFAULT 0,
  losses INT DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_team_name (team_name),
  INDEX idx_overall_rank (overall_rank)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Submissions Table
CREATE TABLE IF NOT EXISTS submissions (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  member_id INT UNSIGNED NOT NULL,
  contest_id INT UNSIGNED NOT NULL,
  problem_id INT UNSIGNED NOT NULL,
  submission_code LONGTEXT,
  language VARCHAR(50),
  status ENUM('pending', 'accepted', 'wrong_answer', 'time_limit_exceeded', 'runtime_error', 'compilation_error') DEFAULT 'pending',
  points INT DEFAULT 0,
  execution_time FLOAT,
  memory_used INT,
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
  FOREIGN KEY (contest_id) REFERENCES contests(id) ON DELETE CASCADE,
  FOREIGN KEY (problem_id) REFERENCES contest_problems(id) ON DELETE CASCADE,
  INDEX idx_member (member_id),
  INDEX idx_contest (contest_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Achievements/Badges Table
CREATE TABLE IF NOT EXISTS achievements (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  member_id INT UNSIGNED NOT NULL,
  badge_name VARCHAR(100) NOT NULL,
  badge_icon VARCHAR(255),
  description TEXT,
  achievement_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
  INDEX idx_member (member_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contest Registrations (for external contests)
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

-- Insert sample admin user (username: admin, password: Admin@123)
INSERT INTO admin_users (username, email, password_hash, role) VALUES 
('admin', 'admin@sgipc.com', '$2y$10$6xLk5NnIWc6p5VbNe4b8Ou5QNzJU6VWzCTqKtJlJZ3FdKn2kZJi4G', 'superadmin')
ON DUPLICATE KEY UPDATE password_hash = PASSWORD_HASH;

-- Insert sample data
INSERT INTO team_rankings (team_name, member_ids, overall_rank, rating, solved_count, total_points, contest_name, status, wins) VALUES
  ('KUET_Team1', '[]', 1, 2450, 11, 1100, 'Team Formation Round', 'Confirmed', 15),
  ('KUET_Team2', '[]', 2, 2385, 10, 1000, 'Team Formation Round', 'Confirmed', 14),
  ('KUET_Team3', '[]', 3, 2310, 10, 1000, 'Team Formation Round', 'Confirmed', 13),
  ('KUET_Team4', '[]', 4, 2240, 9, 900, 'Team Formation Round', 'Confirmed', 12),
  ('KUET_Team5', '[]', 5, 2185, 8, 800, 'Team Formation Round', 'Standby', 10),
  ('KUET_Team6', '[]', 6, 2100, 8, 800, 'Team Formation Round', 'Standby', 9)
ON DUPLICATE KEY UPDATE 
  rating = VALUES(rating),
  solved_count = VALUES(solved_count),
  total_points = VALUES(total_points),
  wins = VALUES(wins),
  updated_at = CURRENT_TIMESTAMP;

-- Insert sample contests
INSERT INTO contests (contest_name, description, contest_type, start_time, end_time, duration_minutes, difficulty_level, platform, status, created_by) VALUES
('SGIPC Practice Round 1', 'First practice round for beginners', 'online', '2026-06-15 10:00:00', '2026-06-15 12:00:00', 120, 'beginner', 'Codeforces', 'upcoming', 1),
('SGIPC Qualification Round', 'Qualification round for main contest', 'online', '2026-06-22 14:00:00', '2026-06-22 16:30:00', 150, 'intermediate', 'Codeforces', 'upcoming', 1),
('ICPC Preparation Marathon', 'Marathon for ICPC preparation', 'virtual', '2026-07-01 09:00:00', '2026-07-02 09:00:00', 1440, 'advanced', 'AtCoder', 'upcoming', 1)
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;
