<?php
require __DIR__ . '/../db.php';

/**
 * Get all columns of a table with type info.
 *
 * @param mysqli $conn
 * @param string $table
 * @return array
 */
function getTableFields($conn, $table) {
    $fields = [];
    $sql = "DESCRIBE `$table`";

    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $fields[] = $row;
        }
        $result->free();
    } else {
        echo "<p style='color:red;'>Error describing $table: {$conn->error}</p>";
    }

    return $fields;
}

/* 👉 Tables you want to inspect */
$tables = [
    'supplier',
    'reasoncode',
    'mobileregister',
    'batch',
    'droppayout',
    'payoutdetails'
];


foreach ($tables as $tableName) {

    $columns = getTableFields($conn, $tableName);

    echo "<h3>Table: {$tableName}</h3>";

    if (empty($columns)) {
        echo "<p>No columns found.</p>";
        continue;
    }

    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>
            <th>Field</th>
            <th>Type</th>
            <th>Null</th>
            <th>Key</th>
            <th>Default</th>
            <th>Extra</th>
          </tr>";

    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }

    echo "</table><br>";
}

$conn->close();
?>
