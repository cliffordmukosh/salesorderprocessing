<?php
// get_item.php

header('Content-Type: application/json');
require "db.php";

$code = trim($_GET['code'] ?? '');

if ($code === '') {
    echo json_encode(['id' => null]);
    exit;
}

$stmt = $conn->prepare("SELECT ID FROM item WHERE ItemLookupCode = ? AND Inactive = 0 LIMIT 1");
$stmt->bind_param("s", $code);
$stmt->execute();
$stmt->bind_result($id);
if ($stmt->fetch()) {
    echo json_encode(['id' => (int)$id]);
} else {
    echo json_encode(['id' => null]);
}
$stmt->close();

?>