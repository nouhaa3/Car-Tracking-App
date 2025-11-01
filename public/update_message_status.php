<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, PUT, OPTIONS");
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
$is_read = isset($data['is_read']) ? intval($data['is_read']) : 1;

$stmt = $conn->prepare("UPDATE contact_messages SET is_read = ? WHERE id = ?");
$stmt->bind_param("ii", $is_read, $id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Message status updated successfully"
    ]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to update message status"]);
}

$stmt->close();
$conn->close();
