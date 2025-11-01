<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Message ID is required"]);
    exit();
}

$id = intval($data['id']);

$stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Message deleted successfully"
    ]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to delete message"]);
}

$stmt->close();
$conn->close();
