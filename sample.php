<?php $thisPage = "upload-sample"; ?>
<?php $basePath = "."; ?>

<?php include("meta.php"); ?>
</head>

<body>

<?php include("header.php"); ?>

<h1>Upload<h1>

<h3>Host Species</h3>
<form name="species" id="species" action="sample_upload.php" method="POST">
   <input name="formName" type="hidden" value="species">
   Common Name <input name='CommonName' title="req" type=text><br />
   Latin Name <input name='LatinName' title="req" type=text><br />
   TLD <input name='TLD' title="req" type=text><br />
   <input type="button" value="Submit" onClick="requireFields('species');">
</form>

<h3>Host</h3>
<form name="host" id="host" action="sample_upload.php" method="POST">
   <input name="formName" type="hidden" value="host">
   Species <input name="Species" title="req" type=text> <br />
   Name <input name="Name" title="req" type=text><br />
   <input type="button" value="Submit" onClick="requireFields('host');">
</form>

<h3>Sample</h3>
<form name="sample" id="sample" action="sample_upload.php" method="POST">
   <input name="formName" type="hidden" value="sample">
   Host Species <input name="Species" title="req" type=text><br />
   Host Name <input name="Name" title="req" type=text><br />
   ID <input name="Id" title="req" type=text><br />
   Date (MM-DD-YYYY) <input name="Date" title="req" type=text><br />
   Location <input name="Location" type=text><br />
   <input type="button" value="Submit" onClick="requireFields('sample');">
</form>

<h3>Isolate</h3>
<form name="isolate" id="isolate" action="sample_upload.php" method="POST">
   <input name="formName" type="hidden" value="isolate">
   Isolate Name <input name="IsolateName" title="req" type=text><br />
   Freezer <input name="Freezer" title="req" type=text><br />
   Date of Freeze (MM-DD-YYYY)<input name="FreezeDate" title="req" type=text><br />
   Technician <input name="Technician" type=text><br />
   Sample ID <input name="SampleID" title="req" type=text><br />
   <input name="Pyroprinted" type="checkbox" value="pyro"> Pyroprinted<br />
   <input type="button" value="Submit" onClick="requireFields('isolate');">
</form>
</body>
</html>
