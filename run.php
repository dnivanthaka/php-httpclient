<?php
require __DIR__ . '/vendor/autoload.php';

use Dinusha\Http\HttpClient;
use Dinusha\Http\HttpResponse;

$client = new HttpClient();
// Get token
$client->withUri('https://www.xxxxxxx.com/assessment-endpoint.php');
$client->withHeader('Accept', 'application/json');
$client->withMethod('OPTIONS');
$response = $client->makeRequest();
$token = $response->getResponseArray();

// Make request
$client->withUri('https://www.xxxxx.com/assessment-endpoint.php');
$client->withHeader('Content-Type', 'application/json');
$client->withHeader('Authorization', 'Bearer '.$token[0]);
$client->withParams(['name' => 'Dinusha Amerasinghe', 'email' => 'nivanthaka@gmail.com', 'url' => 'https://github.com/dnivanthaka/php-httpclient.git']);
$client->withMethod('POST');
$response = $client->makeRequest();