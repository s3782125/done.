<?php
session_start();

$db = new mysqli(
    /* RDS address */,
    /* RDS username */,
    /* RDS password */,
    "db",
    "3306");

$list = $_GET["list"];
$user = $_GET["user"];

$stmt = $db->prepare("UPDATE users SET default_list = ? WHERE username = ?");
$stmt->bind_param("ss", $list, $user);
$stmt->execute();

$_SESSION["default_list"] = $list;

header("Location: http://" . $_SERVER["HTTP_HOST"] . "/pages/main-page.php?list=$list");
die("<br><br>If you're seeing this, something has gone wrong<br>handle-set-default.php");