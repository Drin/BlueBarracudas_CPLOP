<?php 
   /**
    * This file loads the graph for the pyrogram with id = _GET['pyroNum']
    */
?>

<?php include("../sqlFunctions.php");?>

<?php
/*
 *The theme of the graph. (grey, grid, dark-blue, dark-green).
 *<script type="text/javascript" src="/js/themes/gray.js"></script>
 */
?>

<?php
   $ID = $_GET['pyroNum'];

   $res = query("SELECT peak_value, nucleotide FROM pyrogram_data_point WHERE pyrogram_num = $ID ORDER BY position");
   
   echo '<script type="text/javascript">'."\n";

   echo 'var values = new Array();'."\n";
   echo 'var nucleotides = new Array();'."\n";

   $max = 0;

   while ($row = mysqli_fetch_array($res))
   {
      echo "values.push(" . $row['peak_value'] . ");\n";
      echo "nucleotides.push('" . $row['nucleotide'] . "');\n";

      if ($row['peak_value'] > $max)
      {
         $max = $row['peak_value'];
      }
   }

   echo "$(document).ready(function(){makeGraph(nucleotides, values, 'chart-container-1', 'Pyrogram!', $max);});\n";

   echo '</script>';
?>

Click and drag mouse to zoom.
<div id="chart-container-1" style="width: 100%; height: 400px"></div>
