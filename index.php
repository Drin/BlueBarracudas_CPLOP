<?php $thisPage = "main"; ?>
<?php $basePath = "."; ?>

<?php include("meta.php"); ?>
<link rel="StyleSheet" href="css/tablesorter.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo "$cssBase/" ?>index.css" media="screen">
</head>

<body>

<?php include("header.php"); ?>

<div id="index">
   <h1>CPLOP UI Prototype</h1>

   <div id="use_cases">
      <table border='1'>
         <tr>
            <th>Use Case</th>
            <th>Link</th>
         </tr>
         <tr>
            <td>UC 1-1 and 1-2</td>
            <td><?php echo "<a href=\"$webBase/bulk.php\">Bulk Insertion of CSV and/or XML</a>" ?></td>
         </tr>
         <tr>
            <td>UC 2-1</td>
            <td><?php echo "<a href=\"$webBase/sample.php\">Add Isolate to Freezer Stock</a>" ?></td>
         </tr>
         <tr>
            <td>UC 2-2</td>
            <td><?php echo "<a href=\"$webBase/bulk.php\">Add Pyrosequencing Data</a>" ?></td>
         </tr>
         <tr>
            <td>UC 2-3 and 2-4</td>
            <td><?php echo "<a href=\"$webBase/protocol.php\"> Add Protocol (dispensation sequence and primers)</a>" ?></td>
         </tr>
         <tr>
            <td>UC 3-1 and 3-2 </td>
            <td><?php echo "<a href=\"$webBase/browseStock.php\"> Browse/Filter Freezer Stock</a>" ?> </td>
         </tr>
         <tr>
            <td>UC 3-3, 3-4 and UC4 </td>
            <td><?php echo "<a href=\"$webBase/browsePyro.php\"> Browse/Filter Pyrograms (click on Pyrogram to view info)</a>" ?> </td>
         </tr>
         <tr>
            <td>UC 5 </td>
            <td><?php echo "<a href=\"$webBase/search/search.php\"> Search Inside a Pyrogram</a>" ?> </td>
         </tr>
         <tr>
            <td> UC 6-2, 6-3 and 6-4 </td>
            <td><?php echo "<a href=\"$webBase/match/match.php\"> Find matches of pyrograms against themselves or whole DB</a>" ?> </td>
         </tr>
         <tr>
            <td> UC 6-5 </td>
            <td><?php echo "<a href=\"$webBase/match/match.php\">Export match data to csv </a>" ?> </td>
         </tr>
      </table>
   </div>

      <br />
      <br />

      <div>
          <form action="clearAll.php" method="POST">
              <input type="Submit" value="Nuke Database!"/>  
          </form>
      </div>
</div>
   

</body>
</html>
