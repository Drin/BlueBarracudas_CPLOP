<?php
   include('../sqlFunctions.php');
   
    $begin = $_GET['begin'];
    $end = $_GET['end'];

   $query = "SELECT DISTINCT(pyrogram_num), CONCAT(isolate_name, ', ', host_name) as pyroName FROM pyrogram JOIN isolate USING(isolate_name) WHERE pyrogram_num IN (SELECT pyrogram_num FROM pyrogram_data_point) AND host_species = 'Environmental '";
    
    if ($begin != 'begin')
    {
      $query .= ("AND pyrogram_date >= '" . $begin . "' "); 
    }

    if ($end != 'end')
    {
      $query .= ("AND pyrogram_date <= '" . $end . "' "); 
    }

   $query .= "ORDER BY isolate_name, host_name;";
   $res = query($query);
   $rows = Array();

   while ($r = mysqli_fetch_assoc($res))
   {
      $rows[] = $r;
   }

   echo json_encode($rows);
?>
