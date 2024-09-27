<?php
// Start session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve username, email, and password from the form
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Flag to check if the user already exists
    $existingUser = false;

    // Load the existing XML file or create a new one if it doesn't exist
    $xmlFilePath = "Profiles.xml";
    $xml = file_exists($xmlFilePath) ? simplexml_load_file($xmlFilePath) : new SimpleXMLElement("<profiles></profiles>");

    // Search for the user in the XML data
    foreach ($xml->profile as $profile) {
        if ((string)$profile->username === $username || (string)$profile->email === $email) {
            // If user already exists, set existingUser flag to true
            $existingUser = true;
            break;
        }
    }

    if ($existingUser) {
        // If user already exists, return failure
        echo 'failure';
        exit();
    }

    // If user is not found, add new user to the XML data
    $profile = $xml->addChild("profile");
    $profile->addChild("username", $username);
    $profile->addChild("email", $email);
    $profile->addChild("password", $password);
    
    // Add profile image element with default value
    $profile->addChild("profile_image", "profile_images/default.png");

    // Default Role Assignment
    $profile->addChild("role", "user");

    // Add achievement element with default values
    $achievement = $profile->addChild('achievement');
    $achievement->addChild('quiz1', 'incomplete');
    $achievement->addChild('quiz2', 'incomplete');
    $achievement->addChild('assessment1', 'incomplete');
    $achievement->addChild('quiz3', 'incomplete');
    $achievement->addChild('quiz4', 'incomplete');
    $achievement->addChild('assessment2', 'incomplete');

    // Add creation date
    $accountCreated = date('Y-m-d H:i:s');
    $profile->addChild("account_created", $accountCreated);

    // Save the XML file
    $xml->asXML($xmlFilePath);

    // Set session variable and return success
    $_SESSION["username"] = $username;
    echo 'success';
    exit();
}
?>
