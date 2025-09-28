<?php
require __DIR__ . '/db.php';

$sql = "SELECT ID, ItemLookupCode, ItemType, Description, quantity AS AvailableQty, Price 
        FROM item 
        ORDER BY Description ASC 
        LIMIT 100"; // limit for performance

$result = $conn->query($sql);

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

header('Content-Type: application/json');
echo json_encode($items);
?>
