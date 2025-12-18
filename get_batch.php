<?php
// get_batch.php
header('Content-Type: application/json');
require "db.php";

$cashierID = $_SESSION['cashier_id'] ?? 54;

// Find today's open batch for this cashier
$stmt = $conn->prepare("
    SELECT ID FROM batch 
    WHERE CashierID = ? 
      AND DATE(StartDate) = CURDATE() 
      AND Status = 1 
    LIMIT 1
");
$stmt->bind_param("i", $cashierID);
$stmt->execute();
$stmt->bind_result($batchID);

if ($stmt->fetch()) {
    echo json_encode(['batchNumber' => (int)$batchID]);
} else {
    // No open batch → create one
    $stmt2 = $conn->prepare("
        INSERT INTO batch (CashierID, StartDate, Status, Sales, Tax, CostOfGoods) 
        VALUES (?, NOW(), 1, 0, 0, 0)
    ");
    $stmt2->bind_param("i", $cashierID);
    $stmt2->execute();
    $newBatchID = $conn->insert_id;
    echo json_encode(['batchNumber' => (int)$newBatchID]);
    $stmt2->close();
}
$stmt->close();
?>