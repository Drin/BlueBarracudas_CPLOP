<?php $thisPage = "protocol-bulk"; ?>
<?php $basePath = "."; ?>

<?php include("meta.php"); ?>
<script type='text/javascript' src="<?php echo "$jsBase/" ?>jquery.form.js"></script>

<script type='text/javascript'>
   var fileID = 2;

   function addFile(divId, nameBase, className)
   {
      $('#' + divId).append(
       '<input type="file" class="' + className + '" id="' + nameBase + '-' + fileID++ + 
       '" name="' + nameBase + '-' + fileID++ + '" size="40" maxlength="100000" /> <br />');
   }

   $(document).ready(function() 
   { 
      var options = 
      {
         beforeSubmit : function(arr, form, options)
         {
            return requireFieldsNoSubmit(form[0].id);
         },
         success : function(data)
         {
            if (data.match(/error/i))
            {
               alert("Error: " + data);
            }
            else
            {
               alert("success");
            }
         }
      }
      
      //Make all submits ajax 
      $('#hostSpecies').ajaxForm(options); 
      $('#freezerStock').ajaxForm(options); 
      $('#primers').ajaxForm(options); 
      $('#disp').ajaxForm(options); 
      $('#pyro').ajaxForm(options); 
      $('#xml').ajaxForm(options); 

      //Add the initial file selectors
      addFile('xmlFileArea', 'xmlFile', 'xmlFile');
      addFile('pyroFileArea', 'pyroFile', 'pyroFile');
      addFile('dispFileArea', 'dispensationFile', 'dispensationFile');
      addFile('primerFileArea', 'primerFile', 'primerFile');
      addFile('freezerFileArea', 'freezerFile', 'freezerFile');
      addFile('speciesFileArea', 'speciesFile', 'speciesFile');
   });
</script>

</head>

<body>

<?php include("header.php"); ?>

<h1>CSV/XML File Insertion</h1>
<h5>Host Species CSV </h5>
<div>
   <form name="hostSpecies" id="hostSpecies" enctype="multipart/form-data" action="csvUploader.php" method="POST">
      File to upload:

      <div id='speciesFileArea'>
      </div>

      <button type="button" onClick="addFile('speciesFileArea', 'speciesFile', 'speciesFile');">Add Another File </button> <br />
      <input type="submit" value="Upload" />
   </form>
</div>

<h5>Freezer Stock CSV </h5>
<div>
   <form name="freezerStock" id="freezerStock" enctype="multipart/form-data" action="csvUploader.php" method="POST">
      File to upload:

      <div id='freezerFileArea'>
      </div>

      <button type="button" onClick="addFile('freezerFileArea', 'freezerFile', 'freezerFile');">Add Another File </button> <br />
      <input type="submit" value="Upload" />
   </form>
</div>

<h5>Primers CSV </h5>
<div>
   <form name="primers" id="primers" enctype="multipart/form-data" action="csvUploader.php" method="POST">
      Primer file to upload:

      <div id='primerFileArea'>
      </div>

      <button type="button" onClick="addFile('primerFileArea', 'primerFile', 'primerFile');">Add Another File </button> <br />
      <input type="submit" value="Upload" />
   </form>
</div>

<h5>Dispensation  CSV </h5>
<div>
   <form name="disp" id="disp" enctype="multipart/form-data" action="csvUploader.php" method="POST">
      Disp file to upload: 

      <div id='dispFileArea'>
      </div>

      <button type="button" onClick="addFile('dispFileArea', 'dispensationFile', 'dispensationFile');">Add Another File </button> <br />
      <input type="submit" value="Upload" />
   </form>
</div>

<h5>Pyroprints CSV </h5>
<div>
   <p>
   <form name="pyro" id="pyro" enctype="multipart/form-data" action="csvUploader.php" method="POST">
      Pyroprint file to Upload: 

      <div id='pyroFileArea'>
      </div>

      <button type="button" onClick="addFile('pyroFileArea', 'pyroFile', 'pyroFile');">Add Another File </button> <br />
      <input type="submit" value="Upload" />
   </form>
   </p>
</div>

<h5> XML Files </h5>
<div id="xmlDiv">
   <p id="xmlDescrip">
      Specify the XML files you wish to upload here.<br />
      Click Add another file to upload more than one.
   </p>
   <form name="xml" id="xml" enctype="multipart/form-data" action="xmlUploader.php" method="POST">
      <div id='xmlFileArea'>
      </div>
      
      <div id="xmlControls">
         <button type="button" onClick="addFile('xmlFileArea', 'xmlFile', 'xmlFile');">Add Another File </button> <br />
         <input type="submit" value="Upload" />
      </div>
   </form>
</div>

</body>
</html>
