<?php

declare(strict_types=1);

session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $level = $_POST['level'] ?? '';
    $interests = $_POST['interests'] ?? [];
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($full_name) || empty($email) || empty($gender) || empty($level) || empty($interests) || empty($message)) {
        $error = '❌ All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '❌ Invalid email address';
    } else {
        // Database connection
        require 'config.php';
        try {
            $conn = sgipc_db_connection();
            
            $interests_str = implode(', ', $interests);
            $password_hash = password_hash('TempPassword123!', PASSWORD_BCRYPT);
            
            // Insert into contest_registrations
            $insert = $conn->prepare('INSERT INTO contest_registrations (full_name, password_hash, gender, interests, level, message) VALUES (?, ?, ?, ?, ?, ?)');
            $insert->bind_param('ssssss', $full_name, $password_hash, $gender, $interests_str, $level, $message);
            
            if ($insert->execute()) {
                $success = '✅ Registration successful! Admin will review your request. Redirecting...';
                header('Refresh: 3; url=index.html');
            } else {
                $error = '❌ Registration failed: ' . $conn->error;
            }
            $conn->close();
        } catch (Exception $e) {
            $error = '❌ Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration - SGIPC</title>
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
            padding: 40px 20px;
        }

        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: 'Sora', sans-serif;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
        }

        .checkbox-item label {
            margin: 0;
            font-weight: 500;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c62828;
        }

        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2e7d32;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .checkbox-group {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>👥 Join SGIPC</h1>
        <p class="subtitle">Register as a member to participate in contests and events</p>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" required placeholder="John Doe">
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required placeholder="john@example.com">
            </div>

            <div class="form-group">
                <label for="gender">Gender *</label>
                <select id="gender" name="gender" required>
                    <option value="">-- Select Gender --</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="level">Experience Level *</label>
                <select id="level" name="level" required>
                    <option value="">-- Select Level --</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>

            <div class="form-group">
                <label>Interests *</label>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="c_cpp" name="interests" value="C/C++">
                        <label for="c_cpp">C/C++</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="python" name="interests" value="Python">
                        <label for="python">Python</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="java" name="interests" value="Java">
                        <label for="java">Java</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="algorithms" name="interests" value="Algorithms">
                        <label for="algorithms">Algorithms</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="web" name="interests" value="Web Development">
                        <label for="web">Web Dev</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="checkbox" id="db" name="interests" value="Databases">
                        <label for="db">Databases</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="message">Why do you want to join? *</label>
                <textarea id="message" name="message" required placeholder="Tell us about your goals..."></textarea>
            </div>

            <button type="submit">Submit Registration</button>
        </form>

        <div class="back-link">
            <a href="index.html">← Back to Home</a>
        </div>
    </div>
</body>
</html>
