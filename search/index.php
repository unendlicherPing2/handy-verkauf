<?php

$database = new mysqli("127.0.0.1", "mariadb", "mariadb", "mariadb");

$search = $_GET["search"] ?? "";

$phones = $database->query("SELECT models.Modelname, manufacturer.Name FROM models INNER JOIN manufacturer ON models.Manufacturer=manufacturer.ID WHERE Modelname LIKE '$search%';")->fetch_all();
?>

<?php foreach ($phones as $phone) { ?>
    <article>
        <hgroup>
            <h2><?php echo $phone[0] ?></h2>
            <small><?php echo $phone[1] ?></small>
        </hgroup>
    </article>
<?php } ?>