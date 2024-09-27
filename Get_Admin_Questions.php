<?php
header('Content-Type: application/json');

$xml = simplexml_load_file('assessment1/questions.xml');
$questions = [];

foreach ($xml->question as $index => $question) {
    $options = [];
    foreach ($question->options->option as $option) {
        $options[] = (string) $option;
    }
    $questions[] = [
        'index' => $index,
        'text' => (string) $question->text,
        'image' => (string) $question->image,
        'options' => $options,
        'answer' => (string) $question->answer
    ];
}

echo json_encode($questions);
?>
