<?php
require __DIR__ . '/../db.php';

// List of tables
$tables = [

    'orderentry',
    'payment',
    'orders',
    'tax',
    'category',
     'item',
     'itempicture',
    'cashier',
    'store',
    "accountreceivable",
    "accountreceivablehistory",
    "banks",
    "batch",
    "cashier",
    "customer",
    "inventorylocation",
    "inventorylocationitems",
    "item",
    "itemmovement",
    "orderentry",
    "orderhistory",
    "orders",
    "payment",
    "register",
    "store",
    "taxentry",
    "taxtotals",
    "tax",
    "tender",
    "tenderentry",
    "tendertotals",
    "transaction",
    "transactionentry"
];

foreach ($tables as $table) {
    echo "<h3>Table: $table</h3>";

    $sql = "SELECT * FROM `$table` LIMIT 2"; 
    if ($result = $conn->query($sql)) {
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            
            // Print header
            $fields = $result->fetch_fields();
            echo "<tr>";
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";

            // Print rows
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $col) {
                    echo "<td>" . htmlspecialchars($col) . "</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "No data.<br>";
        }
        $result->free();
    } else {
        echo "Error querying $table: " . $conn->error . "<br>";
    }
}

$conn->close();
?>
