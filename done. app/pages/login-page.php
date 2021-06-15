<?php
session_start();
$_SESSION["username_in_use"] = false;

include "../static/header.html"
?>

<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="../static/styles/style.css">
</head>
<body>
<h2>Please log in</h2>
<form method="post" action="../logic/handlers/handle-login.php">
    <label for="username">Username: </label><input type="text" name="username" id="username" placeholder="Username"><br>
    <label for="password">Password: </label><input type="password" name="password" id="password" placeholder="Password"><br>
    <input type="submit" value="Login"><br><br>
</form>
<?php
if (array_key_exists("invalid_login", $_SESSION) and $_SESSION["invalid_login"] == true)
{
    echo "<p style='color: red'>Incorrect username or password. Please try again.</p>";
}
?>
<p>Or, <a href="register-page.php">create an account</a> if you don't have one</p>
</body>
</html>