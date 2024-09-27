<?php
session_start();

// Load XML file
$xml = simplexml_load_file('profiles.xml');

// Check if completion level is posted
if (isset($_POST['completed_level'])) {
    // Update XML with level
    $level = $_POST['completed_level'];

    // Find the profile node corresponding to the current user
    $profile = $xml->xpath("//profile[username='{$_SESSION['username']}']")[0];

    // Check if the profile node is found
    if ($profile) {
        // Update completion status to "completed" for the specified level
        $profile->achievement->$level = 'completed';

        // Save changes back to XML file
        $xml->asXML('profiles.xml');

        // Send success response
        echo 'Completion status for ' . $level . ' updated successfully.';
    } else {
        // Send error response if profile node is not found
        http_response_code(404); // Not Found
        echo 'Error: Profile not found.';
    }
} else {
    // Send error response if completion level is not provided
    http_response_code(400); // Bad Request
    echo 'Error: Completion level not provided.';
}
?>
