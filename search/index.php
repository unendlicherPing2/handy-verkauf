<?php

$database = new mysqli("127.0.0.1", "mariadb", "mariadb", "mariadb");

$search = $_GET["search"] ?? "";

$phones = $database->query("
    SELECT models.Modelname,
           manufacturer.Name,
           models.Image,
           prices.Price 
    FROM models
    LEFT JOIN manufacturer ON 
        models.Manufacturer = manufacturer.ID
        LEFT JOIN prices ON 
        models.Price = prices.ID
    WHERE Modelname LIKE '$search%'
    ORDER BY Modelname;
")->fetch_all();

?>

<?php foreach ($phones as [$name, $manufacturer, $image, $price]) { ?>
    <article style="display: flex; width: 600px; justify-content: space-between;">
        <img src="<?php echo $image ?>" style="width: 200px"/>

        <hgroup>
            <h3><?php echo $name ?></h3>
            <small><?php echo $manufacturer ?></small>

            <br><br>

            <small class="price"><?php echo number_format($price / 100, 2) ?>€</small>
        </hgroup>

        <button hx-get="/phones/?phone=<?php echo $name?>" hx-boost="true">Details</button>
    </article>
<?php } ?>