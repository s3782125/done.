<?php
session_start();

require '../../vendor/autoload.php';
include '../../creds.php';

use Aws\S3\S3Client;
use Aws\Sdk;

$sdk = new Sdk([
    'endpoint' => 'https://dynamodb./* region */.amazonaws.com',
    'region' => /* region */,
    'version' => 'latest',
    'credentials' => $creds
]);
$dynamodb = $sdk->createDynamoDb();

$id = $_POST["id"];
$username = $_SESSION["username"];
$listName = $_POST["listName"];

$item = [
    "done" => ["BOOL" => false],
    "doneInt" => ["N" => "0"],
    "id" => ["N" => $id],
    "user" => ["S" => $username],
    "list" => ["S" => $listName]
];

$text = trim($_POST["text"]);
if ($text !== null and $text !== "")
{
    $item["text"] = ["S" => $text];
}

$tmp_name = $_FILES["image"]["tmp_name"];
echo $tmp_name;
if ($tmp_name !== null and $tmp_name !== "")
{
    $s3 = new S3Client([
        'region' => /* region */,
        'version' => 'latest',
        'credentials' => $creds
    ]);

    $image = file_get_contents($tmp_name);
    $imageHash = hash("md5", $image);

    $result = $s3->putObject([
        'Bucket' => /* Bucket name */,
        'Key' => $imageHash . '.png',
        'Body' => $image,
        'ACL' => 'public-read'
    ]);

    $item["image"] = ["S" => $result['ObjectURL']];
}

$dynamodb->putItem([
    "Item" => $item,
    "TableName" => "ListItems"
]);


$sdk = new Sdk([
    'endpoint' => /* CloudSearch address */,
    'region' => /* region */,
    'version' => 'latest',
    'credentials' => $creds
]);
$csd = $sdk->createCloudSearchDomain();

$text = addslashes($text);
$document = "
[{
    \"type\": \"add\",
    \"id\": \"$id\",
    \"fields\": 
    {
       \"text\": \"$text\",
       \"list\": \"$listName\",
       \"user\": \"$username\",
       \"doneint\": \"0\",
       \"id\": \"$id\"
    }
}]";

$csd->uploadDocuments([
    'contentType' => 'application/json',
    'documents' => $document
]);


header("Location: http://" . $_SERVER["HTTP_HOST"] . "/pages/main-page.php?list=" . $_POST["listName"]);
die("<br><br>If you're seeing this, something has gone wrong<br>handle-add-to-list.php");

?>