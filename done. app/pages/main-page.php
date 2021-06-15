<?php
session_start();

include "../static/header.html";
require "../vendor/autoload.php";
require "../logic/get-list-items.php";
require "../logic/get-lists.php";

if (!array_key_exists("username", $_SESSION))
{
    header("Location: http://" . $_SERVER["HTTP_HOST"]);
    die("<br><br>If you're seeing this, something has gone wrong<br>main-page.php (no details).php");
}

$username = $_SESSION["username"];
$allListItems = $_SESSION["allListItems"];
$nextId = $_SESSION["nextId"];

$listName = array_key_exists("list", $_GET) ? $_GET["list"] : $_SESSION["default_list"];

foreach ($_SESSION["allLists"] as $list)
    if ($list["user"] == $username)
        $userLists = $list;
?>

<html lang="en">
<head>
    <title>Main Page</title>
    <link type="text/css" rel="stylesheet" href="../static/styles/style.css">
    <link type="text/css" rel="stylesheet" href="../static/styles/dropdown.css">
    <link type="text/css" rel="stylesheet" href="../static/styles/modal.css">
    <link type="text/css" rel="stylesheet" href="../static/styles/tooltip.css">
    <script src="../script/dropdown.js"></script>
    <script src="../script/misc.js"></script>
    <script src="../script/modal.js"></script>
</head>
<body>
<p style="text-align: right; margin: 5px">Logged in as: <?php echo $username; ?><br><a href="profile-page.php">View
        profile</a></p>

<button onclick="toggleNewList()">Create new list</button>
<div id="newListForm" style="display: none">
    <form action="../logic/handlers/handle-create-list.php" method="post">
        <input type="text" id="name" name="name" placeholder="List name" required>
        <input type="hidden" id="user" name="user" value="<?php echo $username; ?>">
        <input type="hidden" id="firstList" name="firstList" value="<?php echo empty($userLists["lists"]); ?>">
        <input type="submit" value="Create">
    </form>
</div>

<?php
if (empty($userLists["lists"]))
{
    echo "<br>It seems you have no lists. Get started by creating one!";
} else
{
    echo "
    <form action='../logic/handlers/handle-set-default.php' method='get'>
        <input type='hidden' name='list' id='list' value='$listName'>
        <input type='hidden' id='user' name='user' value='$username'>
        <input type='submit' value='Set list as default'>
    </form>
    
    <p style='margin: 10px;'></p>

    <div class='dropdown'>
        <button onclick='toggleDropdown()' class='dropbtn'>$listName</button>
        <div id='myDropdown' class='dropdown-content'>";
    foreach ($userLists["lists"] as $name)
        echo "<a href=\"?list=$name\">$name</a>";
    echo "
        </div>
    </div>
    <p style='margin: 10px;'>---</p>
    <table style='margin-left: auto; margin-right: auto'>";

    foreach ($allListItems as $listItem)
    {
        if ($listItem["user"] != $username or $listItem["listName"] != $listName)
            continue;

        if ($listItem["done"])
            continue;

        $text = $listItem["text"];
        $itemId = $listItem["id"];
        $image = $listItem["image"];
        if ($image == "none")
            $image = "../static/images/done-tick.svg";

        echo "
        <tr>
            <td>
                <form method='post' action='../logic/handlers/handle-done-item.php'>
                    <input type='hidden' id='id' name='id' value='$itemId'>
                    <input type='hidden' id='listName' name='listName' value='$listName'>
                    <input type='hidden' id='text' name='text' value='$text'>
                    <input type='image' name='submit' src='$image' alt='Submit' style='width: 20px'>
                </form>
            </td>
            <td>
                <p style='margin: 0; text-align: left'>$text&nbsp;
            <img src='../static/images/bell.png' width='20' onclick='openModal(\"$itemId modal\")'>
            </p>
            </td>
        </tr>
        <div id='$itemId modal' class='modal'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <span class='close' onClick='closeModal(\"$itemId modal\")'>&times;</span>
                    <h1>$text</h1>
                </div>
                <div class='modal-body'>
                    <p>Set a reminder</p>
                    <form action='../logic/handlers/handle-set-reminder.php' method='post'>
                        <label for='time'>
                            <div class='tooltip'>Time and date: 
                                <span class='tooltiptext'>Note for Firefox or Safari users:<br>
                                Input must be given in the format of <i>YYYY-MM-DD</i>T<i>HH:MM</i></span>
                            </div>
                        </label>
                        <input type='datetime-local' name='time' id='time' style='margin: 5px'><br>
                        <label for='email'>Email: </label>
                        <input required type='email' name='email' id='email' style='margin: 5px'><br>
                        <input type='hidden' name='id' id='id' value='$itemId'>
                        <input type='submit' value='Set'>
                    </form>
                </div>
                <div class='modal-footer'>
                </div>
            </div>
        </div>
        ";
    }
    echo "
    </table>
    <p style='margin: 10px;'>---</p>
    <form enctype='multipart/form-data' method='post' action='../logic/handlers/handle-add-to-list.php'
          style='text-align: center'>
        <input type='submit' id='submitNewItem' value='&#43;'>
        <input type='text' id='text' name='text' placeholder='New Item' size='40'><br>
        <p>Upload custom icon (Optional):</p>
        <input type='file' id='image' name='image' accept='image/*' onchange='checkImageSize(this)'>
        <input type='hidden' id='id' name='id' value='$nextId'>
        <input type='hidden' id='listName' name='listName' value='$listName'>
    </form>
    <p id='imageErrorMessage' style='color: red'></p>
    ";
}
?>
</body>
</html>