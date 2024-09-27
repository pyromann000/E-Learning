<?php
header('Content-Type: application/json');

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$activityType = isset($_GET['activityType']) ? $_GET['activityType'] : 'all';
$startDate = isset($_GET['startDate']) ? strtotime($_GET['startDate']) : null;
$endDate = isset($_GET['endDate']) ? strtotime($_GET['endDate']) : null;

$xml = simplexml_load_file("Profiles.xml");
$activityLogs = [];

$currentTime = time(); // Get the current time once before the loop

foreach ($xml->profile as $profile) {
    if (isset($profile->activity_log)) {
        foreach ($profile->activity_log->activity as $activity) {
            $timestamp = strtotime((string)$activity->timestamp);
            $include = false;

            // Calculate the time range based on the selected filter
            switch ($filter) {
                case 'day':
                    $timeRange = 86400; // 24 hours
                    break;
                case 'week':
                    $timeRange = 604800; // 7 days
                    break;
                case 'month':
                    $timeRange = 2592000; // 30 days
                    break;
                case 'year':
                    $timeRange = 31536000; // 365 days
                    break;
                default:
                    // For 'all' filter, include all activity logs
                    $include = true;
                    break;
            }

            // Check if the activity log falls within the specified time range
            if ($filter !== 'all') {
                $include = ($currentTime - $timestamp) <= $timeRange;
            }

            // Further filter by activity type if specified
            if ($activityType !== 'all' && (string)$activity->type !== $activityType) {
                $include = false;
            }

            // Further filter by start and end dates if specified
            if ($startDate && $timestamp < $startDate) {
                $include = false;
            }
            if ($endDate && $timestamp > $endDate) {
                $include = false;
            }

            // If the activity log passes all filters, add it to the list
            if ($include) {
                $activityLogs[] = [
                    'username' => (string)$profile->username,
                    'activity_type' => (string)$activity->type,
                    'timestamp' => (string)$activity->timestamp
                ];
            }
        }
    }
}

echo json_encode($activityLogs);
?>
