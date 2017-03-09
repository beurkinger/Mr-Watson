<?php
require 'app/config.php';
require 'vendor/autoload.php';
use Beurkinger\MrWatson\Watson;

$watson = new Watson(USERNAME, PASSWORD);
$response = $watson->request("Hello, my name is Thibault'. I'm very happy to be with you today. I hope you are having a great time.");

http_response_code($response['statusCode']);
header('Content-Type: application/json');
echo $response['content'];
