<?php
session_start();

include '../vendor/autoload.php';
include '../creds.php';

use Aws\Sdk;

$sdk = new Sdk([
    'endpoint' => 'https://dynamodb./* region */.amazonaws.com',
    'region' => /* region */,
    'version' => 'latest',
    'credentials' => $creds
]);

$dynamodb = $sdk->createDynamoDb();
$result = $dynamodb->scan(["TableName" => "Lists"]);

$listArray = [];
foreach ($result["Items"] as $item)
{
    $array = [];
    $lists = [];

    foreach ($item["lists"]["L"] as $arr)
    {
        foreach ($arr as $listItem)
            array_push($lists, $listItem);
    }

    $array["user"] = $item["user"]["S"];
    $array["lists"] = $lists;

    array_push($listArray, $array);
}

$_SESSION["allLists"] = $listArray;