<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#form-demo');
    exit;
}

$fullName = trim((string) ($_POST['fullName'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$gender = trim((string) ($_POST['gender'] ?? ''));
$interests = $_POST['interest'] ?? [];
$level = trim((string) ($_POST['level'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if (!is_array($interests)) {
    $interests = [$interests];
}

$allowedGenders = ['male', 'female', 'other'];
$allowedLevels = ['Beginner', 'Intermediate', 'Advanced'];
$allowedInterests = [
    'cp' => 'Competitive Programming',
    'ai' => 'AI',
    'web' => 'Web Development',
];

$errors = [];

if ($fullName === '') {
    $errors[] = 'Full name is required.';
}
if (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}
if (!in_array($gender, $allowedGenders, true)) {
    $errors[] = 'Select a valid gender.';
}
if ($level === '' || !in_array($level, $allowedLevels, true)) {
    $errors[] = 'Select a valid current level.';
}
if ($message === '') {
    $errors[] = 'Message is required.';
}

$selectedInterestKeys = array_values(array_intersect(array_keys($allowedInterests), $interests));
if ($selectedInterestKeys === []) {
    $errors[] = 'Select at least one interest.';
}

if ($errors !== []) {
    $query = http_build_query([
        'status' => 'error',
        'message' => $errors[0],
    ]);
    header('Location: index.php?' . $query . '#form-demo');
    exit;
}

$interestLabels = array_map(static fn (string $key): string => $allowedInterests[$key], $selectedInterestKeys);
$interestString = implode(', ', $interestLabels);
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $connection = sgipc_db_connection();
    $statement = $connection->prepare(
        'INSERT INTO contest_registrations (full_name, password_hash, gender, interests, level, message) VALUES (?, ?, ?, ?, ?, ?)'
    );

    if (!$statement) {
        throw new RuntimeException('Unable to prepare registration insert statement.');
    }

    $statement->bind_param(
        'ssssss',
        $fullName,
        $passwordHash,
        $gender,
        $interestString,
        $level,
        $message
    );

    if (!$statement->execute()) {
        throw new RuntimeException('Unable to save registration data.');
    }

    $statement->close();
    $connection->close();

    $query = http_build_query([
        'status' => 'success',
        'message' => 'Registration saved to MySQL successfully.',
    ]);
    header('Location: index.php?' . $query . '#form-demo');
    exit;
} catch (Throwable $exception) {
    $query = http_build_query([
        'status' => 'error',
        'message' => 'Database save failed. Import the schema in XAMPP first.',
    ]);
    header('Location: index.php?' . $query . '#form-demo');
    exit;
}
