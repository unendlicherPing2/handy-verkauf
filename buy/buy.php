<?php

require("../database.php");

$phone = $_POST["phone"] ?? "";
$forename = $_POST["forename"] ?? "";
$surname = $_POST["surname"] ?? "";
$email = $_POST["email"] ?? "";
$address = $_POST["address"] ?? "";

$success = db\buy_phone($phone, $forename, $surname, $email, $address);

echo match ($success) {
    true => "Success",
    false => "Failed"
};

?>

<a href="../" >Go Back</a>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">