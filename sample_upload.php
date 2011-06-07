<?php include("sqlFunctions.php"); ?>

<?php
   if ($_POST['formName'] == 'species')
   {
      if (!$_POST['CommonName'] || !$_POST['LatinName'] || !$_POST['TLD'])
      {
         die("Cannot upload species, not all fields present.");
      }
      
      $insert = "INSERT IGNORE INTO host_species (common_name, latin_name, tld) VALUES ";
      $insert = $insert."('".simpleFilter($_POST['CommonName'])."', '".simpleFilter($_POST['LatinName'])."', '".simpleFilter($_POST['TLD'])."')";

      //echo "$insert<br />";

      query($insert);
   }
   elseif ($_POST['formName'] == 'host')
   {
      if (!$_POST['Species'] || !$_POST['Name'])
      {
         die("Cannot upload host, not all fields present.");
      }
      
      $insert = "INSERT IGNORE INTO host (host_name, host_species) VALUES ";
      $insert = $insert."('".simpleFilter($_POST['Name'])."', '".simpleFilter($_POST['Species'])."')";

      echo "$insert<br />";

      query($insert);
   }
   elseif ($_POST['formName'] == 'sample')
   {
      if (!$_POST['Species'] || !$_POST['Name'] || !$_POST['Id'] || !$_POST['Date'])
      {
         die("Cannot upload sample, not all fields present.");
      }

      //Only accecp valid dates.
      //TODO: move this to js
      if (!preg_match("/^\d\d-\d\d-\d\d\d\d$/", $_POST['Date']))
      {
         die("Invalid date format.");
      }
      
      $species = simpleFilter($_POST['Species']);
      $name = simpleFilter($_POST['Name']);
      $id = simpleFilter($_POST['Id']);

      $date = "STR_TO_DATE('".simpleFilter($_POST['Date'])."', '%m-%d-%Y')";

      if ($_POST['Location'] == "")
      {
         $location = 'NULL';
      }
      else
      {
         $location = "'".simpleFilter($_POST['Location'])."'";
      }

      $insert = "INSERT IGNORE INTO sample (sample_id, sample_date, sample_location, host_name, host_species) VALUES ";
      $insert = $insert."('$id', $date, $location, '$name', '$species')";

      //echo "$insert<br />";

      query($insert);
   }
   if ($_POST['formName'] == 'isolate')
   {
      if (!$_POST['IsolateName'] || !$_POST['Freezer'] || !$_POST['FreezeDate'] || !$_POST['SampleID'])
      {
         die("Cannot upload isolate, not all fields present.");
      }

      //Only accecp valid dates.
      //TODO: move this to js
      if (!preg_match("/^\d\d-\d\d-\d\d\d\d$/", $_POST['FreezeDate']))
      {
         die("Invalid date format.");
      }
      
      $isolate = simpleFilter($_POST['IsolateName']);
      $freezer = simpleFilter($_POST['Freezer']);

      $date = "STR_TO_DATE('".simpleFilter($_POST['FreezeDate'])."', '%m-%d-%Y')";

      $sample = simpleFilter($_POST['SampleID']);
      $hostName = "(SELECT host_name from sample where sample_id = '".simpleFilter($_POST['SampleID'])."')";
      $hostSpecies = "(SELECT host_species from sample where sample_id = '".simpleFilter($_POST['SampleID'])."')";

      if (isset($_POST['Pyroprinted']) && $_POST['Pyroprinted'] == 'pyro')
      {
         $pyro = 'TRUE';
      }
      else
      {
         $pyro = 'FALSE';
      }

      if ($_POST['Technician'] == "")
      {
         $tech = 'NULL';
      }
      else
      {
         $tech = "'".simpleFilter($_POST['Technician'])."'";
      }

      $insert = "INSERT IGNORE INTO isolate (isolate_name, freezer_location, freezing_date, isolate_technician, is_pyroprinted, host_name, host_species, sample_id) VALUES ";
      $insert = $insert."('$isolate', '$freezer', $date, $tech, $pyro, $hostName, $hostSpecies, '$sample')";

      //echo "$insert<br />";

      query($insert);
   }
   else
   {
      echo "TEST ELSE";
   }
?>
