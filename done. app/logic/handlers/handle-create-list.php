<?php
session_start();

require '../../vendor/autoload.php';
include '../../creds.php';
include '../get-lists.php';

use Aws\DynamoDb\Marshaler;

$marshaler = new Marshaler();

$user = $_POST["user"];
$listName = $_POST["name"];

if (str_contains($listName, "'") or str_contains($listName, '"'))
    header("Location: http://" . $_SERVER["HTTP_HOST"] . "/pages/main-page.php");

$lists = [];
foreach ($_SESSION["allLists"] as $allList)
    if ($allList["user"] == $user)
    {
        $lists = $allList["lists"];
        break;
    }
array_push($lists, $listName);

$listsString = "";
foreach ($lists as $list)
    $listsString .= "\"$list\",";
$listsString = substr($listsString, 0, -1);

$item = $marshaler->marshalJson('
{
    "user": "' . $user . '",
    "lists": [' . $listsString . ']
}');

$delParams = [
    'TableName' => 'Lists',
    'Key' => $marshaler->marshalJson('{"user": "' . $user . '"}')
];

$putParams = [
    'TableName' => 'Lists',
    'Item' => $item
];

$dynamodb->deleteItem($delParams);
$dynamodb->putItem($putParams);

if ($_POST["firstList"])
{
    header("Location: http://" . $_SERVER["HTTP_HOST"] . "/logic/handlers/handle-set-default.php
            /?list=" . $listName . "&user=" . $_SESSION["user"]);
    die();
}

header("Location: http://" . $_SERVER["HTTP_HOST"] . "/pages/main-page.php?list=" . $listName);
die("<br><br>If you're seeing this, something has gone wrong<br>handle-create-list.php");