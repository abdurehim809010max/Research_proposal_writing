<?php
require_once '../config.php';

header('Content-Type: application/json');

$email = sanitize($_GET['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['available' => false, 'message' => 'Invalid email format.']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$exists = $stmt->get_result()->num_rows > 0;
$stmt->close();

echo json_encode([
    'available' => !$exists,
    'message' => $exists ? 'Email is already registered.' : 'Email is available.'
]);
?>
