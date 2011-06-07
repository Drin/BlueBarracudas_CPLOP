<?php include("sqlFunctions.php"); ?>

<?php

$ISOLATE_TABLE = 'isolate';


if ($_FILES['freezerUploadFile']){
      
	  if ($_FILES['freezerUploadFile']['error'] != 0){
         die("Error uploading file: ".$_FILES['freezerUploadFile']['name']."\n".$_FILES['freezerUploadFile']['error']);
      }

      if (($handle = fopen($_FILES['freezerUploadFile']['tmp_name'], "r")) !== FALSE){
      
         fgetcsv($handle);

         $insert = "INSERT IGNORE INTO $ISOLATE_TABLE (isolate_name, freezer_location, 
					freezer_num, box_number, box_position, freezing_date, isolate_technician, 
					is_pyroprinted, host_name,host_species, sample_id) VALUES ";
         while (($line = fgetcsv($handle)) !== FALSE){
		 
            $IsolateID = "'".simpleFilter($line[0])."'";
            $Freezerlocation = "'".simpleFilter($line[1])."'";
            $FreezingDate = "'".simpleFilter($line[2])."'";
            $Hostspecies = "'".simpleFilter($line[3])."'";
            $Hostid = "'".simpleFilter($line[4])."'";
            $Samplenumber = "'".simpleFilter($line[5])."'";
			$SamplingDate = "'".simpleFilter($line[6])."'";
            $Site = "'".simpleFilter($line[7])."'";
            $technician = "'".simpleFilter($line[8])."'";
            $Pyroprintdate = "'".simpleFilter($line[9])."'";
            
            
            $insert .= "($IsolateID, $Freezerlocation,
						 NULL,NULL,NULL, $FreezingDate, $technician,
						 $Pyroprintdate,$Hostid,$Hostspecies,$Samplenumber), ";   
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
         die("Unable to open the uploaded file: " . $_FILES['freezerUploadFile']['name']);
      }

   }
   
?>