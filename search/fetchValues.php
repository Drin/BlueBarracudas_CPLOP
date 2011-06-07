<?php
   include('../sqlFunctions.php');
   
   $query = "SELECT data.pyrogram_num as pyroNum, 
              GROUP_CONCAT(data.nucleotide ORDER BY data.position SEPARATOR ' ') as seq,
              GROUP_CONCAT(data.peak_value ORDER BY data.position SEPARATOR ' ') as peak_values 
             FROM pyrogram p, protocol pr, dispensation_sequence d, pyrogram_data_point data 
             WHERE d.dispensation_sequence LIKE '%".$_GET['searchSeq']."%' 
              AND pr.dispensation_id = d.dispensation_id 
              AND pr.name = p.protocol
              AND p.pyrogram_num = data.pyrogram_num
             GROUP BY data.pyrogram_num;";

   $res = query($query);
   $rows = Array();

   while ($r = mysqli_fetch_assoc($res))
   {
      $rows[] = $r;
   }

   echo json_encode($rows);
?>
