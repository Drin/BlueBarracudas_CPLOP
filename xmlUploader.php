<?php include("xmlFunctions.php"); ?>
<?php include("meta.php"); ?>

<?php
   /*
    * Process:
    *    IFF they choose an XML file, it is to add a pyroprint to the DB.
    *
    * Control Flow:
    *    File is received
    *    File should be moved to directory on the server
    *    File should be parsed for necessary information:
    *       wellId
    *       date of pyrogram
    *       isolate name
    *       quality control (default = true)
    *       compensated values (normalized)
    *       drop off curves
    *       peak values
    *
    */
   
   //Big file precaution?
   set_time_limit(0);

   foreach ($_FILES as &$file)
   {
      if ($file['name'] !== '')
      {
         if ($file['error'] !== 0)
         {
            die("Error uploading xml file: ".$file['name'].
               "\nError: ".$file['error']);
         }

         //error_reporting(E_ALL);
         //ini_set("display_errors", 1); 

         //Move to know good place (on vm).
         //if (copy($_FILES['xmlFile']['tmp_name'], '/var/www/html/tmp/'.$_FILES['xmlFile']['name']))
         
         //Move to blueBarracudas (current) directory
         //if (copy($file['tmp_name'], '/home/eriq/csc366/366-Spring2011/blueBarracudas/tmp/'.$file['name']))

         if (copy($file['tmp_name'], $tempDir . '/' . $file['name']))
         {
            parseXMLFile($tempDir . '/' . $file['name'], $file['name']);
         }
         else
         {
            die("Error parsing xml file.");
         }
      }
   }
?>
