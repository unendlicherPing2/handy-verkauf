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

        return $database->query("
            SELECT
                models.ID,
                models.Modelname,
                manufacturer.Name,
                models.Image,
                prices.Price
            FROM
                {$env["TABLE_MODELS"]} AS models
            LEFT JOIN {$env["TABLE_MANUFACTURER"]} AS manufacturer ON models.Manufacturer = manufacturer.ID
            LEFT JOIN {$env["TABLE_PRICES"]} AS prices ON prices.model = models.ID
            WHERE models.ID = $phone
            ORDER BY
                prices.Timestamp
            DESC
            LIMIT 1
        ")->fetch_row();
    }

    function search_phones(String $query)
    {
        global $database, $env;

        $query = $database->real_escape_string($query);

        return $database->query("
            SELECT
                models.ID,
                models.Modelname,
                manufacturer.Name,
                models.Image,
                prices.Price
            FROM
                {$env["TABLE_MODELS"]} AS models
            LEFT JOIN {$env["TABLE_MANUFACTURER"]} AS manufacturer ON models.Manufacturer = manufacturer.ID
            LEFT JOIN {$env["TABLE_PRICES"]} AS prices ON prices.Model = models.ID
            WHERE
                Modelname LIKE '$query%' AND NOT EXISTS(
                SELECT
                    1
                FROM
                    prices Other
                WHERE
                    prices.model = Other.model AND prices.Timestamp < Other.Timestamp
            )
            ORDER BY
                Modelname;
        ")->fetch_all();
    }
}
