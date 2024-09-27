<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
    exit;
}

$index = $data['index'] ?? null;
$text = $data['text'] ?? '';
$image = $data['image'] ?? '';
$options = $data['options'] ?? [];
$answer = $data['answer'] ?? '';

$xmlFilePath = 'assessment1/questions.xml';

if (!file_exists($xmlFilePath)) {
    echo json_encode(['status' => 'error', 'message' => 'XML file not found']);
    exit;
}

$xml = simplexml_load_file($xmlFilePath);
if ($xml === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to load XML file']);
    exit;
}

$question = $xml->question[intval($index)];

if ($question) {
    $question->text = htmlspecialchars($text);
    $question->image = htmlspecialchars($image);
    $question->options = ''; // Clear existing options
    foreach ($options as $option) {
        $question->options->addChild('option', htmlspecialchars($option));
    }
    $question->answer = htmlspecialchars($answer);
    
    if ($xml->asXML($xmlFilePath) === false) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save XML file']);
        exit;
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Question not found']);
}
?>
