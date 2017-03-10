<?php
require 'app/config.php';
require 'vendor/autoload.php';
use Beurkinger\MrWatson\Watson;

header('Content-Type: application/json');

if (isset($_GET["text"]) && $_GET['text']) {
    $text = htmlspecialchars(trim($_GET['text']));
    $watson = new Watson(USERNAME, PASSWORD);
    $response = $watson->request($text);
    http_response_code($response['statusCode']);
    echo $response['content'];
} else {
    http_response_code(400);
    echo json_encode(['error' => 'text query is not defined']);
}
