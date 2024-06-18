<?php

require("../database.php");

session_start();

if (!($_SESSION["login"] ?? false)) {
    header("Location: ./login.php");
    exit();
}

$max = $_GET["max"] ?? 5;

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

    <table>
        <thead>
            <tr>
                <th>Model</th>
                <th>Manufacturer</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Sold</th>
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