<?php include("sqlFunctions.php"); ?>

<?php
    $delete = "DELETE from host_species;";
    query($delete);

    $delete = "DELETE from host;";
    query($delete);

    $delete = "DELETE from sample;";
    query($delete);

    $delete = "DELETE from isolate;";
    query($delete);

    $delete = "DELETE from pyrogram;";
    query($delete);

    $delete = "DELETE from pyrogram_data_point;";
    query($delete);

    $delete = "DELETE from compensation_slope;";
    query($delete);

    $delete = "DELETE from protocol;";
    query($delete);

    $delete = "DELETE from dispensation_sequence;";
    query($delete);

    $delete = "DELETE from primer;";
    query($delete);
?>
