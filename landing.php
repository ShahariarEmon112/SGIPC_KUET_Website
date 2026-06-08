<?php
// Landing page with registration/login options
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGIPC - Competitive Programming Club</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Sora', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Navigation */
        .navbar {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 600;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #667eea;
        }

        /* Hero Section */
        .hero {
            padding: 80px 20px;
            text-align: center;
            color: white;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            font-weight: 800;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        /* Options Container */
        .options {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
        }

        .option-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            text-align: center;
        }

        .option-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        }

        .option-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .option-card h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 26px;
        }

        .option-card p {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
            line-height: 1.6;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        /* Features Section */
        .features {
            max-width: 1200px;
            margin: 80px auto;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .features h2 {
            text-align: center;
            color: #333;
            margin-bottom: 50px;
            font-size: 32px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .feature {
            text-align: center;
        }

        .feature-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .feature h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .feature p {
            color: #666;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: 80px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }

            .nav-links {
                gap: 15px;
                font-size: 12px;
            }

            .options {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .option-card {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">🎯 SGIPC</div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="index.html">Website</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero">
        <h1>SGIPC Management System</h1>
        <p>Competitive Programming Club - Admin & Member Portal</p>
    </div>

    <!-- Options Section -->
    <div class="options">
        <!-- Admin Option -->
        <div class="option-card">
            <div class="option-icon">👨‍💼</div>
            <h2>Admin Portal</h2>
            <p>Manage contests, members, achievements, and view submissions. Full administrative access to the system.</p>
            <div class="btn-group">
                <a href="admin_login.php" class="btn btn-primary">Login as Admin</a>
                <a href="admin_register.php" class="btn btn-secondary">Register as Admin</a>
            </div>
        </div>

        <!-- Member Option -->
        <div class="option-card">
            <div class="option-icon">👤</div>
            <h2>Member Registration</h2>
            <p>Join SGIPC and participate in competitions, access learning resources, and connect with other programmers.</p>
            <div class="btn-group">
                <a href="user_register.php" class="btn btn-primary">Register as Member</a>
                <a href="index.html" class="btn btn-secondary">View Website</a>
            </div>
        </div>

        <!-- Contest Participation -->
        <div class="option-card">
            <div class="option-icon">🏆</div>
            <h2>Contests</h2>
            <p>View ongoing and upcoming contests, check leaderboards, and track your performance.</p>
            <div class="btn-group">
                <a href="contests.php" class="btn btn-primary">View Contests</a>
                <a href="rankings.php" class="btn btn-secondary">View Rankings</a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="features">
        <h2>✨ Key Features</h2>
        <div class="feature-grid">
            <div class="feature">
                <div class="feature-icon">🎯</div>
                <h3>Contest Management</h3>
                <p>Create and manage programming contests with ease</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🏅</div>
                <h3>Leaderboards</h3>
                <p>Real-time rankings and performance tracking</p>
            </div>
            <div class="feature">
                <div class="feature-icon">📚</div>
                <h3>Resources</h3>
                <p>Access learning materials and guidelines</p>
            </div>
            <div class="feature">
                <div class="feature-icon">👥</div>
                <h3>Community</h3>
                <p>Connect with fellow competitive programmers</p>
            </div>
            <div class="feature">
                <div class="feature-icon">📊</div>
                <h3>Analytics</h3>
                <p>Track progress and performance metrics</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🔒</div>
                <h3>Secure</h3>
                <p>Secure authentication and data protection</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2026 SGIPC - Special Group Interested In Programming Contest. All rights reserved.</p>
        <p>Kuet Competitive Programming Club</p>
    </div>
</body>
</html>
