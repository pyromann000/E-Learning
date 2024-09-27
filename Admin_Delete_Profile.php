<?php
session_start();

// Check if the user is authenticated and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "admin") {
    echo json_encode(["status" => "error", "message" => "Access denied"]);
    exit();
}

// Get the username from the request body
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];

// Load the existing XML file
$xml = simplexml_load_file("Profiles.xml");

// Find the user's profile
$user_profile = $xml->xpath("//profile[username='$username']")[0];

if ($user_profile) {
    // Remove the user's profile from the XML
    $dom = dom_import_simplexml($user_profile);
    $dom->parentNode->removeChild($dom);

    // Save changes to the XML file
    $xml->asXML("Profiles.xml");

    // Return a success response
    echo json_encode(["status" => "success"]);
    exit();
} else {
    // Return an error response if profile not found
    echo json_encode(["status" => "error", "message" => "Profile not found"]);
    exit();
}
?>
