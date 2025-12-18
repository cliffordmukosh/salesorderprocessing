<?php
// save_transaction.php → FINAL BULLETPROOF VERSION
ob_start();
session_start();
require "db.php";

// ALWAYS return valid JSON – even on fatal error
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Fatal server error']);
    }
});

header('Content-Type: application/json');

if (!isset($_SESSION['cashier_id'])) {
    ob_end_clean();
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['action'])) {
    ob_end_clean();
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit;
}

try {
    if ($data['action'] === "insert_header") {
        $sql = "CALL rms_transaction_insert(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("iiiiidddssiiii",
            $data['ShipToIDX'],
            $data['StoreIDX'],
            $data['BatchNumberX'],
            $data['CustomerIDX'],
            $data['CashierIDX'],
            $data['TotalX'],
            $data['SalesTaxX'],
            $data['CommentX'],
            $data['RefenceNumberX'],
            $data['StatusX'],
            $data['ExchangeIDX'],
            $data['ChannelTypeX'],
            $data['RecallIDX'],
            $data['RecallTypeX']
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $row = $result->fetch_row();

        ob_end_clean();
        echo json_encode([
            "success" => true,
            "transactionID" => $row[0] ?? null,
            "date" => $row[1] ?? null
        ]);
        $stmt->close();
        exit;
    }

    elseif ($data['action'] === "insert_entry") {
        $sql = "CALL rms_transactionentry_insert(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Entry prepare failed: " . $conn->error);
        }

        $zero = 0;
        $stmt->bind_param("iididididiiidisidids",
            $zero,
            $data['TransactionIDX'],
            $data['CommissionX'] ?? 0,
            $data['CostX'] ?? 0,
            $data['FullPriceX'],
            $data['StoreIDX'],
            $data['ItemIDX'],
            $data['PriceX'],
            $data['PriceSourceX'] ?? 1,
            $data['QuantityX'],
            $data['SalesRepIDX'] ?? 0,
            $data['TaxableX'],
            $data['DetailedIDX'] ?? 0,
            $data['CommentX'] ?? '',
            $data['DiscountReasonCodeIDX'] ?? 0,
            $data['ReturnReasonCodeIDX'] ?? 0,
            $data['SalesTaxX'],
            $data['QuantityDiscountIDX'] ?? 0,
            $data['BatchNoX'] ?? '<none>'
        );

        if (!$stmt->execute()) {
            throw new Exception("Entry execute failed: " . $stmt->error);
        }

        ob_end_clean();
        echo json_encode(["success" => true]);
        $stmt->close();
        exit;
    }

    else {
        ob_end_clean();
        echo json_encode(["success" => false, "error" => "Unknown action"]);
        exit;
    }

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(["success" , false, "error" => $e->getMessage()]);
    exit;
}
?>