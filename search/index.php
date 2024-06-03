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
    <div class="card card-side bg-base-100 shadow-xl m-">   
        <figure><img src="<?php echo $image ?>" style="width: 100px" /></figure>
        <div class="card-body">
            <h2 class="card-title"><?php echo $name ?></h2>
            <p><?php echo $manufacturer ?></p>
            <div class="card-actions justify-end">
                <button hx-get="/phones/?<?php echo $name ?>" hx-boost="true" class="btn btn-primary">
                    Details
                </button>
            </div>
        </div>
    </div>
<?php } ?>