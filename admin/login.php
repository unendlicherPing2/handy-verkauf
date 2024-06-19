<?php

session_start();

$env = parse_ini_file("../.env", true);

$hash = $env["ADMIN"]["HASH"];
$salt = $env["ADMIN"]["SALT"];

$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";
$login = hash("sha256", $username . $password . $salt);

$_SESSION["login"] = $_SERVER["REQUEST_METHOD"] == "POST"
                  && $login                     == $hash;

if ($_SESSION["login"]) {
    header("Location: ../admin/");
    exit();
}

?>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
</head>

<form method="post">
    <fieldset>
        <input type="text" name="username" required>
        <input type="password" name="password" required>
    </fieldset>
    <button type="submit">Login</button>
</form>