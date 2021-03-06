<?php include("sqlFunctions.php");?>
<?php
if (!isset($__XMLPARSER_FUNCTIONS__PHP))
{
   $__XMLPARSER_FUNCTIONS__PHP = "YES";

   #http://www.php.net/manual/en/example.xml-map-tags.php
   #http://www.php.net/manual/en/function.xml-parse.php

   $dispTag = "";
   $currTag = "";
   $currWellId = "";

   $wellIdMap = array();
   $peakValueMap = array();
   $peakAreaMap = array();
   $peakWidthMap = array();
   $baselineOffsetMap = array();
   $signalToNoiseMap = array();
   $dispSeqMap = array();
   $normalizedValMap = array();
   $wellDropOffCurves = array();

   ################################
   ###### Character handler ######
   ################################

   function tagData($parser, $data) {
      global $dispTag, $currTag, $currWellId, $wellIdMap, $dispSeqMap, $normalizedValMap, $peakValueMap,
       $peakAreaMap, $peakWidthMap, $baselineOffsetMap, $signalToNoiseMap;

      if ($dispTag == "DISPENSATION")  {
         //echo "dispTag says dispensation, ";
      }

      if ($currTag == "NORMALIZEDSINGELVALUES" && $currWellId != "") {
         $normalizedValMap[$currWellId] = explode(";", $data, -1);
         #echo "tag data: $data[0]\n";
      }

      else if ($currTag == "DISPORDER" && $currWellId != "") {
         $dispSeqMap[$currWellId] = $data;
      }

      else if ($currTag == "NOTE" && $currWellId != "") {
         $wellIdMap[$currWellId] = $data;
      }

      else if ($dispTag == "DISPENSATION" && $currWellId != "") {
         if ($currTag == "SIGNALVALUE") {
            //echo "added peakValue Well: $currWellId, ";
            if (!isset($peakValueMap[$currWellId])) {
               $peakValueMap[$currWellId] = array();
            }
         
            $peakList = $peakValueMap[$currWellId];
            $peakList[count($peakList)] = $data;

            $peakValueMap[$currWellId] = $peakList;
         }
         else if ($currTag == "PEAKAREA") {
            //echo "added peakArea Well: $currWellId, ";
            if (!isset($peakAreaMap[$currWellId])) {
               $peakAreaMap[$currWellId] = array();
            }
         
            $peakList = $peakAreaMap[$currWellId];
            $peakList[count($peakList)] = $data;

            $peakAreaMap[$currWellId] = $peakList;
         }
         else if ($currTag == "PEAKWIDTH") {
            //echo "added peakWidth Well: $currWellId, ";
            if (!isset($peakWidthMap[$currWellId])) {
               $peakWidthMap[$currWellId] = array();
            }
         
            $peakList = $peakWidthMap[$currWellId];
            $peakList[count($peakList)] = $data;

            $peakWidthMap[$currWellId] = $peakList;
         }
         else if ($currTag == "BASELINEOFFSET") {
            //echo "added baselineOffset Well: $currWellId, ";
            if (!isset($baselineOffsetMap[$currWellId])) {
               $baselineOffsetMap[$currWellId] = array();
            }
         
            $peakList = $baselineOffsetMap[$currWellId];
            $peakList[count($peakList)] = $data;

            $baselineOffsetMap[$currWellId] = $peakList;
         }
         else if ($currTag == "SIGNALTONOISE") {
            //echo "added signalToNoise Well: $currWellId, ";
            if (!isset($signalToNoiseMap[$currWellId])) {
               $signalToNoiseMap[$currWellId] = array();
            }
         
            $peakList = $signalToNoiseMap[$currWellId];
            $peakList[count($peakList)] = $data;

            $signalToNoiseMap[$currWellId] = $peakList;
         }
      }
   }

   ################################
   ######  xml tag handler  #######
   ################################

   function wellInfoStart($parser, $name, $attrs) {
      global $dispTag, $currTag, $currWellId, $wellDropOffCurves;
      $currTag = $name;

      #echo "looking at tag $currTag\n";
      if ($name == "DISPENSATION") {
         $dispTag = $name;
      }
      else if ($name == "SDISPENSATION") {
         $dispTag = "";
      }

      if ($name == "WELLINFO" || $name == "WELLANALYSISMETHODRESULTS" || $name == "WELLDATA") {
         $currWellId = $attrs["WELLNR"];
      }
      else if ($name == "DROPOFFCURVE" && $currWellId != "") {
         if (!isset($wellDropOffCurves[$currWellId])) {
            $wellDropOffCurves[$currWellId] = array();
         }

         $dropOffCurveMap = $wellDropOffCurves[$currWellId];
         $dropOffCurveMap[$attrs["LEVEL"]] = explode(";", $attrs["VALUES"], -1);

         $wellDropOffCurves[$currWellId] = $dropOffCurveMap;
      }
   }

   function wellInfoEnd($parser, $name) {
      global $currTag, $currWellId;
      $currTag = "";

      if ($name == "WELLINFO" || $name == "WELLANALYSISMETHODRESULTS") {
         $currWellId = "";
      }
   }

   ################################
   ######## Print Functions #######
   ################################
   function printWellIds() {
      global $wellIdMap;

      foreach ($wellIdMap as $wellId => $wellIsolate) {
         echo "well $wellId contains $wellIsolate\n"; 
      }
   }

   function printDispensations() {
      global $dispSeqMap;

      foreach ($dispSeqMap as $well => $seq) {
         echo "well $well has sequence $seq\n";
      }
   }

   function printNormalizedValues() {
      global $normalizedValMap;

      foreach ($normalizedValMap as $wellId => $valueList) {
         echo "Well $wellId has normalized values:\n";
         foreach ($valueList as $index => $value) {
            echo "\tindex $index = $value\n";
         }
      }
   }

   function printDropOffCurves() {
      global $wellDropOffCurves;

      foreach ($wellDropOffCurves as $wellId => $dropOffMap) {
         echo "well $wellId has dropOffCurve:\n";

         foreach ($dropOffMap  as $dropLevel => $dropList) {
            echo "\tdropOffLevel $dropLevel has dropOffValues:\n";

            foreach ($dropList as $dropNdx => $dropValue) {
               echo "\t\tindex $dropNdx = $dropValue\n";
            }
         }
      }
   }

   function printPeakValues() {
      global $peakValueMap;

      foreach ($peakValueMap as $wellId => $peakList) {
         echo "well $wellId has peaks:\n";

         foreach ($peakList as $peakNdx => $peakVal) {
            echo "\tindex $peakNdx = $peakVal:\n";
         }
      }
   }

   function printPeakAreas() {
      global $peakAreaMap;

      foreach ($peakAreaMap as $wellId => $peakList) {
         echo "well $wellId has peaks areas:\n";

         foreach ($peakList as $ndx => $peakArea) {
            echo "\tindex $ndx = $peakArea:\n";
         }
      }
   }

   function printPeakWidths() {
      global $peakWidthMap;

      foreach ($peakWidthMap as $wellId => $peakList) {
         echo "well $wellId has peaks widths:\n";

         foreach ($peakList as $ndx => $peakWidth) {
            echo "\tindex $ndx = $peakWidth:\n";
         }
      }
   }

   function printBaselineOffsets() {
      global $baselineOffsetMap;

      foreach ($baselineOffsetMap as $wellId => $peakList) {
         echo "well $wellId has baseline offsets:\n";

         foreach ($peakList as $ndx => $baselineOffset) {
            echo "\tindex $ndx = $baselineOffset:\n";
         }
      }
   }

   function printSignalToNoises() {
      global $signalToNoiseMap;

      foreach ($signalToNoiseMap as $wellId => $peakList) {
         echo "well $wellId has signal to noises:\n";

         foreach ($peakList as $ndx => $signalToNoise) {
            echo "\tindex $ndx = $signalToNoise:\n";
         }
      }
   }

   ################################
   ########### Main ###############
   ################################
   #call calculate(xml_file)

   function parseXMLFile($fileName, $shortName) {
      if (!$file = fopen($fileName, "r")) {
         die ("Error: could not open file");
      }

      $xmlParser = xml_parser_create();
      xml_set_element_handler($xmlParser, "wellInfoStart", "wellInfoEnd");
      xml_set_character_data_handler($xmlParser, "tagData");

      while ($xmlFileData = fread($file, filesize($fileName))) {
         $success = xml_parse($xmlParser, $xmlFileData);
      }

      /*
      printPeakAreas();
      printPeakWidths();
      printBaselineOffsets();
      printSignalToNoises();
       */
      printWellIds();

      $basicFileName = preg_replace('/\.pyrorun$/', '', $shortName);
      
      $pyroMap = prepDataInsert($fileName, $shortName);

      insertParsedData($pyroMap);

      echo "call calculate('$basicFileName');";
      query("call calculate('$basicFileName');");

      /*
      foreach ($pyroMap as $well_id => $pyroNum) {
         echo "well $well_id has pyroNum $pyroNum"."<br/>";
      }
       */
   }

   function prepDataInsert($basicFileName) {
      global $dispSeqMap;

      //echo "$basicFileName";

      $pyrogramIdList = query("SELECT well_id, pyrogram_num FROM pyrogram WHERE xml_file = '$basicFileName'");
      $pyrogramIDMap = array();

      while ($row = mysqli_fetch_array($pyrogramIdList)) {
         //echo "iterating...<br/>";
         $pyrogramIDMap[$row['well_id']] = $row['pyrogram_num'];
         /*
         foreach (str_split($dispSeqMap[$row['well_id']]) as $ndx => $nucl) {
            echo "index $ndx has nucleotide $nucl"."<br/>";
         }
          */
      }

      return $pyrogramIDMap;
   }

   function insertParsedData($pyroMap) {
      global $wellIdMap, $peakValueMap, $dispSeqMap, $normalizedValMap, $wellDropOffCurves;

      foreach ($pyroMap as $wellId => $pyroNum) {
         $dispArr = str_split($dispSeqMap[$wellId]);
         #$normArr = $normalizedValMap[$wellId];
         //echo "wellID as a key: $wellId, ";
         $peakArr = $peakValueMap[$wellId];
         $peakWidthArr = $peakWidthMap[$wellId];
         $peakAreaArr = $peakAreaMap[$wellId];
         $baselineOffsetArr = $baselineOffsetMap[$wellId];
         $signalToNoiseArr = $signalToNoiseMap[$wellId];

         #TODO add appropriate attribute names to dataPointQuery string and insert pyroprint values
         #in the same format as peakArr[$pos] (the form <valueMap[$pos]>)
         foreach ($normalizedValMap[$wellId] as $pos => $normVal) {
            $dataPointQuery = "INSERT IGNORE INTO pyrogram_data_point (pyrogram_num, position, peak_value,"
                             ."compensated_peak_value, nucleotide) VALUES ($pyroNum, $pos, $peakArr[$pos], "
                             ."$normVal, '$dispArr[$pos]')";

            query($dataPointQuery);
            //echo "$dataPointQuery<br/>\n";
         }

         foreach ($wellDropOffCurves[$wellId] as $level => $dropValueList) {
            foreach ($dropValueList as $dropPos => $dropValue) {
               $compSlopeQuery = "INSERT IGNORE INTO compensation_slope (pyrogram_num, position, level, drop_off_value)"
                                ." VALUES ($pyroNum, $dropPos, $level, $dropValue)";

               query($compSlopeQuery);
               #echo "$compSlopeQuery<br/>";
            }
         }
      }
   }
}
parseXMLFile($argv[1], "");
?>
