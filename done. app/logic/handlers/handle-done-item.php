<?php
session_start();

require '../../vendor/autoload.php';
include '../../creds.php';

use Aws\DynamoDb\Marshaler;
use Aws\Sdk;

$sdk = new Sdk([
    'endpoint' => 'https://dynamodb./* region */.amazonaws.com',
    'region' => /* region */,
    'version' => 'latest',
    'credentials' => $creds
]);
$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();

$user = $_SESSION["username"];
$id = (int)$_POST["id"];

$key = $marshaler->marshalJson('
    {
        "user": "' . $user . '",
        "id": ' . $id . '
    }');

$params = [
    'TableName' => 'ListItems',
    'Key' => $key,
    'UpdateExpression' => 'set done = :d, doneInt = :i',
    'ExpressionAttributeValues' => $marshaler->marshalJson('{ ":d": true, ":i": "1" }'),
    'ReturnValues' => 'ALL_NEW'
];

$result = $dynamodb->updateItem($params);
print_r($result);

$sdk = new Sdk([
    'endpoint' => /* CloudSearch address */,
    'region' => /* region */,
    'version' => 'latest',
    'credentials' => $creds
]);
$csd = $sdk->createCloudSearchDomain();

$text = $_POST["text"];
$listName = $_POST["listName"];

$text = addslashes($text);
$document = "
[{
    \"type\": \"add\",
    \"id\": \"$id\",
    \"fields\": 
    {
       \"text\": \"$text\",
       \"list\": \"$listName\",
       \"user\": \"$user\",
       \"doneint\": \"1\",
       \"id\": \"$id\"
    }
}]";

$csd->uploadDocuments([
    'contentType' => 'application/json',
    'documents' => $document
]);

header("Location: http://" . $_SERVER["HTTP_HOST"] . "/pages/main-page.php?list=" . $listName);
die("<br><br>If you're seeing this, something has gone wrong<br>handle-done-item.php");
