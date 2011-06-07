<?php
/*
 * This file loads all the meta info about the pyrogram with id = _GET['pyroNum']
 */
?>



<?php include("../sqlFunctions.php"); ?>

<?php
   $res = query("SELECT * from isolate where isolate_name = '".$_GET['isolateName']."'");

   if ($res)
   {
      $row = mysqli_fetch_array($res);

      $tech = $row['isolate_technician'];
      $freezDate = $row['freezing_date'];
      $sample = $row['sample_id'];
      $host = $row['host_name'];
      $species = $row['host_species'];
      $pyroPrinted = $row['is_pyroprinted'];
   }
?>

<table cellpadding="10">
   <tr><td>
      Isolate Technician: <?php echo $tech ?>
   </td></tr>
   <tr><td>
      Freezing Date: <?php echo $freezDate ?>
   </td></tr>
   <tr><td>
      Sample ID: <?php echo $sample ?>
   </td></tr>
   <tr><td>
      Host ID: <?php echo $host ?>
   </td></tr>
   <tr><td>
      Host Species:  <?php echo $species ?>
   </td></tr>
   <tr><td>
      <?php
         if ($pyroPrinted == 1) {
            echo "<span class=\"true_text\"> Has Been </span>\n".
                 "<span class=\"plain_text\"> Pyroprinted </span>";
         }
         else if ($pyroPrinted == 0) {
            echo "<span class=\"false_text\"> Has Not Been </span>\n".
                 "<span class=\"plain_text\"> Pyroprinted </span>";
         }
      ?>
   </td></tr>
</table>

</body>
</html>
