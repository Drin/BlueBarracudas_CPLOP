<?php
   include('../sqlFunctions.php');
   
   $query = "CALL closestSpecies(" . $_GET['pyroNum'] . ");";
 
   //echo "$query <br />";

   $res = query($query);
   $rows = Array();

   while ($r = mysqli_fetch_assoc($res))
   {
      $rows[] = $r;
   }

   echo json_encode($rows);
?>
