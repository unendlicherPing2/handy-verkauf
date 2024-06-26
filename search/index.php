<?php

require("../database.php");

$search = $_GET["search"] ?? "";

$phones = db\search_phones($search);

?>

<?php foreach ($phones as [$id, $name, $manufacturer, $image, $price]) { ?>
    <div class="card card-side bg-base-100 shadow-xl m-10 card-bordered">
        <figure><img src="<?php echo $image ?>" style="width: 100px" /></figure>
        <div class="card-body">
            <h2 class="card-title"><?php echo $name ?></h2>
            <p><?php echo $manufacturer ?></p>
            <div class="card-actions justify-end">
                <a href="./phones/?phone=<?php echo $id ?>" class="btn btn-primary">
                    Details
                </a>
            </div>
        </div>
    </div>
<?php } ?>