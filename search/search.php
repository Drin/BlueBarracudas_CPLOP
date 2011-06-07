<?php $thisPage = "search"; ?>

<?php include("../meta.php");?>
<link rel="StyleSheet" href="../css/tablesorter.css" type="text/css" />
<link rel="StyleSheet" href="../css/pagination.css" type="text/css" />
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<script type="text/javascript" src="../js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="../js/jquery-table-me.js"></script>

<script type="text/javascript" src="search.js"></script>
</head>

<body>

<?php include("../header.php");?>

<h2>Search Pyrograms</h2>

<table>
<tr>
<td width="600">
<form>
   Search Sequence: <input id='searchSequence' type='text'> <br />
   Search Values: <input id='searchValues' type='text'> <br />
   Tolerence: <input id='tolerence' type='text' value='1'> <br />
   Comparison Method: 
    <input type='radio' id="radio-direct" name='comparisonMethod' value='direct'>Direct 
    <input type='radio' id="radio-pearson" name='comparisonMethod' value='pearson' checked>Pearson Correlation 
    <br />
   <input type="button" value='Search' onClick="doSearch();"> <br />
</form>
</td>
<td>
<img id="loadingImage" style="visibility:hidden" src="<?php echo "$imagePath/loading.gif"; ?>" alt="Loading..." />
</td>
</tr>
</table>

<br />
<hr />
<br />

<table border='1' class='paged sortable' id="sortPyroTable">
   <thead><tr>
      <th>Match %</th>
      <th>XML File</th>
      <th>Well</th>
      <th>Pyro Date</th>
      <th>Protocol</th>
      <th>Isolate Name</th>
      <th>Quality Control</th>
   </tr></thead>
   <tbody id="pyros"></tbody>
</table>

</body>
</html>
