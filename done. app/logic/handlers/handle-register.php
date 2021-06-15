<?php
session_start();

$db = new mysqli(
    /* RDS address */,
    /* RDS username */,
    /* RDS password */,
    "db",
    "3306");

$username = $_POST["username"];
$password = $_POST["password"];

$result = $db->query("SELECT * FROM users WHERE username = '$username'");

$_SESSION["username_in_use"] = false;
if ($result->fetch_object())
{
    $_SESSION["username_in_use"] = true;
    header("Location: http://" . $_SERVER["HTTP_HOST"] . "/pages/register-page.php");
    die("<br><br>If you're seeing this, something has gone wrong<br>handle-register.php");
}
else
{
    $db->query("INSERT INTO users VALUES ('$username', '$password', NULL)");
    header("Location: http://" . $_SERVER["HTTP_HOST"] . "/pages/login-page.php");
    die("<br><br>If you're seeing this, something has gone wrong<br>handle-register.php");
}