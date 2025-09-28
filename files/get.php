<?php
require __DIR__ . '/db.php';

/**
 * Get all columns of a table with type info.
 *
 * @param mysqli $conn
 * @param string $table
 * @return array
 */
function getTableFields($conn, $table) {
    $fields = [];
    $sql = "DESCRIBE `$table`"; // or: SHOW COLUMNS FROM `$table`
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $fields[] = $row; // contains Field, Type, Null, Key, Default, Extra
        }
        $result->free();
    } else {
        die("Error describing $table: " . $conn->error);
    }
    return $fields;
}

// Example usage
$tableName = "customer";  // change this to any table
$columns = getTableFields($conn, $tableName);

echo "<h3>Table: $tableName</h3>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

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
echo "</table>";

$conn->close();
?>
