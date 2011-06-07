<?php include("xmlGhostFunctions.php"); ?>
<?php include("../meta.php"); ?>

<?php
foreach ($_FILES as &$file) {
   if ($file['name'] != '') {
      if ($file['error'] !== 0) {
         die ("Error reading file: ".$file['name']."\n");
      }

      if (copy($file['tmp_name'], $tempDir . $file['name'])) {
         $initialPyro = -1;
         if ($_GET['side'] == 'right') {
            $initialPyro = -41;
         }

         parseGhostXMLFile($tempDir . $file['name'], $initialPyro);
      }

      else {
         die("Error parsing xml file.");
      }
   }
}
?>
