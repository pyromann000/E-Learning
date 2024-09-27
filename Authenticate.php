<?php
session_start();
$loginSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve identifier (username or email) and password from the form
    $identifier = $_POST["identifier"];
    $password = $_POST["password"];

    // Load the existing XML file or create a new one if it doesn't exist
    $xml = file_exists("Profiles.xml") ? simplexml_load_file("Profiles.xml") : new SimpleXMLElement("<profiles></profiles>");

    foreach ($xml->profile as $profile) {
        // Check if the identifier matches either username or email, and then validate the password
        if (((string)$profile->username === $identifier || (string)$profile->email === $identifier) && (string)$profile->password === $password) {
            $_SESSION["username"] = (string)$profile->username;
            $_SESSION["email"] = (string)$profile->email;
            $_SESSION["role"] = (string)$profile->role;  // Store the role in the session

            // Log the login activity
            if (!$profile->activity_log) {
                $profile->addChild('activity_log');
            }
            $activityLog = $profile->activity_log;
            $activity = $activityLog->addChild('activity');
            $activity->addChild('type', 'login');
            $activity->addChild('timestamp', date('Y-m-d H:i:s'));

            // Save the updated XML
            $xml->asXML("Profiles.xml");

            $loginSuccess = true;
            break;
        }
    }
    
    if (!$loginSuccess) {
        // Provide more informative message for authentication failure
        echo json_encode(['status' => 'failure', 'message' => 'Incorrect username or password']);
    } else {
        echo json_encode(['status' => 'success', 'redirect' => 'home.html']);
    }
}
?>
