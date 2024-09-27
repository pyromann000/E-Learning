<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["username"])) {
    // Return an error response if the user is not authenticated
    http_response_code(401); // Unauthorized
    exit();
}

// Retrieve username from session
$username = $_SESSION["username"];

// Load the existing XML file
$xml = simplexml_load_file("profiles.xml");

// Find the user's profile
$user_profile = $xml->xpath("//profile[username='$username']")[0];

// Prepare profile data
$profileData = [
    'username' => (string) $user_profile->username,
    'password' => (string) $user_profile->password,
    'email' => (string) $user_profile->email,
    'profile_image' => isset($user_profile->profile_image) ? (string) $user_profile->profile_image : '', // Check if profile image exists
    'role' => isset($user_profile->role) ? (string) $user_profile->role : 'user', // Check if role exists, default to 'user'
    'completion_counts' => []
];

// Extract completion counts
$completion_counts = [];
foreach ($user_profile->achievement->children() as $level => $status) {
    $completion_counts[$level] = (string) $status;
}
$profileData['completion_counts'] = $completion_counts;

// Return profile data
echo json_encode($profileData);
?>
