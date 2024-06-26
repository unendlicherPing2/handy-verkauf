<?php

namespace db {

    use mysqli;

    $env = parse_ini_file(".env", true);
    $db_login = $env["DATABASE"];
    $tables = $env["DATABASE.TABLES"];

    $database = new mysqli($db_login["IP"], $db_login["USER"], $db_login["PASSWORD"], $db_login["DATABASE"]);

    if ($database->connect_errno) {
        echo "An internal error occured!";
        exit();
    }

    function get_phone(String $phone)
    {
        global $database, $tables;

        $phone = $database->real_escape_string($phone);

        $query = $database->prepare("
            SELECT
                {$tables["MODELS"]}.Name,
                {$tables["MANUFACTURER"]}.Name,
                {$tables["MODELS"]}.Image,
                {$tables["PRICES"]}.Price,
                {$tables["MODELS"]}.Storage,
                {$tables["MODELS"]}.Stock
            FROM
                {$tables["MODELS"]}
            LEFT JOIN {$tables["MANUFACTURER"]} ON {$tables["MODELS"]}.Manufacturer = {$tables["MANUFACTURER"]}.ID
            LEFT JOIN {$tables["PRICES"]} ON {$tables["PRICES"]}.model = {$tables["MODELS"]}.ID
            WHERE {$tables["MODELS"]}.ID = ?
            ORDER BY
                {$tables["PRICES"]}.Timestamp
            DESC
            LIMIT 1
        ");
        $query->bind_param("i", $phone);
        $query->execute();
        return $query->get_result()->fetch_row();
    }

    function get_manufacturers()
    {
        global $database, $tables;

        $result = $database->query("SELECT Name FROM {$tables["MANUFACTURER"]};");
        return array_map(fn ($e) => $e[0], $result->fetch_all());
    }

    /**
     * Searches based on the model name and sorts the results by how much they got sold. (bestseller list)
     */
    function search_phones(String $query)
    {
        global $database, $tables;

        $query = $database->real_escape_string($query);

        return $database->query(
            "SELECT
                {$tables["MODELS"]}.ID,
                {$tables["MODELS"]}.Name,
                {$tables["MANUFACTURER"]}.Name,
                {$tables["MODELS"]}.Image,
                {$tables["PRICES"]}.Price
            FROM
                {$tables["SOLD"]}
            LEFT JOIN {$tables["MODELS"]} ON 
                {$tables["SOLD"]}.Model = {$tables["MODELS"]}.ID
            LEFT JOIN {$tables["MANUFACTURER"]} ON {$tables["MODELS"]}.Manufacturer = {$tables["MANUFACTURER"]}.ID
            LEFT JOIN {$tables["PRICES"]} ON {$tables["PRICES"]}.Model = {$tables["MODELS"]}.ID
            WHERE
                {$tables["MODELS"]}.Name LIKE '$query%' AND NOT EXISTS(
                SELECT
                    1
                FROM
                    {$tables["PRICES"]} Other
                WHERE
                    {$tables["PRICES"]}.model = Other.model AND {$tables["PRICES"]}.Timestamp < Other.Timestamp
            )
            GROUP BY
                {$tables["SOLD"]}.Model
            ORDER BY
	            COUNT({$tables["SOLD"]}.ID) DESC
        "
        )->fetch_all();
    }

    function buy_phone(String $phone, String $forename, String $surname, String $email, String $address): bool
    {
        global $database, $tables;

        $phone = $database->real_escape_string($phone);
        $forename = $database->real_escape_string($forename);
        $surname = $database->real_escape_string($surname);
        $email = $database->real_escape_string($email);
        $address = $database->real_escape_string($address);

        $query = $database->prepare("SELECT {$tables["MODELS"]}.Stock FROM {$tables["MODELS"]} WHERE {$tables["MODELS"]}.ID = ?;");
        $query->bind_param("i", $phone);
        $query->execute();

        if ($query->get_result()->fetch_row()[0] < 1) {
            return false;
        }

        $query = $database->prepare("
        INSERT INTO {$tables["SOLD"]} (
            {$tables["SOLD"]}.Model,
            {$tables["SOLD"]}.Forename,
            {$tables["SOLD"]}.Surname,
            {$tables["SOLD"]}.EMail,
            {$tables["SOLD"]}.Address
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
            "UPDATE {$tables["MODELS"]} 
                SET {$tables["MODELS"]}.Stock = {$tables["MODELS"]}.Stock - 1 
                WHERE {$tables["MODELS"]}.ID = $phone;
        "
        );

        return true;
    }

    function bestsellers()
    {
        global $database, $tables;

        return $database->query(
            "SELECT
                {$tables["MODELS"]}.ID,
                {$tables["MODELS"]}.Name,
                {$tables["MANUFACTURER"]}.Name,
                {$tables["MODELS"]}.Image,
                {$tables["PRICES"]}.Price,
                {$tables["MODELS"]}.Stock,
                COUNT({$tables["SOLD"]}.ID) - 1
            FROM
                {$tables["SOLD"]}
            LEFT JOIN {$tables["MODELS"]} ON 
                {$tables["SOLD"]}.Model = {$tables["MODELS"]}.ID
            LEFT JOIN {$tables["MANUFACTURER"]} ON
                {$tables["MODELS"]}.Manufacturer = {$tables["MANUFACTURER"]}.ID
            LEFT JOIN {$tables["PRICES"]} ON
                {$tables["PRICES"]}.Model = {$tables["MODELS"]}.ID
            GROUP BY
                {$tables["SOLD"]}.Model
            ORDER BY
	            COUNT({$tables["SOLD"]}.ID) DESC"
        )->fetch_all();
    }

    function recent_orders(Int $max)
    {
        global $database, $tables;

        return $database->query(
            "SELECT
                {$tables["MODELS"]}.Name
            FROM
                {$tables["SOLD"]}
            LEFT JOIN {$tables["MODELS"]} ON
                {$tables["SOLD"]}.Model = {$tables["MODELS"]}.ID
            ORDER BY
                {$tables["SOLD"]}.Timestamp DESC
            LIMIT $max
        "
        )->fetch_all();
    }

    function add_phone($modelname,  $manufacturer, $storage, $price, $stock, $image)
    {
        global $database, $tables;

        $result = $database->query(
            "SELECT ID FROM {$tables["MANUFACTURER"]} 
                WHERE Name = '$manufacturer';
            "
        );

        if ($result->num_rows == 0) {
            $database->query(
                "INSERT INTO {$tables["MANUFACTURER"]} (Name)
                    VALUES ($manufacturer);"
            );

            $result = $database->query("SELECT LAST_INSERTED_ID()");
        }

        $manufacturer = match ($result->num_rows) {
            0 => $result->fetch_row()[0],
            default => $result->fetch_row()[0]
        };

        $stock += 1;
        $database->query(
            "INSERT INTO {$tables["MODELS"]} 
                (Name, Manufacturer, Storage, Image, Stock)
                VALUES ('$modelname', $manufacturer, $storage, '$image', $stock);"
        );
        $model = $database->query(
            "SELECT LAST_INSERT_ID();"
        )->fetch_row()[0];

        $price *= 100;
        $price = $database->query(
            "INSERT INTO {$tables["PRICES"]}
                (Price, Model)
                VALUES ($price, $model);
        "
        );

        buy_phone($model, "dummy", "dummy", "dummy", "dummy");
    }

    function update_model(
        $id,
        $modelname,
        $manufacturer,
        $storage,
        $price,
        $stock,
        $image
    ) {
        global $database, $tables;

        $result = $database->query(
            "SELECT ID FROM {$tables["MANUFACTURER"]} 
                WHERE Name = '$manufacturer';
            "
        );

        if ($result->num_rows == 0) {
            $database->query(
                "INSERT INTO {$tables["MANUFACTURER"]} (Name)
                    VALUES ($manufacturer);"
            );

            $result = $database->query("SELECT LAST_INSERTED_ID()");
        }

        $manufacturer = match ($result->num_rows) {
            0 => $result->fetch_row()[0],
            default => $result->fetch_row()[0]
        };

        $database->execute_query(
            "UPDATE {$tables["MODELS"]} 
             SET Name = ?, Manufacturer = ?, Storage = ?, Image = ?, Stock = ? WHERE ID = ?
             ",
            [$modelname, $manufacturer, $storage, $image, $stock, $id]
        );

        $price *= 100;
        $database->query("INSERT INTO {$tables["PRICES"]} (Price, Model) VALUES ($price, $id);");
    }
}
