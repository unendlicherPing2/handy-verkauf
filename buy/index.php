<?php

require("../database.php");

$phone = $_GET["phone"] ?? "";

[$name, $manufacturer, $image, $price] = db\get_phone($phone);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy <?php echo $name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.11.1/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
</head>

<body>
    <form hx-post="./buy.php" hx-swap="outerHTML" class="flex justify-center items-center h-screen">
        <input type="hidden" name="phone" value="<?php echo $phone ?>">

        <div class="modal-box modal-bottom">
            <grid class="flex items-center gap-2 m-4">
                <input type="text" class="input input-bordered" placeholder="Forename" name="forename" required />
                <input type="text" class="input input-bordered" placeholder="Surname" name="surname" required />
            </grid>

            <label class="input input-bordered flex items-center gap-2 m-4">
                <input type="text" class="grow" placeholder="Email" name="email" required />
            </label>
            <span></span>
            <label class="input input-bordered flex items-center gap-2 m-4">
                <input type="text" class="grow" placeholder="Address" name="address" required />
            </label>

        </div>
        <div class="modal-box modal-bottom">
            <h1>Price: <?php echo number_format($price / 100, 2) . "€" ?></h1>
            <button type="submit" class="modal-action btn btn-primary">Confirm purchase</button>
        </div>
    </form>
</body>