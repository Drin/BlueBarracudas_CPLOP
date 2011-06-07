<?php
   include('sqlFunctions.php');
  
   //IF the table is the pyrogram one, only get pyrograms already sequenced.
   if ($_GET['table'] == 'pyrogram')
   {
      $query = "SELECT DISTINCT(" . $_GET['attr'] . ") FROM " . $_GET['table'] . " WHERE " . $_GET['attr'] . " IS NOT NULL AND pyrogram_num IN (SELECT pyrogram_num FROM pyrogram_data_point) ORDER BY " . $_GET['attr'];
   }
   else
   {
      $query = "SELECT DISTINCT(" . $_GET['attr'] . ") FROM " . $_GET['table'] . " WHERE " . $_GET['attr'] . " IS NOT NULL ORDER BY " . $_GET['attr'];
   }
   $res = query($query);
   $rows = Array();

   while ($r = mysqli_fetch_assoc($res))
   {
      $rows[] = $r;
   }

   echo json_encode($rows);
?>
