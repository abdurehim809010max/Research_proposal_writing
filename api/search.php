<?php
require_once '../config.php';

header('Content-Type: application/json');

$query = sanitize($_GET['q'] ?? '');

if (strlen($query) < 2) {
    echo json_encode(['results' => []]);
    exit;
}

$searchTerm = "%{$query}%";
$stmt = $conn->prepare("SELECT mi.id, mi.name, mi.price, mi.description, c.name as category_name 
                         FROM menu_items mi 
                         JOIN categories c ON mi.category_id = c.id 
                         WHERE mi.is_available = 1 AND (mi.name LIKE ? OR mi.description LIKE ? OR c.name LIKE ?) 
                         LIMIT 10");
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'price' => number_format($row['price'], 2),
        'description' => substr($row['description'], 0, 80),
        'category' => $row['category_name']
    ];
}

echo json_encode(['results' => $results]);
$stmt->close();
?>
