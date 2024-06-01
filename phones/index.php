<?php

$database = new mysqli("127.0.0.1", "mariadb", "mariadb", "mariadb");

$phone = $_GET["phone"] ?? "";

header("HX-Redirect: /phones/?phone=$phone");

[$name, $manufacturer, $image, $price] = $database->query("
    SELECT models.Modelname,
           manufacturer.Name,
           models.Image,
           prices.Price 
    FROM models
    LEFT JOIN manufacturer ON 
        models.Manufacturer = manufacturer.ID
        LEFT JOIN prices ON 
        models.Price = prices.ID
    WHERE Modelname = '$phone';
")->fetch_row();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $name ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2.0.6/css/pico.min.css" />
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li>
                    <hgroup>
                        <h1><?php echo $name ?></h1>
                        <small><?php echo $manufacturer ?></small></li>
                    </hgroup>
                </li>
            </ul>
        </nav>
    </header>

    <img src="<?php echo $image ?>">
</body>
</html>