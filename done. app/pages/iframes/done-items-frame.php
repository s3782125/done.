<?php
session_start()
?>

<html>
<head>
    <link type="text/css" rel="stylesheet" href="../../static/styles/style.css">
    <link type="text/css" rel="stylesheet" href="../../static/styles/profile.css">
</head>
<body>
<form method="post" action="../../logic/handlers/handle-search.php">
    <label for="text">Item text: </label>
    <input type="text" id="text" name="text">
    <input type="hidden" id="source" name="source" value="/pages/iframes/done-items-frame.php">
    <input type="submit" value="Search">
</form>
<table style="width: 100%">
    <tr>
        <th>Text</th>
        <th>List</th>
        <th>Custom image</th>
    </tr>
    <?php
    $result = $_SESSION["search_results"];
    if (!empty($result))
    {
        foreach ($result as $value)
        {
            $text = $value["text"];
            $list = $value["list"];
            $image = $value["image"];
            if ($value["doneint"] == "1" and $value["user"] == $_SESSION["username"])
            {
                echo "
                <tr>
                    <td>$text</td>
                    <td>$list</td>
                    <td>";
                if (array_key_exists("image", $value))
                    echo "<img src=\"$image\" style='width: 30px'>";
                echo "</td>
                </tr>
                ";
            }
        }
    }
    $_SESSION["search_results"] = [];
    ?>
</table>
</body>
</html>