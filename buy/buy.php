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
