<?php
// save_message.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cartrackingapp";

// reCAPTCHA secret key
$recaptcha_secret = "6LcmDesrAAAAANamW-7doOpe_3jlsyAopqp5xrNP";

// Check reCAPTCHA
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
if (!$recaptcha_response) {
    echo json_encode(['status' => 'error', 'message' => 'Veuillez valider le reCAPTCHA.']);
    exit;
}

// Verify with Google
$verify_url = "https://www.google.com/recaptcha/api/siteverify";
$response = file_get_contents($verify_url . "?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response);
$responseData = json_decode($response);

if (!$responseData->success) {
    echo json_encode(['status' => 'error', 'message' => 'Échec de la validation reCAPTCHA.']);
    exit;
}

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data safely
$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$message = $_POST['message'] ?? '';

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO contact_messages (nom, prenom, email, phone, message) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nom, $prenom, $email, $phone, $message);

// Execute and respond
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'enregistrement.']);
}

$stmt->close();
$conn->close();
?>
