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
    'ai' => 'AI',
    'web' => 'Web Development',
];

$selectedInterestKeys = array_values(array_intersect(array_keys($allowedInterests), $interests));
$interestString = implode(', ', array_map(fn($k)=>$allowedInterests[$k], $selectedInterestKeys));

$errors = [];
if ($full_name === '') $errors[] = 'Full name required.';
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) $errors[] = 'Valid email required.';
if ($interestString === '') $errors[] = 'Select at least one interest.';
if ($message === '') $errors[] = 'Message required.';

if (!empty($errors)) {
    $query = http_build_query(['status'=>'error','message'=>$errors[0]]);
    header('Location: request_join.php?'.$query.'#join-demo');
    exit;
}

try {
    $connection = sgipc_db_connection();
    $stmt = $connection->prepare('INSERT INTO member_requests (full_name, email, interests, message) VALUES (?,?,?,?)');
    $stmt->bind_param('ssss', $full_name, $email, $interestString, $message);
    $stmt->execute();
    $stmt->close();
    $connection->close();
    $query = http_build_query(['status'=>'success','message'=>'Request submitted successfully.']);
    header('Location: request_join.php?'.$query.'#join-demo');
    exit;
} catch (Throwable $e) {
    $query = http_build_query(['status'=>'error','message'=>'Database error.']);
    header('Location: request_join.php?'.$query.'#join-demo');
    exit;
}
?>
