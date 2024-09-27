<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$text = $data['text'];
$image = $data['image'];
$options = $data['options'];
$answer = $data['answer'];

$xml = simplexml_load_file('http://localhost/WST%20updated/assessment1/questions.xml');
$newQuestion = $xml->addChild('question');
$newQuestion->addChild('text', $text);
$newQuestion->addChild('image', $image);
$optionsNode = $newQuestion->addChild('options');
foreach ($options as $option) {
    $optionsNode->addChild('option', $option);
}
$newQuestion->addChild('answer', $answer);

$xml->asXML('assessment1/questions.xml');
echo json_encode(['status' => 'success']);
?>
