<?php
session_start();
$_SESSION["invalid_login"] = false;
include "../static/header.html"
?>

<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="../static/styles/style.css">
</head>
<body>
<h2>Register an account</h2>
<form method="post" action="../logic/handlers/handle-register.php">
    <label for="username">Username: </label><input required type="text" name="username" id="username" placeholder="Username"><br>
    <label for="password">Password: </label><input required type="password" name="password" id="password" placeholder="Password"><br>
    <input type="submit" value="Register"><br><br>
</form>
<?php
if (array_key_exists("username_in_use", $_SESSION) and $_SESSION["username_in_use"])
{
    echo "<p style='color: red'>That username is taken, please try another.</p>";
}
?>
<p>Or, <a href="login-page.php">login</a> if you already have an account</p>
</body>
</html>
