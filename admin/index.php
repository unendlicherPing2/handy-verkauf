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
        <fieldset>
            <input type="text" name="modelname" placeholder="modelname">
            <input type="text" name="manufacturer" placeholder="manufacturer" list="manufacturers">
            <input type="number" name="storage" placeholder="storage">
            <input type="number" name="price" placeholder="price">
            <input type="number" name="stock" placeholder="stock">
            <input type="url" name="image" placeholder="image">

            <datalist id="manufacturers">
                <?php foreach ($manufacturers as $manufacturer) { ?>
                    <option value="<?php echo $manufacturer; ?>" />
                <?php } ?>
            </datalist>
        </fieldset>
        <button type="submit">Add</button>
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
                            <input type="hidden" name="max" value="<?php echo $max ?>">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="model" value="<?php echo $id ?>">
                            <button type="submit">Update</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="max" value="<?php echo $max ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="model" value="<?php echo $id ?>">
                            <button type="submit">Delete</button>
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