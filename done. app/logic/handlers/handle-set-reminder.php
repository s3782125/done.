<?php

require '../../vendor/autoload.php';
include "../../creds.php";

use Aws\Sdk;

$sdk = new Sdk([
    'endpoint' => 'https://dynamodb./* region */.amazonaws.com',
    'region' => /* region */,
    'version' => 'latest',
    'credentials' => $creds
]);
$dynamodb = $sdk->createDynamoDb();

$time = $_POST["time"];
$id = $_POST["id"];
$email = $_POST["email"];

$item = [
    "time" => ["S" => $time],
    "id" => ["N" => $id],
    "email" => ["S" => $email],
    "done" => ["BOOL" => false]
];

$dynamodb->putItem([
    "Item" => $item,
    "TableName" => "Reminders"
]);

header("Location: http://" . $_SERVER["HTTP_HOST"] . "/pages/main-page.php");
die("<br><br>If you're seeing this, something has gone wrong<br>handle-set-reminder.php");