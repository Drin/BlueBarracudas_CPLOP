<?php
   include('../sqlFunctions.php');
   
   //$query = "SELECT DISTINCT(pyrogram_num), CONCAT(xml_file, ', ', well_id) as pyroName FROM pyrogram WHERE pyrogram_num IN (" . $_GET['pyroNums'] . ") ORDER BY xml_file, well_id;";
   $query = "SELECT DISTINCT(pyrogram_num), CONCAT(isolate_name, ', ', host_name) as pyroName FROM pyrogram JOIN isolate USING(isolate_name) WHERE pyrogram_num IN (" . $_GET['pyroNums'] . ") ORDER BY isolate_name, host_name;";
   $res = query($query);
   $rows = Array();

   while ($r = mysqli_fetch_assoc($res))
   {
      $rows[] = $r;
   }

   echo json_encode($rows);
?>
