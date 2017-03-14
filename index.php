<?php
require 'app/config.php';
require 'vendor/autoload.php';
use Beurkinger\MrWatson\Watson;

header('Content-Type: application/json');

if (!isset($_GET["text"]) && !$_POST['text']) {
  http_response_code(400);
  echo json_encode(['error' => 'text is not defined']);
}

$watson = new Watson(USERNAME, PASSWORD);

$text = htmlspecialchars(trim($_GET['text']));
if (isset($_GET["sentences"])) {
  $bool = trim($_GET["sentences"]) === 'false' ? false : true;
  $watson->setSentences($bool);
}
if (isset($_GET["tones"])) $watson->setToneFilter(trim((string) $_GET["tones"]));

$response = $watson->request($text);
http_response_code($response['statusCode']);
echo $response['content'];
