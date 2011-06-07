<?php
/*
 * This file loads all the meta info about the pyrogram with id = _GET['pyroNum']
 */
?>

<?php include("../sqlFunctions.php"); ?>

<?php
   $res = query("SELECT * from pyrogram where pyrogram_num = ".$_GET['pyroNum']);

   if ($res)
   {
      $row = mysqli_fetch_array($res);

      $pyrogramNum = $row['pyrogram_num'];
      $wellID = $row['well_id'];
      $isolateID = $row['isolate_name'];
      $machineID = $row['machine_id'] ? $row['machine_id'] : '<b>MISSING</b>';
      $pyroDate = $row['pyrogram_date'];
      $pcrDate = $row['pcr_date'] ? $row['pcr_date'] : "<b>MISSING</b>";
      $pcrMachine = $row['pcr_machine'] ? $row['pcr_machine'] : "<b>MISSING</b>";
      $tech = $row['pyroprint_technician'] ? $row['pyroprint_technician'] : "<b>MISSING</b>";
      $xmlFile = $row['xml_file'];
      $qc = $row['quality_control'] == 1 ? 'YES' : '<b>NO</b>';

      $protoRes = 
      query("SELECT proto.name, disp.dispensation_name, forward.sequence_name as forward_primer_name, 
       rev.sequence_name as reverse_primer_name, seq.sequence_name as sequence_primer_name, 
       proto.region_name from protocol proto, primer forward, primer rev, primer seq, 
       dispensation_sequence disp WHERE name = '".$row['protocol']."' AND proto.dispensation_id = 
       disp.dispensation_id AND forward.primer_id = proto.forward_primer_id AND 
       rev.primer_id = proto.reverse_primer_id AND seq.primer_id = proto.sequence_primer_id;");

      if ($protoRes)
      {
         $protoRow = mysqli_fetch_array($protoRes);

         $protoName = $protoRow["name"];
         $region = $protoRow["region_name"];
         $disp = $protoRow["dispensation_name"];
         $f_primer = $protoRow["forward_primer_name"];
         $r_primer = $protoRow["reverse_primer_name"];
         $s_primer = $protoRow["sequence_primer_name"];
      }
      else
      {
         $protoName = "<b>NOT FOUND</b>";
         $region = "<b>NOT FOUND</b>";
         $disp = "<b>NOT FOUND</b>";
         $f_primer = "<b>NOT FOUND</b>";
         $r_primer = "<b>NOT FOUND</b>";
         $s_primer = "<b>NOT FOUND</b>";
      }
   }
?>

<h2><?php echo "Pyrogram: $xmlFile ~ $wellID" ?></h2>

<table>
   <tr>
      <td width='400'>
         <table cellpadding="10">
            <tr><td>
               Well ID: <?php echo $wellID ?>
            </td></tr>
            <tr><td>
               Isolate ID: <?php echo $isolateID ?>
            </td></tr>
            <tr><td>
               Pyrogram Date: <?php echo $pyroDate ?>
            </td></tr>
            <tr><td>
               Machine ID: <?php echo $machineID ?>
            </td></tr>
            <tr><td>
               PCR Date:  <?php echo $pcrDate ?>
            </td></tr>
            <tr><td>
               PCR Machine: <?php echo $pcrMachine ?>
            </td></tr>
            <tr><td>
               Pyroprint Technician: <?php echo $tech ?>
            </td></tr>
            <tr><td>
               XML File: <?php echo $xmlFile ?>
            </td></tr>
            <tr><td>
               Passes Quality Control: <?php echo $qc ?>
            </td></tr>

            <tr><td>
               <a href="javascript:toggleMenu('protocol', '../imgs');" id=protocol_bar><img src= '../imgs/down.jpg'/></a>
               Protocol: <?php echo $protoName ?>
            <table id=protocol style="display:none">
               <tr><td>
                  Region Name: <?php echo $region ?>
               </td></tr>
               <tr><td>
                  Dispensation Sequence: <?php echo $disp ?>
               </td></tr>
               <tr><td>
                  Forward Primer: <?php echo $f_primer ?>
               </td></tr>
               <tr><td>
                  Reverse Primer ID: <?php echo $r_primer ?>
               </td></tr>
               <tr><td>
                  Sequence Primer: <?php echo $s_primer ?>
               </td></tr>
            </table>
            </td></tr>
         </table>
      </td>
      <td>
         <input type='button' value='Find Closest Species' 
          onClick="findClosestSpecies(<?php echo $pyrogramNum; ?>, 'closestSpecies');" />
         <div id='closestSpecies'>
         </div>
      </td>
   </tr>
</table>

</body>
</html>
