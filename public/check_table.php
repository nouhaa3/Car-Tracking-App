<?php
$conn = new mysqli('localhost', 'root', '', 'cartrackingapp');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Table structure for contact_messages:\n\n";
$result = $conn->query('DESCRIBE contact_messages');
while($row = $result->fetch_assoc()) {
    echo "Field: " . $row['Field'] . " | Type: " . $row['Type'] . " | Default: " . ($row['Default'] ?? 'NULL') . "\n";
}

echo "\n\nSample data:\n\n";
$result = $conn->query('SELECT * FROM contact_messages LIMIT 1');
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    print_r($row);
}

$conn->close();
