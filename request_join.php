<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#join-demo');
    exit;
}

$full_name = trim((string)($_POST['full_name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$interests = $_POST['interest'] ?? [];
$message = trim((string)($_POST['message'] ?? ''));

if (!is_array($interests)) {
    $interests = [$interests];
}

$allowedInterests = [
    'cp' => 'Competitive Programming',
    'ai' => 'AI & Machine Learning',
    'web' => 'Web Development',
    'mobile' => 'Mobile Development',
    'devops' => 'DevOps & Cloud',
];

$selectedInterestKeys = array_values(array_intersect(array_keys($allowedInterests), $interests));
$interestString = implode(', ', array_map(fn($k)=>$allowedInterests[$k], $selectedInterestKeys));

$errors = [];
if ($full_name === '') $errors[] = 'Full name is required.';
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) $errors[] = 'Valid email is required.';
if ($interestString === '') $errors[] = 'Please select at least one interest.';
if ($message === '') $errors[] = 'Message is required.';

// Check if email already exists
if (empty($errors)) {
    try {
        $connection = sgipc_db_connection();
        $check_stmt = $connection->prepare('SELECT id FROM member_requests WHERE email = ? AND status = "pending"');
        $check_stmt->bind_param('s', $email);
        $check_stmt->execute();
        if ($check_stmt->get_result()->fetch_assoc()) {
            $errors[] = 'You have already submitted a request. Pending review.';
        }
        $check_stmt->close();
        $connection->close();
    } catch (Throwable $e) {
        $errors[] = 'Database error. Please try again later.';
    }
}

if (!empty($errors)) {
    $query = http_build_query(['status'=>'error','message'=>$errors[0]]);
    header('Location: request_join.php?'.$query.'#join-demo');
    exit;
}

try {
    $connection = sgipc_db_connection();
    $stmt = $connection->prepare('INSERT INTO member_requests (full_name, email, interests, message, status) VALUES (?, ?, ?, ?, "pending")');
    $stmt->bind_param('ssss', $full_name, $email, $interestString, $message);
    
    if ($stmt->execute()) {
        $stmt->close();
        $connection->close();
        $query = http_build_query(['status'=>'success','message'=>'Your request has been submitted successfully! Admins will review and get back to you soon.']);
        header('Location: request_join.php?'.$query.'#join-demo');
        exit;
    } else {
        throw new Exception('Failed to insert request');
    }
} catch (Throwable $e) {
    $query = http_build_query(['status'=>'error','message'=>'Failed to submit request. Please try again.']);
    header('Location: request_join.php?'.$query.'#join-demo');
    exit;
}
?>
