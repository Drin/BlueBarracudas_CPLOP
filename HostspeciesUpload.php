<?php include("sqlFunctions.php"); ?>

<?php

	
	$HOST_SPECIES_TABLE = 'host_species';

   if ($_FILES['HostSpeciesFile']){
      
	  if ($_FILES['HostSpeciesFile']['error'] !== 0){
         die("Error uploadingS file: ".$_FILES['HostSpeciesFile']['name']."\n".$_FILES['HostSpeciesFile']['error']);
      }

      if (($handle = fopen($_FILES['HostSpeciesFile']['tmp_name'], "r")) !== FALSE){
         
         fgetcsv($handle);

         $insert = "INSERT IGNORE INTO $HOST_SPECIES_TABLE (latin_name, common_name, tld) VALUES ";

         while (($line = fgetcsv($handle)) !== FALSE){
            
			$comm_name = "'".simpleFilter($line[0])."'";
			$lati_name = "'".simpleFilter($line[2])."'";
            $tld_des = "'".simpleFilter($line[1])."'";

            $insert .= "($lati_name, $comm_name, $tld_des), ";
         }

         $insert = preg_replace('/, $/', "", $insert);
         fclose($handle);
         $res = query($insert);
		 
		 if($res){
		 
			echo 'The data was succesfully entered in the database <br />';
			
			echo '<a href="javascript:history.go(-1)">Go back</a>';
		 }
		 
		 
      }
      else
      {
         die("Unable to open the uploaded file: " . $_FILES['HostSpeciesFile']['name']);
      }
   }

?>
