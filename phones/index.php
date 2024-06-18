<?php
require("../database.php");

$phone = $_GET["phone"] ?? "";

[$name, $manufacturer, $image, $price] = db\get_phone($phone)

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.11.1/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
</head>

<body>
    <hgroup>
        <h1><?php echo $name ?></h1>
        <small><?php echo $manufacturer ?></small></li>
    </hgroup>

    <img src="<?php echo $image ?>">

    <a href="../buy/?phone=<?php echo $phone ?>" class="btn btn-primary">Buy now!</a>
</body>

</html>