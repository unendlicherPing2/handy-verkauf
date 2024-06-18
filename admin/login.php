<?php

session_start();

$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";
$env = parse_ini_file("../.env");

if ($_SERVER["REQUEST_METHOD"] == "POST" 
    && $username == $env["ADMIN_USER"] 
    && $password == $env["ADMIN_PASSWORD"]) {
    $_SESSION["login"] = true;
}

if ($_SESSION["login"] ?? false) {
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