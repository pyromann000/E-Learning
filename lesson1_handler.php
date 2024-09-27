<?php
$xmlFile = 'lesson%201/lessonone.xml';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    $xml = simplexml_load_file($xmlFile);

    if ($action === 'load') {
        header('Content-Type: application/json');
        echo json_encode($xml);
        exit();
    }

    if ($action === 'add') {
        $title = $_POST['title'];
        $topic = $_POST['topic'];
        $description = $_POST['description'];
        $example = $_POST['example'];
        $image = $_POST['image'];
        $section = $_POST['section'];

        $lesson = $xml->addChild('lessons');
        $lesson->addChild('title', $title);
        $lesson->addChild('topic', $topic);
        $lesson->addChild('description', $description);
        $lesson->addChild('example', $example);
        $lesson->addChild('image', $image);
        $lesson->addChild('section', $section);

        $xml->asXML($xmlFile);
        echo 'Lesson added successfully';
        exit();
    }

    if ($action === 'update') {
        $index = intval($_POST['index']);
        $title = $_POST['title'];
        $topic = $_POST['topic'];
        $description = $_POST['description'];
        $example = $_POST['example'];
        $image = $_POST['image'];
        $section = $_POST['section'];

        $lesson = $xml->lessons[$index];
        $lesson->title = $title;
        $lesson->topic = $topic;
        $lesson->description = $description;
        $lesson->example = $example;
        $lesson->image = $image;
        $lesson->section = $section;

        $xml->asXML($xmlFile);
        echo 'Lesson updated successfully';
        exit();
    }

    if ($action === 'delete') {
        $index = intval($_POST['index']);
        unset($xml->lessons[$index]);

        $xml->asXML($xmlFile);
        echo 'Lesson deleted successfully';
        exit();
    }
}
?>
