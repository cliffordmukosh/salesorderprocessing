<?php
// get_customer.php
ob_start();
header('Content-Type: application/json');
require "db.php";

$name = trim($_GET['name'] ?? '');

if ($name === '' || $name === '-') {
    echo json_encode(['id' => 0]);
    exit;
}

$stmt = $conn->prepare("
    SELECT ID 
    FROM customer 
    WHERE CONCAT(FirstName, ' ', LastName) = ? 
       OR Company = ? 
    LIMIT 1
");
$stmt->bind_param("ss", $name, $name);
$stmt->execute();
$stmt->bind_result($id);
if ($stmt->fetch()) {
    echo json_encode(['id' => (int)$id]);
} else {
    echo json_encode(['id' => 0]); // walk-in
}
$stmt->close();
ob_end_clean();
?>