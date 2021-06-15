<?php
session_start();

require '../vendor/autoload.php';
include "../creds.php";

use Aws\Sdk;

$sdk = new Sdk([
    'endpoint' => 'https://dynamodb./* region */.amazonaws.com',
    'region' => /* region */,
    'version' => 'latest',
    'credentials' => $creds
]);
$dynamodb = $sdk->createDynamoDb();

$result = $dynamodb->scan(["TableName" => "Reminders"]);

$reminders = [];
foreach ($result["Items"] as $item)
{
    $array = [];
    $array["id"] = $item["id"]["N"];
    $array["done"] = $item["done"]["BOOL"];
    $array["email"] = $item["email"]["S"];
    $array["time"] = $item["time"]["S"];

    array_push($reminders, $array);
}

$_SESSION["reminders"] = $reminders;