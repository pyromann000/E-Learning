<?php
header('Content-Type: application/json');

// Load the XML file
$xmlFile = 'questions.xml';
if (!file_exists($xmlFile)) {
    echo json_encode(['error' => 'XML file not found.']);
    exit;
}

$xml = simplexml_load_file($xmlFile);
if ($xml === false) {
    echo json_encode(['error' => 'Failed to load XML file.']);
    exit;
}

$questions = [];

// Parse the XML file into an array
foreach ($xml->question as $question) {
    $options = [];
    foreach ($question->options->option as $option) {
        $options[] = (string)$option;
    }
    $questions[] = [
        'text' => (string)$question->text,
        'options' => $options,
        'answer' => (string)$question->answer,
        'image' => isset($question->image) ? (string)$question->image : null
    ];
}

// Shuffle and select a subset of questions
shuffle($questions);
$selectedQuestions = array_slice($questions, 0, 10);

// Output the selected questions as JSON
echo json_encode($selectedQuestions);
