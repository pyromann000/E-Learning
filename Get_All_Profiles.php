<?php
session_start();

// Check if the user is authenticated and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    http_response_code(403); // Forbidden
    echo json_encode(["status" => "error", "message" => "Access denied"]);
    exit();
}

// Load the existing XML file
$xml = simplexml_load_file("Profiles.xml");

// Prepare profile data
$profiles = [];
foreach ($xml->profile as $profile) {
    $achievements = [];
    foreach ($profile->achievement->children() as $achievement) {
        $achievements[$achievement->getName()] = (string) $achievement;
    }
    $profiles[] = [
        'username' => (string) $profile->username,
        'email' => (string) $profile->email,
        'role' => (string) $profile->role,
        'password' => (string) $profile->password,
        'achievements' => $achievements,
        'account_created' => (string) $profile->account_created
    ];
}

// Return profile data
echo json_encode($profiles);
?>
