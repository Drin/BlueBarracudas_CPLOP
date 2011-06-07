<?php include("../sqlFunctions.php");?>
<?php
if (!isset($__FILTER_ISOLATE__PHP))
{
   $__FILTER_ISOLATE__PHP = "YES";

   ################################
   ########### Main ###############
   ################################

   $attrCollMap = array("isolate_name" => "i.isolate_name",
                        "host_name" => "i.host_name",
                        "sample_id" => "i.sample_id",
                        "host_species" => "i.host_species");

   $queryStr = "SELECT * ".
               "FROM isolate i INNER JOIN (sample s, host_species h) ".
               "ON (i.sample_id = s.sample_id AND i.host_species = s.host_species ".
               "AND i.host_name = s.host_name AND s.host_species = h.common_name) WHERE ";
   $conjStr = ' AND ';

   $hasParams = false;
   $pyroPrinted = 0;
   $notPyroPrinted = 0;

   foreach ($_POST as $attrName => $attrVal) {
      if ($attrVal == '')
         continue;
      
      #echo 'attribute: '.$attrName.' value: '.$attrVal."\n";
      if ($attrName == 'from_date') {
         $queryStr .= $attrName." > '".$attrVal."'";
         $hasParams = true;
      }
      else if ($attrName == 'to_date') {
         $queryStr .= $attrName." < '".$attrVal."'";
         $hasParams = true;
      }
      else if ($attrName == 'not_pyroPrinted') {
         //$queryStr .= "is_pyroprinted = $attrVal";
         //$hasParams = true;
         $notPyroPrinted = $attrVal;
         continue;
      }
      else if ($attrName == 'is_pyroPrinted') {
         //$queryStr .= "is_pyroprinted = $attrVal";
         $pyroPrinted = $attrVal;
         continue;
      }
      else {
         if (isset($attrCollMap[$attrName]))
            $attrName = $attrCollMap[$attrName];

         $queryStr .= $attrName." = '".$attrVal."'";
         $hasParams = true;
      }

      $queryStr .= $conjStr;

   }

   if ($pyroPrinted != $notPyroPrinted) {
      //tmpVal is the 'negation' of the 'notPyroPrinted' checkbox because
      //if 'not yet pyrosequenced' is checked then is_pyroprinted = 0 and
      //notPyroPrinted = 1. therefore this converts the value to the equivalent
      //desired value in the DB.
      if ($notPyroPrinted == 1)
         $queryStr .= "is_pyroprinted = 0";
      else
         $queryStr .= "is_pyroprinted = 1";

      $hasParams = true;
   }

   $queryStr = preg_replace('/ AND $/', "", $queryStr);

   $filteredPyros;
   #echo $queryStr;

   if ($hasParams) {
      #echo "hasParams\n";
      $filteredPyros = query($queryStr); //acts as a return
   }
   else {
      #echo "hasNoParams\n";
      $filteredPyros = query(preg_replace('/ WHERE $/', "", $queryStr));
   }

   $pyroList = Array();
   while ($tuple = mysqli_fetch_assoc($filteredPyros)) {
      #echo "stuff\n";
      #echo join(",", $tuple)."\n";
      #echo "more stuff\n";
      $pyroList[count($pyroList)] = $tuple;
   }

   echo json_encode($pyroList);

}
?>
