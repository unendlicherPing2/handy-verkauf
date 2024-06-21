<?php

require("../database.php");

session_start();

if (!($_SESSION["login"] ?? false)) {
    header("Location: ./login.php");
    exit();
}

$max = $_GET["max"] ?? 5;

$manufacturers = db\get_manufacturers();
$phones = db\bestsellers();
$recent_orders = db\recent_orders($max);

$action = $_POST["action"] ?? "add";

[$modelname, $manufacturer, $storage, $price, $stock, $image, $id] = ["", "", "", "", "", "", ""];

function handle_add()
{
    global $action;

    $modelname = $_POST["modelname"] ?? "";
    $manufacturer = $_POST["manufacturer"] ?? "";
    $storage = $_POST["storage"] ?? 0;
    $price = $_POST["price"] ?? 0;
    $stock = $_POST["stock"] ?? 0;
    $image = $_POST["image"] ?? "";

    db\add_phone($modelname,  $manufacturer, $storage, $price, $stock, $image);

    $action = "add";

    header("Location: .");
    exit();
}

function handle_update()
{
    global $action;

    $id = $_POST["model"] ?? -1;
    $modelname = $_POST["modelname"] ?? "";
    $manufacturer = $_POST["manufacturer"] ?? "";
    $storage = $_POST["storage"] ?? 0;
    $price = $_POST["price"] ?? 0;
    $stock = $_POST["stock"] ?? 0;
    $image = $_POST["image"] ?? "";

    db\update_model($id, $modelname, $manufacturer, $storage, $price, $stock, $image);

    $action = "add";
}

function handle_preupdate()
{
    global $action, $modelname, $manufacturer, $storage, $price, $stock, $image, $id;

    $id = $_POST["model"];

    [$modelname, $manufacturer, $image, $price, $storage, $stock] = db\get_phone($id);

    $action = "update";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    match ($action) {
        "add" => handle_add(),
        "update" => handle_update(),
        "preupdate" => handle_preupdate(),
    };
}

?>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
</head>

<body>

    <h1>Admin Dashboard</h1>

    <h2>Models</h2>

    <form method="post">
        <input type="hidden" name="action" value="<?php echo $action; ?>">
        <input type="hidden" name="model" value="<?php echo $id; ?>">

        <fieldset>
            <input type="text" name="modelname" placeholder="modelname" value="<?php echo $modelname ?>" required>
            <input type="text" name="manufacturer" placeholder="manufacturer" list="manufacturers" value="<?php echo $manufacturer ?>" required>
            <input type="number" name="storage" placeholder="storage" value="<?php echo $storage ?>" required>
            <input type="number" name="price" placeholder="price" value="<?php echo $price ?>" required>
            <input type="number" name="stock" placeholder="stock" value="<?php echo $stock ?>" required>
            <input type="url" name="image" placeholder="image" value="<?php echo $image ?>" required>

            <datalist id="manufacturers">
                <?php foreach ($manufacturers as $manufacturer) { ?>
                    <option value="<?php echo $manufacturer; ?>" />
                <?php } ?>
            </datalist>
        </fieldset>
        <button type="submit"><?php echo $action; ?></button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Model</th>
                <th>Manufacturer</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Sold</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($phones as [$id, $model, $manufacturer, $image, $price, $stock, $sold]) { ?>
                <tr>
                    <td><?php echo $model; ?></td>
                    <td><?php echo $manufacturer; ?></td>
                    <td><?php echo number_format($price / 100, 2); ?>€</td>
                    <td><?php echo $stock; ?></td>
                    <td><?php echo $sold; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="action" value="preupdate">
                            <input type="hidden" name="model" value="<?php echo $id ?>">
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Recent Orders</h2>
    <form method="GET"><input type="number" name="max" value=<?php echo $max; ?>></form>
    <ul>
        <?php foreach ($recent_orders as [$model]) { ?>
            <li><?php echo $model; ?></li>
        <?php } ?>
    </ul>
</body>