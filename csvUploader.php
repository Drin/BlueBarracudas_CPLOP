<?php include("sqlFunctions.php"); ?>

<?php
   $PRIMER_TABLE = 'primer';
   $DISPENSATION_TABLE = 'dispensation_sequence';
   $PYROPRINT_TABLE = 'pyrogram';

   //Big file precaution?
   //set_time_limit(0);

   foreach ($_FILES as $fileName => $file)
   {
      if ($file['name'] !== '')
      {
         if ($file['error'] !== 0 || $file['size'] == 0)
         {
            die("Error uploading csv file: ".$file['name'].
               "\nError: ".$file['error']);
         }

         //Match primer
         if (preg_match('/primer/i', $fileName))
         {
            if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) 
            {
               //Skip the first (header) line.
               fgetcsv($handle);

               $insert = "INSERT IGNORE INTO $PRIMER_TABLE (primer_id, sequence, sequence_name) VALUES ";

               while (($line = fgetcsv($handle)) !== FALSE)
               {
                  $primerName = "'".simpleFilter($line[0])."'";
                  $primerString = "'".simpleFilter($line[1])."'";

                  $insert .= "(NULL, $primerString, $primerName), ";
               }

               //Strip off the final ', '
               $insert = preg_replace('/, $/', "", $insert);
               
               //echo "$insert<br />";

               fclose($handle);
               
               query($insert);
            }
            else
            {
               die("Unable to open the uploaded primer file: " . $file['name']);
            }
         }
         elseif (preg_match('/dispensation/i', $fileName))
         {
            if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) 
            {
               //Skip the first (header) line.
               fgetcsv($handle);

               $insert = "INSERT IGNORE INTO $DISPENSATION_TABLE (dispensation_id, dispensation_sequence, dispensation_name) VALUES ";

               while (($line = fgetcsv($handle)) !== FALSE)
               {
                  $dispName = "'".simpleFilter($line[0])."'";
                  $dispString = "'".simpleFilter($line[1])."'";

                  $insert .= "(NULL, $dispString, $dispName), ";
               }

               //Strip off the final ', '
               $insert = preg_replace('/, $/', "", $insert);
               
               //echo "$insert<br />";

               fclose($handle);
               
               query($insert);
            }
            else
            {
               die("Unable to open the uploaded dispensation file: " . $file['name']);
            }
         }
         elseif (preg_match('/pyro/i', $fileName))
         {
            if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) 
            {
               //don't upload table column names
               fgetcsv($handle);

               $insert = "REPLACE INTO $PYROPRINT_TABLE (pyrogram_num, well_id, pyrogram_date, machine_id, pcr_date, isolate_name, pyroprint_technician, xml_file, protocol) VALUES ";
               while (($line = fgetcsv($handle)) !== FALSE)
               {
                  $pyroDate = "STR_TO_DATE('".simpleFilter($line[0])."', '%m/%d/%Y')";
                  $isolateName = "'".simpleFilter($line[2])."'";
                  $pcrDate = "STR_TO_DATE('".simpleFilter($line[3])."', '%m/%d/%Y')";
                  $pyroMachina = "'".simpleFilter($line[4])."'";
                  $well = "'".simpleFilter($line[5])."'";
                  $dispensation = "'".simpleFilter($line[6])."'";
                  $tech = "'".simpleFilter($line[7])."'";
                  $region = "'".simpleFilter($line[8])."'";
                  $pyroFileName = "'".simpleFilter($line[9])."'";
                  $forward = "'".simpleFilter($line[10])."'";
                  $reverse = "'".simpleFilter($line[11])."'";
                  $sequence = "'".simplefilter($line[12])."'";

                  $protocol = "(SELECT proto.name from protocol proto, dispensation_sequence disp, primer forward, primer rev, primer seq 
                  WHERE disp.dispensation_name = $dispensation AND forward.sequence_name = $forward AND rev.sequence_name = $reverse AND seq.sequence_name = $sequence AND disp.dispensation_id = proto.dispensation_id AND forward.primer_id = proto.forward_primer_id AND rev.primer_id = proto.reverse_primer_id AND seq.primer_id = proto.sequence_primer_id AND region_name = $region)";
                  
                  $query = $insert."(NULL, $well, $pyroDate, $pyroMachina, $pcrDate, $isolateName, $tech, $pyroFileName, $protocol)";   
                  query($query);
               }

               //Strip off the final ', '
               $insert = preg_replace('/, $/', "", $insert);
               
               fclose($handle);
            }
            else
            {
               die("Unable to open the uploaded pyro file: " . $file['name']);
            }
         }
         elseif (preg_match('/species/i', $fileName))
         {
            if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) 
            {
               //Skip the first (header) line.
               fgetcsv($handle);

               $insert = "INSERT IGNORE INTO host_species (common_name, tld, latin_name) VALUES ";

               while (($line = fgetcsv($handle)) !== FALSE)
               {
                  $commonName = "'".simpleFilter($line[0])."'";
                  $tld = "'".simpleFilter($line[1])."'";
                  
                  $latinName = "'".simpleFilter($line[2])."'";
                  if ($latinName == "''")
                  {
                     $latinName = "NULL";
                  }

                  if ($commonName != "''")
                  {
                     $insert .= "($commonName, $tld, $latinName), ";
                  }
               }

               //Strip off the final ', '
               $insert = preg_replace('/, $/', "", $insert);
               
               //echo "$insert<br />";

               fclose($handle);
               
               query($insert);
            }
            else
            {
               die("Unable to open the uploaded host species file: " . $file['name']);
            }
         }
         elseif (preg_match('/freezer/i', $fileName))
         {
            if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) 
            {
               //Skip the first (header) line.
               fgetcsv($handle);

               $hostInsert = "INSERT IGNORE INTO host (host_name, host_species) VALUES ";
               $sampleInsert = "INSERT IGNORE INTO sample (sample_id, sample_date, sample_location, host_name, host_species) VALUES ";
               $isoInsert = "INSERT IGNORE INTO isolate (isolate_name, freezer_location, freezing_date, isolate_technician, is_pyroprinted, host_name, host_species, sample_id) VALUES ";

               while (($line = fgetcsv($handle)) !== FALSE)
               {
                  $name = "'".simpleFilter($line[0])."'";
                  
                  $freezer = "'".simpleFilter($line[1])."'";
                  if ($freezer == "''")
                  {
                     $freeze = "NULL";
                  }
                  
                  $freezeDate = "STR_TO_DATE('".simpleFilter($line[2])."', '%m/%d/%Y')";

                  $hostSpecies = "'".simpleFilter($line[3])."'";
                  $hostName = "'".simpleFilter($line[4])."'";

                  $sampleId = "'".simpleFilter($line[5])."'";
               
                  if (simpleFilter($line[6]) == "")
                  {
                     $sampleDate = "NULL";
                  }
                  else
                  {
                     $sampleDate = "STR_TO_DATE('".simpleFilter($line[6])."', '%m/%d/%Y')";
                  }
                  
                  $sampleLocation = "'".simpleFilter($line[7])."'";

                  $isoTech = "'".simpleFilter($line[8])."'";

                  //This is pyro date, but that info comes in other file
                  // will just use this to specify if it has been pyro'd.
                  if (simpleFilter($line[9]) == "")
                  {
                     $isPyro = 'FALSE';
                  }
                  else
                  {
                     $isPyro = 'TRUE';
                  }
               
                  $hostQuery = "$hostInsert ($hostName, $hostSpecies)";
                  $sampleQuery = "$sampleInsert ($sampleId, $sampleDate, $sampleLocation, $hostName, $hostSpecies)";
                  $isoQuery = "$isoInsert ($name, $freezer, $freezeDate, $isoTech, $isPyro, $hostName, $hostSpecies, $sampleId)";

                  query($hostQuery);
                  query($sampleQuery);
                  query($isoQuery);
               }

               fclose($handle);
            }
            else
            {
               die("Unable to open the uploaded freezer stock file: " . $file['name']);
            }
         }
      }
   }
?>
