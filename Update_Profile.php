<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["username"])) {
    header("Location: index.html");
    exit();
}

// Load the existing XML file
$xml = simplexml_load_file("Profiles.xml");

// Find the user's profile
$username = $_SESSION["username"];
$user_profile = $xml->xpath("//profile[username='$username']")[0];

// Define variables and set to empty values
$new_username = $new_password = $new_email = "";
$usernameErr = $passwordErr = $emailErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate new username
    if (empty($_POST["new_username"])) {
        $usernameErr = "Username is required";
    } else {
        $new_username = test_input($_POST["new_username"]);
        // Check if username only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z ]*$/", $new_username)) {
            $usernameErr = "Only letters and white space allowed";
        }
    }

    // Validate new email
    if (empty($_POST["new_email"])) {
        $emailErr = "Email is required";
    } else {
        $new_email = test_input($_POST["new_email"]);
        // Check if email is well-formed
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // Validate passwords
    if (empty($_POST["old_password"])) {
        $passwordErr = "Old password is required";
    } elseif (empty($_POST["new_password"])) {
        $passwordErr = "New password is required";
    } elseif (empty($_POST["confirm_password"])) {
        $passwordErr = "Confirmation of new password is required";
    } else {
        $old_password = test_input($_POST["old_password"]);
        $new_password = test_input($_POST["new_password"]);
        $confirm_password = test_input($_POST["confirm_password"]);

        // Check if the old password is correct
        if ($old_password != $user_profile->password) {
            $passwordErr = "Old password is incorrect";
        }

        // Check if the new password and confirmation match
        if ($new_password != $confirm_password) {
            $passwordErr = "New password and confirmation do not match";
        }

        // Check if password is valid
        if (strlen($new_password) < 6) {
            $passwordErr = "Password must be at least 6 characters";
        }
    }

    // If no errors, update profile
    if ($usernameErr == "" && $passwordErr == "" && $emailErr == "") {
        // Update username, email, and password in the XML
        $user_profile->username = $new_username;
        $user_profile->email = $new_email;
        $user_profile->password = $new_password;

        // Handle profile image upload
        if ($_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['profile_image']['tmp_name'];
            $filename = $_FILES['profile_image']['name'];
            $destination = 'profile_images/' . $filename;
            move_uploaded_file($tmp_name, $destination);
            // Update profile image path in XML
            $user_profile->profile_image = $destination;
        }

        // Save changes to the XML file
        $xml->asXML("Profiles.xml");
        
        // Update session variable with new username
        $_SESSION["username"] = $new_username;

        // Return a success response
        echo json_encode(["status" => "success"]);
        exit();
    }
}

// Return an error response if validation fails
echo json_encode(["status" => "error", "usernameErr" => $usernameErr, "passwordErr" => $passwordErr, "emailErr" => $emailErr]);
exit();

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
