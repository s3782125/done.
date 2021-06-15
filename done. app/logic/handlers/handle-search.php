<?php
session_start();

include '../../vendor/autoload.php';
include '../../creds.php';

use Aws\Sdk;

$sdk = new Sdk([
    'endpoint' => /* CloudSearch address */,
    'region' => /* region */,
    'version' => 'latest',
    'credentials' => $creds
]);
$csd = $sdk->createCloudSearchDomain();

$text = $_POST["text"];

$result = $csd->search([
    'query' => $text,
    'queryOptions' => '{"fields":["text"]}'
]);

$items = [];
foreach ($result["hits"]["hit"] as $hit)
{
    $item = [];
    foreach ($hit["fields"] as $name => $fields)
    {
        $item[$name] = $fields[0];
    }
    array_push($items, $item);
}

$_SESSION["search_results"] = $items;

header("Location: http://" . $_SERVER["HTTP_HOST"] . $_POST["source"]);
die("<br><br>If you're seeing this, something has gone wrong<br>handle-search.php");