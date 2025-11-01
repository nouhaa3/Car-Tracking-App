<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cartrackingapp"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Database connection failed"]);
  exit();
}

$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);

$messages = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
  }
}

echo json_encode($messages);
$conn->close();
