<?php
   include('../sqlFunctions.php');
   include('../constants.php');

   $leftPyroList = '';
   $rightPyroList = '';
   $leftGhostList = '';
   $rightGhostList = '';
   $query = "";
   $leftGhostQuery = "";
   $rightGhostQuery = "";
   $doubleGhostQuery = "";
   $results = array();
    
   $leftPyros = explode(", ", $_GET['leftPyros']);
   $rightPyros = explode(", ", $_GET['rightPyros']);

   foreach ($leftPyros as $key => $pyroNum) {
      if ($pyroNum < 0) {
         //ghost pyrogram
         $leftGhostList .= "$pyroNum, ";
      }
      else {
         //normal pyrogram
         $leftPyroList .= "$pyroNum, ";
      }
   }

   foreach ($rightPyros as $key => $pyroNum) {
      if ($pyroNum < 0) {
         //ghost pyrogram
         $rightGhostList .= "$pyroNum, ";
      }
      else {
         //normal pyrogram
         $rightPyroList .= "$pyroNum, ";
      }
   }

   $leftPyroList = preg_replace("/, $/", "", $leftPyroList);
   $rightPyroList = preg_replace("/, $/", "", $rightPyroList);
   $leftGhostList = preg_replace("/, $/", "", $leftGhostList);
   $rightGhostList = preg_replace("/, $/", "", $rightGhostList);
   
   if ($leftPyroList != '' && $rightPyroList != '') {
      $query = "SELECT " . 
       "p1.pyrogram_num as num1, p1.xml_file as xml1, p1.well_id as well1, i1.isolate_name as isolate1, i1.host_name as host1, i1.host_species as species1, " .
       "p2.pyrogram_num as num2, p2.xml_file as xml2, p2.well_id as well2, i2.isolate_name as isolate2, i2.host_name as host2, i2.host_species as species2, " .
       "pearsonMatch(p1.pyrogram_num, p2.pyrogram_num) as pearson " .
       "FROM pyrogram p1 JOIN isolate i1 USING(isolate_name), pyrogram p2 JOIN isolate i2 USING(isolate_name) " . 
       "WHERE p1.pyrogram_num IN (" . $leftPyroList . ") AND p2.pyrogram_num IN (" . $rightPyroList . ") ORDER BY pearson;";
   }

   if ($leftGhostList != '' && $rightPyroList != '') {
     $leftGhostQuery = "SELECT " . 
      "p1.pyrogram_num as num1, " .
      "p2.pyrogram_num as num2, " .
      "ghost_pearsonMatch(p1.pyrogram_num, p2.pyrogram_num) as pearson " .
      "FROM ghost_pyrogram p1, pyrogram p2 " . 
      "WHERE p1.pyrogram_num IN (" . $leftGhostList . ") AND p2.pyrogram_num IN (" . $rightPyroList . ") " .
      "ORDER BY pearson;";
   }

   if ($leftPyroList != '' && $rightGhostList != '') {
      $rightGhostQuery = "SELECT " . 
       "p1.pyrogram_num as num2, " .
       "p2.pyrogram_num as num1, " .
       "ghost_pearsonMatch(p1.pyrogram_num, p2.pyrogram_num) as pearson " .
       "FROM ghost_pyrogram p1, pyrogram p2 " . 
       "WHERE p1.pyrogram_num IN (" . $rightGhostList . ") AND p2.pyrogram_num IN (" . $leftPyroList . ") " .
       "ORDER BY pearson;";
   }

   if ($leftGhostList != '' && $rightGhostList != '') {
      $doubleGhostQuery = "SELECT " . 
       "p1.pyrogram_num as num1, " .
       "p2.pyrogram_num as num2, " .
       "ghost_pearsonMatch(p1.pyrogram_num, p2.pyrogram_num) as pearson " .
       "FROM ghost_pyrogram p1, ghost_pyrogram p2 " . 
       "WHERE p1.pyrogram_num IN (" . $leftGhostList . ") AND p2.pyrogram_num IN (" . $rightGhostList . ") " .
       "ORDER BY pearson;";
   }

   /*
   echo "left_ghostQuery: $leftGhostQuery";
   echo "right_ghostQuery: $rightGhostQuery";
    */
 
   //echo "$query <br />";

   $writeFile = true;

   $file = fopen($tempDir . "/match/matchData.csv", 'w') OR $writeFile = false;

   if ($query != "") {
      $res = query($query);

      if ($writeFile)
      {
         $headerLine = '';

         $headerLine .= "xml_file1,well_id1,isolate_name1,host_name1,host_species1,";
         $headerLine .= "xml_file2,well_id2,isolate_name2,host_name2,host_species2,";
         $headerLine .= "pearson\n";

         //echo $headerLine;
         fwrite($file, $headerLine);
      }

      while ($r = mysqli_fetch_assoc($res))
      {
         if ($writeFile)
         {
            $csvLine = '';
         
            $csvLine .= $r['xml1'] . ",";
            $csvLine .= $r['well1'] . ",";
            $csvLine .= $r['isolate1'] . ",";
            $csvLine .= $r['host1'] . ",";
            $csvLine .= $r['species1'] . ",";

            $csvLine .= $r['xml2'] . ",";
            $csvLine .= $r['well2'] . ",";
            $csvLine .= $r['isolate2'] . ",";
            $csvLine .= $r['host2'] . ",";
            $csvLine .= $r['species2'] . ",";
            $csvLine .= $r['pearson'] . "\n";

            //echo $csvLine;
            fwrite($file, $csvLine);
         }
     
         $results[] = array('num1' => $r['num1'], 'num2' => $r['num2'], 'pearson' => $r['pearson']);
      }

      if ($writeFile)
      {
         fclose($file);
      }
   }

   /*
    * TODO ensure matching between ghost and non-ghost is done correctly
    * */
   if ($leftGhostQuery != '') {
      //echo "leftGhost: $leftGhostQuery\n";
      $ghostCorrs = query($leftGhostQuery);

      while ($tuple = mysqli_fetch_assoc($ghostCorrs)) {
         //echo "\n\n===\n\nnum1: ".$tuple['num1']."\n";
         $results[] = array('num1' => $tuple['num1'], 'num2' => $tuple['num2'], 'pearson' => $tuple['pearson']);
      }
   }

   if ($rightGhostQuery != '') {
      //echo "rightGhost: $rightGhostQuery\n";
      $ghostCorrs = query($rightGhostQuery);

      while ($tuple = mysqli_fetch_assoc($ghostCorrs)) {
         //echo "\n\n===\n\nnum1: ".$tuple['num1']."\n";
         $results[] = array('num1' => $tuple['num1'], 'num2' => $tuple['num2'], 'pearson' => $tuple['pearson']);
      }
   }

   if ($doubleGhostQuery != '') {
      //echo "doubleGhost: $doubleGhostQuery\n";
      $ghostCorrs = query($doubleGhostQuery);

      while ($tuple = mysqli_fetch_assoc($ghostCorrs)) {
         //echo "\n\n===\n\nnum1: ".$tuple['num1']."\n";
         $results[] = array('num1' => $tuple['num1'], 'num2' => $tuple['num2'], 'pearson' => $tuple['pearson']);
      }
   }

   echo json_encode($results);
?>
