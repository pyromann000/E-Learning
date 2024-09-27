<?php
session_start();

// Check if the user is authenticated and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    http_response_code(403); // Forbidden
    echo json_encode(["status" => "error", "message" => "Access denied"]);
    exit();
}

// Get the raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Validate input data
if (!isset($data['originalUsername'], $data['username'], $data['email'], $data['role'], $data['password'])) {
    http_response_code(400); // Bad request
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit();
}

// Load the existing XML file
$xml = simplexml_load_file("Profiles.xml");

// Find the user by original username and update their details
$userFound = false;
foreach ($xml->profile as $profile) {
    if ((string)$profile->username === $data['originalUsername']) {
        $profile->username = $data['username'];
        $profile->email = $data['email'];
        $profile->role = $data['role'];
        $profile->password = $data['password'];
        $userFound = true;
        break;
    }
}

// Save the updated XML file if user is found
if ($userFound) {
    $xml->asXML("Profiles.xml");
    echo json_encode(["status" => "success"]);
} else {
    http_response_code(404); // Not Found
    echo json_encode(["status" => "error", "message" => "User not found"]);
}
?>
