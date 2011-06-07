<?php $thisPage = "browseProtocol"; ?>
<?php $basePath = "'"; ?>
<?php include("sqlFunctions.php"); ?>

<?php include('meta.php'); ?>
</head>

<?php include("functions.php"); ?>

<body>
<?php include("header.php"); ?>
    <h1> Browse Protocols </h1>

<?php $question = "select name, region_name, forward.sequence_name as forward, reverse.sequence_name as reverse, sequence.sequence_name as sequence, dispensation_name as dispensation from protocol inner join primer as forward on protocol.forward_primer_id=forward.primer_id inner join primer as reverse on reverse.primer_id=protocol.reverse_primer_id inner join primer as sequence on protocol.sequence_primer_id=sequence.primer_id natural join dispensation_sequence;";
$protocolArray = query($question);
?>

<form>
    <table border="1" class="sortable">
        <tr>
           <th>Name</th>
           <th>Dispensation</th>
           <th>Forward Primer </th>
           <th>Reverse Primer </th>
           <th>Sequence Primer </th>
           <th>Region Name </th>
        </tr>

      <?php while($row = mysqli_fetch_array($protocolArray))
      {
      echo "<td nowrap=\"nowrap\">".$row['name']."</td>";
      echo "<td nowrap=\"nowrap\">".($row['dispensation'])."</td>";
      echo "<td nowrap=\"nowrap\">".$row['forward']."</td>";
      echo "<td nowrap=\"nowrap\">".$row['reverse']."</td>";
      echo "<td nowrap=\"nowrap\">".$row['sequence']."</td>";
      echo "<td nowrap=\"nowrap\">".$row['region_name']."</td>
      </tr>";
      }
 ?>

</form>

</body>
</html>
