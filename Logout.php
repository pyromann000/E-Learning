<?php
session_start();

if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];

    // Load the existing XML file
    $xml = simplexml_load_file("Profiles.xml");

    foreach ($xml->profile as $profile) {
        // Find the logged-in user in the XML file
        if ((string)$profile->username === $username) {
            // Log the logout activity
            if (!$profile->activity_log) {
                $profile->addChild('activity_log');
            }
            $activityLog = $profile->activity_log;
            $activity = $activityLog->addChild('activity');
            $activity->addChild('type', 'logout');
            $activity->addChild('timestamp', date('Y-m-d H:i:s'));

            // Save the updated XML
            $xml->asXML("Profiles.xml");
            break;
        }
    }
}

// Unset all session variables
session_unset();
// Destroy the session
session_destroy();
// Redirect to login page
header("Location: index.html");
exit();
?>
