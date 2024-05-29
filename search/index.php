<?php
$ENV = parse_ini_file(".env");
$database = new mysqli($ENV["CONNECTION"], $ENV["USER"], $ENV["PASSWORD"],$ENV["DATABASE"]);

$search = $_GET["search"] ?? "";
$phones = $database->query("SELECT ".$ENV["TABLE_MODELS"].".modelname, ".$ENV["TABLE_MANUFACTURER"].".name FROM ".$ENV["TABLE_MODELS"]." INNER JOIN ".$ENV["TABLE_MANUFACTURER"]." ON ".$ENV["TABLE_MODELS"].".hersteller_id=".$ENV["TABLE_MANUFACTURER"].".id WHERE modelname LIKE '$search%';")->fetch_all();
?>
<?php foreach ($phones as $phone) { ?>
    <article>
        <hgroup>
            <h2><?php echo $phone[0] ?></h2>
            <small><?php echo $phone[1] ?></small>
        </hgroup>
    </article>
<?php } ?>