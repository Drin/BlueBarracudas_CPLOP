<?php include("../sqlFunctions.php")?>;

<?php
if (!isset($__FILTER_PYROGRAM__PHP))
{
   $__FILTER_PYROGRAM__PHP = "YES";

   ################################
   ########### Main ###############
   ################################

   $queryStr = "SELECT * ".
               "FROM pyrogram p INNER JOIN (isolate i, primer f, primer r, primer s, dispensation_sequence d, protocol pr) ".
               "ON (p.isolate_name = i.isolate_name AND p.protocol = pr.name AND pr.forward_primer_id = f.primer_id AND ".
                   "pr.reverse_primer_id = r.primer_id AND pr.sequence_primer_id = s.primer_id AND pr.dispensation_id = ".
                   "d.dispensation_id) ".
               " WHERE p.pyrogram_num IN (SELECT pyrogram_num FROM pyrogram_data_point) AND ";
   $conjStr = ' AND ';

   $hasParams = false;

   foreach ($_POST as $attrName => $attrVal) {
      if ($attrVal == '')
         continue;
      
      #echo 'attribute: '.$attrName.' value: '.$attrVal."\n";
      if ($attrName == 'isolate_name') {
         $queryStr .= "p.$attrName = '".$attrVal."'";
         $hasParams = true;
      }
      else if ($attrName != 'machine_id') {
         $queryStr .= $attrName." = '".$attrVal."'";
         $hasParams = true;
      }
      else {
         $queryStr .= $attrName.' = '.$attrVal;
         $hasParams = true;
      }

      $queryStr .= $conjStr;

   }

   $queryStr = preg_replace('/ AND $/', "", $queryStr);

   $filteredPyros;

   if ($hasParams) {
      $filteredPyros = query($queryStr); //acts as a return
   }
   else {
      $filteredPyros = query(preg_replace('/ WHERE p.pyrogram_num IN (SELECT pyrogram_num FROM pyrogram_data_point) AND $/', "", $queryStr));
   }

   $pyroList = Array();
   while ($tuple = mysqli_fetch_assoc($filteredPyros)) {
      $pyroList[count($pyroList)] = $tuple;
   }

   echo json_encode($pyroList);
}

?>
