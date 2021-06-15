<?php
session_start();

$db = new mysqli(
    /* RDS address */,
    /* RDS username */,
    /* RDS password */,
    "db",
    "3306");

$result = $db->query("SELECT * FROM users");

$users = [];
while ($item = $result->fetch_object())
{
    $array = [];
    $array["username"] = $item->username;
    $array["password"] = $item->password;
    $array["default_list"] = $item->default_list;

    array_push($users, $array);
}

$username = $_POST["username"];
$password = $_POST["password"];

$_SESSION["invalid_login"] = false;

foreach ($users as $user)
{
    if($user["username"] == $username)
    {
        if ($user["password"] == $password)
        {
            $_SESSION["username"] = $username;
            $list = $user["default_list"];
            $_SESSION["default_list"] = $list;

            header("Location: http://" . $_SERVER["HTTP_HOST"] . "/pages/main-page.php?list=$list");
            die("<br><br>If you're seeing this, something has gone wrong<br>handle-login.php");
        } else
            break;
    }
}

$_SESSION["invalid_login"] = true;
header("Location: http://" . $_SERVER["HTTP_HOST"] . "/pages/login-page.php");
die("<br><br>If you're seeing this, something has gone wrong<br>handle-login.php");