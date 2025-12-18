<?php
// get_batch.php → 100% WORKING ON REAL RMS (NO MORE EMPTY RESPONSE)
ob_start();

header('Content-Type: application/json');
require "db.php";

// === CONFIGURATION ===
$registerID = 1;   // Change only if you have multiple registers
$storeID    = 1;   // Almost always 1

try {
    // 1. Look for today's open batch
    $sql = "SELECT ID FROM batch 
            WHERE RegisterID = ? 
              AND StoreID = ? 
              AND DATE(OpeningTime) = CURDATE() 
              AND Status = 1 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $registerID, $storeID);
    $stmt->execute();
    $stmt->bind_result($batchID);

    if ($stmt->fetch()) {
        // Found open batch
        ob_end_clean();
        echo json_encode(['batchNumber' => (int)$batchID]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    // 2. No open batch → create one
    $insert = "INSERT INTO batch 
        (RegisterID, StoreID, OpeningTime, Status, Sales, Tax, CostOfGoods, CustomerCount) 
        VALUES (?, ?, NOW(), 1, 0, 0, 0, 0)";

    $stmt2 = $conn->prepare($insert);
    if (!$stmt2) {
        throw new Exception("Insert prepare failed: " . $conn->error);
    }

    $stmt2->bind_param("ii", $registerID, $storeID);
    if (!$stmt2->execute()) {
        throw new Exception("Insert failed: " . $stmt2->error);
    }

    $newBatchID = $conn->insert_id;
    $stmt2->close();

    ob_end_clean();
    echo json_encode(['batchNumber' => (int)$newBatchID]);
    exit;

} catch (Exception $e) {
    // Even if everything explodes → we still return valid JSON
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'batchNumber' => 0,
        'error' => $e->getMessage()
    ]);
    exit;
}
?>