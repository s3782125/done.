<?php
session_start();

if (!array_key_exists("username", $_SESSION))
{
    header("Location: http://" . $_SERVER["HTTP_HOST"]);
    die("<br><br>If you're seeing this, something has gone wrong<br>profile-page.php (no details).php");
}

include "../static/header.html";
include "../logic/get-reminders.php";
include "../logic/get-list-items.php";

$username = $_SESSION["username"];

$items = [];
foreach ($_SESSION["allListItems"] as $item)
    if ($item["user"] == $username)
        $items[$item["id"]] = $item;
?>

<html lang="en">
<head>
    <title>Profile Page</title>
    <link type="text/css" rel="stylesheet" href="../static/styles/style.css">
    <link type="text/css" rel="stylesheet" href="../static/styles/profile.css">
    <link type="text/css" rel="stylesheet" href="../static/styles/modal.css">
    <script src="../script/modal.js"></script>
</head>
<body>
<table style="width: 100%; border: none">
    <tr>
        <td style="border: none">
            <form action="main-page.php" style="float:left;">
                <input type="submit" value="Back to lists">
            </form>
        </td>
        <td style="border: none">
            <form action="login-page.php" style="float:right;">
                <input type="submit" value="Logout">
            </form>
        </td>
    </tr>
    <tr>
        <td style="border: none"></td>
        <td style="border: none">
            <button style="float:right;" onClick="openModal('notifs')">See notifications</button>
        </td>
    </tr>
</table>
<div id='notifs' class='modal'>
    <div class='modal-content'>
        <div class='modal-header'>
            <span class='close' onClick="closeModal('notifs')">&times;</span>
            <h1>Pending reminders</h1>
        </div>
        <div class='modal-body'>
            <table style="width: 100%;">
                <tr>
                    <th>Text</th>
                    <th>Email</th>
                    <th>Time</th>
                </tr>
                <?php
                $allReminders = $_SESSION["reminders"];
                foreach ($allReminders as $reminder)
                {
                    if ($item = $items[$reminder["id"]] and $item["user"] == $username and !$reminder["done"])
                    {
                        $text = $item["text"];
                        $email = $reminder["email"];
                        $time = $reminder["time"];
                        echo "
                        <tr>
                            <td>$text</td>
                            <td>$email</td>
                            <td>$time</td>
                        </tr>
                        ";
                    }
                }
                ?>
            </table>
        </div>
        <div class='modal-footer'>
        </div>
    </div>
</div>
<h1>Your Profile</h1>
<p><?php echo $username; ?></p><br>
<table style="width: 100%">
    <tr>
        <th>All items that aren't done yet</th>
        <th>All items that are done</th>
    </tr>
    <tr>
        <td><span style="display: block; height: 500px;">
            <iframe seamless src="iframes/items-frame.php" width="100%" height="100%">Your browser does not support iframes :(
            </iframe></span>
        </td>
        <td><span style="display: block; height: 500px;">
            <iframe seamless src="iframes/done-items-frame.php" width="100%" height="100%">Your browser does not support iframes :(
            </iframe></span>
        </td>
    </tr>
</table>
</body>
