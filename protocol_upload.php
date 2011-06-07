<?php include("sqlFunctions.php"); ?>

<?php
   if ($_POST['formName'] == 'primer')
   {
      if (!$_POST['PrimerName'] || !$_POST['PrimerSequence'])
      {
         die("Cannot upload primer, not all fields present.");
      }
      
      $name = simpleFilter($_POST['PrimerName']);
      $seq = simpleFilter($_POST['PrimerSequence']);

      $insert = "INSERT IGNORE INTO primer (primer_id, sequence, sequence_name) VALUES ";
      $insert = $insert."(NULL, '$seq', '$name')";

      //echo "$insert<br />";

      query($insert);
   }
   elseif ($_POST['formName'] == 'disp')
   {
      if (!$_POST['DispensationName'] || !$_POST['DispensationSequence'])
      {
         die("Cannot upload dispensation sequence, not all fields present.");
      }
      
      $name = simpleFilter($_POST['DispensationName']);
      $seq = simpleFilter($_POST['DispensationSequence']);

      $insert = "INSERT IGNORE INTO dispensation_sequence (dispensation_id, dispensation_sequence, dispensation_name) VALUES ";
      $insert = $insert."(NULL, '$seq', '$name')";

      //echo "$insert<br />";

      query($insert);
   }
   elseif ($_POST['formName'] == 'fullProtocol')
   {
      if (!$_POST['ProtocolName'] || !$_POST['Reverse'] || !$_POST['Forward'] || !$_POST['Sequence'] || !$_POST['Dispensation'])
      {
         die("Cannot upload full protocol, not all fields present.");
      }
      
      $name = simpleFilter($_POST['ProtocolName']);

      $disp = "(SELECT dispensation_id from dispensation_sequence where dispensation_name = '".simpleFilter($_POST['Dispensation'])."')";

      $forward = "(SELECT primer_id from primer where sequence_name = '".simpleFilter($_POST['Forward'])."')";
      $rev = "(SELECT primer_id from primer where sequence_name = '".simpleFilter($_POST['Reverse'])."')";
      $seq = "(SELECT primer_id from primer where sequence_name = '".simpleFilter($_POST['Sequence'])."')";

      $reg = simpleFilter($_POST['Region']);
      
      $insert = "INSERT IGNORE INTO protocol (name, dispensation_id, forward_primer_id, reverse_primer_id, sequence_primer_id, region_name) VALUES ";
      $insert = $insert."('$name', $disp, $forward, $rev, $seq, '$reg')";

      //echo "$insert<br />";

      query($insert);
   }
?>
