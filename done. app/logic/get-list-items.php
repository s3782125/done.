<?php

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

$result = $dynamodb->scan(["TableName" => "ListItems"]);

$allListItems = [];
$nextId = 0;

foreach ($result["Items"] as $item)
{
    $array = [];
    $array["user"] = $item["user"]["S"];
    $array["text"] = $item["text"]["S"];
    $array["id"] = $item["id"]["N"];
    $array["listName"] = $item["list"]["S"];
    $array["done"] = $item["done"]["BOOL"];
    if (array_key_exists("image", $item))
        $array["image"] = $item["image"]["S"];
    else
        $array["image"] = "none";

    array_push($allListItems, $array);
    $nextId = $array["id"];
}

$_SESSION["nextId"] = $nextId + 1;
$_SESSION["allListItems"] = $allListItems;