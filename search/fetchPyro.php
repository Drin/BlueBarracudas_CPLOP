<?php
   include('../sqlFunctions.php');
   
   $query = "SELECT p.well_id, p.pyrogram_date, p.protocol, p.xml_file, 
    p.quality_control, p.isolate_name, p.pyrogram_num
    FROM pyrogram p
    WHERE p.pyrogram_num IN(" . $_GET['pyroNum'] . ");";

   $res = query($query);
   $rows = Array();

   while ($r = mysqli_fetch_assoc($res))
   {
      $rows[] = $r;
   }

   echo json_encode($rows);
?>
