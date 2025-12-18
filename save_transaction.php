<?php
// save_transaction.php
session_start();
require "db.php";
header('Content-Type: application/json');

if (!isset($_SESSION['cashier_id'])) {
    echo json_encode(["success" => false, "error" => "Not logged in"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['action'])) {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit;
}

try {
    if ($data['action'] === "insert_header") {
        // Call your exact stored procedure
        $sql = "CALL rms_transaction_insert(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
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
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_row(); // returns [TransactionID, DateTime]
        echo json_encode([
            "success" => true,
            "transactionID" => $row[0] ?? null,
            "date" => $row[1] ?? null
        ]);
        $stmt->close();
    }

    elseif ($data['action'] === "insert_entry") {
        $sql = "CALL rms_transactionentry_insert(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iididididiiidisidids",
            $zero = 0, // IDX (auto increment)
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
        $stmt->execute();
        echo json_encode(["success" => true]);
        $stmt->close();
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>