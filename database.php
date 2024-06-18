<?php

namespace db {

    use mysqli;

    $env = parse_ini_file(".env");

    $database = new mysqli($env["IP"], $env["USER"], $env["PASSWORD"], $env["DATABASE"]);

    if ($database->connect_errno) {
        echo "An internal error occured!";
        exit();
    }

    function get_phone(String $phone)
    {
        global $database, $env;

        $phone = $database->real_escape_string($phone);

        $query = $database->prepare("
            SELECT
                {$env["TABLE_MODELS"]}.Name,
                {$env["TABLE_MANUFACTURER"]}.Name,
                {$env["TABLE_MODELS"]}.Image,
                {$env["TABLE_PRICES"]}.Price
            FROM
                {$env["TABLE_MODELS"]}
            LEFT JOIN {$env["TABLE_MANUFACTURER"]} ON {$env["TABLE_MODELS"]}.Manufacturer = {$env["TABLE_MANUFACTURER"]}.ID
            LEFT JOIN {$env["TABLE_PRICES"]} ON {$env["TABLE_PRICES"]}.model = {$env["TABLE_MODELS"]}.ID
            WHERE {$env["TABLE_MODELS"]}.ID = ?
            ORDER BY
                {$env["TABLE_PRICES"]}.Timestamp
            DESC
            LIMIT 1
        ");
        $query->bind_param("i", $phone);
        $query->execute();
        return $query->get_result()->fetch_row();
    }

    /**
     * Searches based on the model name and sorts the results by how much they got sold. (bestseller list)
     */
    function search_phones(String $query)
    {
        global $database, $env;

        $query = $database->real_escape_string($query);

        return $database->query(
            "SELECT
                {$env["TABLE_MODELS"]}.ID,
                {$env["TABLE_MODELS"]}.Name,
                {$env["TABLE_MANUFACTURER"]}.Name,
                {$env["TABLE_MODELS"]}.Image,
                {$env["TABLE_PRICES"]}.Price
            FROM
                {$env["TABLE_SOLD"]}
            LEFT JOIN {$env["TABLE_MODELS"]} ON 
                {$env["TABLE_SOLD"]}.Model = {$env["TABLE_MODELS"]}.ID
            LEFT JOIN {$env["TABLE_MANUFACTURER"]} ON {$env["TABLE_MODELS"]}.Manufacturer = {$env["TABLE_MANUFACTURER"]}.ID
            LEFT JOIN {$env["TABLE_PRICES"]} ON {$env["TABLE_PRICES"]}.Model = {$env["TABLE_MODELS"]}.ID
            WHERE
                {$env["TABLE_MODELS"]}.Name LIKE '$query%' AND NOT EXISTS(
                SELECT
                    1
                FROM
                    {$env["TABLE_PRICES"]} Other
                WHERE
                    {$env["TABLE_PRICES"]}.model = Other.model AND {$env["TABLE_PRICES"]}.Timestamp < Other.Timestamp
            )
            GROUP BY
                {$env["TABLE_SOLD"]}.Model
            ORDER BY
	            COUNT({$env["TABLE_SOLD"]}.ID) DESC
        ")->fetch_all();
    }

    function buy_phone(String $phone, String $forename, String $surname, String $email, String $address): bool
    {
        global $database, $env;

        $phone = $database->real_escape_string($phone);
        $forename = $database->real_escape_string($forename);
        $surname = $database->real_escape_string($surname);
        $email = $database->real_escape_string($email);
        $address = $database->real_escape_string($address);

        $query = $database->prepare("SELECT {$env["TABLE_MODELS"]}.Stock FROM {$env["TABLE_MODELS"]} WHERE {$env["TABLE_MODELS"]}.ID = ?;");
        $query->bind_param("i", $phone);
        $query->execute();

        if ($query->get_result()->fetch_row()[0] < 1) {
            return false;
        }

        $query = $database->prepare("
        INSERT INTO {$env["TABLE_SOLD"]} (
            {$env["TABLE_SOLD"]}.Model,
            {$env["TABLE_SOLD"]}.Forename,
            {$env["TABLE_SOLD"]}.Surname,
            {$env["TABLE_SOLD"]}.EMail,
            {$env["TABLE_SOLD"]}.Address
        )
        VALUES(
            ?,
            ?,
            ?,
            ?,
            ?
        );
        ");
        $query->bind_param("issss", $phone, $forename, $surname, $email, $address);
        $query->execute();

        $database->query(
            "UPDATE {$env["TABLE_MODELS"]} 
                SET {$env["TABLE_MODELS"]}.Stock = {$env["TABLE_MODELS"]}.Stock - 1 
                WHERE {$env["TABLE_MODELS"]}.ID = $phone;
        "
        );

        return true;
    }

    function bestsellers()
    {
        global $database, $env;

        return $database->query(
            "SELECT
                {$env["TABLE_MODELS"]}.ID,
                {$env["TABLE_MODELS"]}.Name,
                {$env["TABLE_MANUFACTURER"]}.Name,
                {$env["TABLE_MODELS"]}.Image,
                {$env["TABLE_PRICES"]}.Price,
                {$env["TABLE_MODELS"]}.Stock,
                COUNT({$env["TABLE_SOLD"]}.ID)
            FROM
                {$env["TABLE_SOLD"]}
            LEFT JOIN {$env["TABLE_MODELS"]} ON 
                {$env["TABLE_SOLD"]}.Model = {$env["TABLE_MODELS"]}.ID
            LEFT JOIN {$env["TABLE_MANUFACTURER"]} ON
                {$env["TABLE_MODELS"]}.Manufacturer = {$env["TABLE_MANUFACTURER"]}.ID
            LEFT JOIN {$env["TABLE_PRICES"]} ON
                {$env["TABLE_PRICES"]}.Model = {$env["TABLE_MODELS"]}.ID
            WHERE
                NOT EXISTS(
                SELECT
                    1
                FROM
                    {$env["TABLE_PRICES"]} Other
                WHERE
                    {$env["TABLE_PRICES"]}.Model = Other.Model AND
                    {$env["TABLE_PRICES"]}.Timestamp < Other.Timestamp
            )
            GROUP BY
                {$env["TABLE_SOLD"]}.Model
            ORDER BY
	            COUNT({$env["TABLE_SOLD"]}.ID) DESC"
        )->fetch_all();
    }

    function recent_orders(Int $max) {
        global $database, $env;

        return $database->query(
            "SELECT
                {$env["TABLE_MODELS"]}.Name
            FROM
                {$env["TABLE_SOLD"]}
            LEFT JOIN {$env["TABLE_MODELS"]} ON
                {$env["TABLE_SOLD"]}.Model = {$env["TABLE_MODELS"]}.ID
            ORDER BY
                {$env["TABLE_SOLD"]}.Timestamp
            LIMIT $max
        ")->fetch_all();
    }
}
