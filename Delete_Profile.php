<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["username"])) {
    echo json_encode(["status" => "error", "message" => "User not authenticated"]);
    exit();
}

// Load the existing XML file
$xml = simplexml_load_file("Profiles.xml");

// Find the user's profile
$username = $_SESSION["username"];
$user_profile = $xml->xpath("//profile[username='$username']")[0];

if ($user_profile) {
    // Remove the user's profile from the XML
    $dom = dom_import_simplexml($user_profile);
    $dom->parentNode->removeChild($dom);

    // Save changes to the XML file
    $xml->asXML("Profiles.xml");

    // Destroy the session
    session_destroy();

    // Return a success response
    echo json_encode(["status" => "success"]);
    exit();
} else {
    // Return an error response if profile not found
    echo json_encode(["status" => "error", "message" => "Profile not found"]);
    exit();
}
?>
