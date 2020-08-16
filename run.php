<?php
require __DIR__ . '/vendor/autoload.php';

use Dinusha\Http\HttpClient;
use Dinusha\Http\HttpResponse;

$client = new HttpClient();
$client->withUri('https://www.coredna.com/assessment-endpoint.php');
$client->withHeader('Accept', 'application/json');
$client->withParams(['name' => 'Dinusha Amerasinghe', 'email' => 'test@test.com', 'url' => 'test.com']);
$client->withMethod('OPTIONS');
$response = $client->makeRequest();
print_r($response->getResponseArray());
echo $client;
//$client->with