<?php
$ENV = parse_ini_file(".env");
$database = new mysqli($ENV["CONNECTION"], $ENV["USER"], $ENV["PASSWORD"],$ENV["DATABASE"]);

$search = $_GET["search"] ?? "";
$phones = $database->query("SELECT ".$ENV["TABLE_MODELS"].".modelname,".$ENV["TABLE_MODELS"].".preis,".$ENV["TABLE_MODELS"].".speicher,".$ENV["TABLE_MODELS"].".bestand, ".$ENV["TABLE_MANUFACTURER"].".name FROM ".$ENV["TABLE_MODELS"]." INNER JOIN ".$ENV["TABLE_MANUFACTURER"]." ON ".$ENV["TABLE_MODELS"].".hersteller_id=".$ENV["TABLE_MANUFACTURER"].".id WHERE modelname LIKE '$search%';")->fetch_all();
?>
 <div class="grid" >
<?php foreach ($phones as $phone) { ?>
        <div class="pico-background-grey-100" style="border-radius:7px;height:105%">
            <hgroup>
                <h3 style="text-align:center"><?php echo $phone[0] ?></h3>
                <p style="text-align:center"><?php echo $phone[2] ?> GB</p>
            </hgroup>
        <div class="grid center" style="width:95%;margin: auto">
            <div class="pico-background-grey-150" style="text-align:center;border-radius:7px"><small style="font-size:75%"><?php echo $phone[3] ?> Stk.</small></div>
            <div class="pico-background-grey-150" style="text-align:center;border-radius:7px"><small style="font-size:75%"><b><?php echo $phone[1] ?>€</b></small></div>
            <div class="pico-background-grey-150" style="text-align:center;border-radius:7px"><small style="font-size:75%"><?php echo $phone[4] ?></small></div>
            </div>
        </div>
<?php } ?>
</div>