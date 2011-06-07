<?php $thisPage = "upload-protocol"; ?>
<?php $basePath = "."; ?>

<?php include("meta.php"); ?>
<script type='text/javascript' src="<?php echo "$jsBase/" ?>jquery.form.js"></script>

<script type='text/javascript' src="<?php echo "$jsBase/" ?>dropDown.js"></script>

<script type='text/javascript'>
   $(document).ready(function() 
   { 
      //Fill the drop down menus
      fillDropDowns();
      
      var options = 
      {
         beforeSubmit : function(arr, form, options)
         {
            return requireFieldsNoSubmit(form[0].id);
         },
         success : function(data)
         {
            alert("success!" + data);
         }
      }
      
      //Make all submits ajax 
      $('#primer').ajaxForm(options); 
      $('#disp').ajaxForm(options); 
      $('#fullProtocol').ajaxForm(options); 
   });
</script>

</head>

<body>

<?php include("header.php"); ?>
     
<h1>Protocol Insertion</h1>

<h2>Single Primer</h2>
<form name="primer" id="primer" action="protocol_upload.php" method="POST">
   <input name="formName" type="hidden" value="primer">
   Primer Name <input name="PrimerName" title="req" type=text> <br />
   Primer Sequence <input name="PrimerSequence" title="req" type=text><br />
   <input type="submit" value="Submit" />
</form>

<h2>Single Dispensation Sequence</h2>
<form name="disp" id="disp" action="protocol_upload.php" method="POST">
   <input name="formName" type="hidden" value="disp">
   Dispensation Name <input name="DispensationName" title="req" type=text> <br />
   Dispensation Sequence <textarea cols=44 rows=3 title="req" name="DispensationSequence">Sequence here</textarea><br />
   <input type="submit" value="Submit" />
</form>

<h2>Full Protocol</h2>
<form name="fullProtocol" id="fullProtocol" action="protocol_upload.php" method="POST">
   <input name="formName" type="hidden" value="fullProtocol">
   
   Protocol Name <input title="req" type="text" name="ProtocolName"> <br /> 
   
   Dispensation Sequence Name <input id='dispSeq' name="Dispensation" title="req" type=text>
    <select class='database-fill' id='disp_drop' query='dispensation_sequence.dispensation_name' 
     onChange="fillDropText('disp_drop', 'dispSeq');"></select><br />
   
   Forward Primer Name <input id="fPrimer" title="req" type="text" name="Forward">
    <select class='database-fill' id='fPrimer_drop' query='primer.sequence_name' 
     onChange="fillDropText('fPrimer_drop', 'fPrimer');"></select><br />
   
   Reverse Primer Name <input id='rPrimer' title="req" type="text" name="Reverse"> 
    <select class='database-fill' id='rPrimer_drop' query='primer.sequence_name' 
     onChange="fillDropText('rPrimer_drop', 'rPrimer');"></select><br />
   
   Sequence Primer Name <input id='sPrimer' title="req" type="text" name="Sequence"> 
    <select class='database-fill' id='sPrimer_drop' query='primer.sequence_name' 
     onChange="fillDropText('sPrimer_drop', 'sPrimer');"></select><br />
   
   Region Name <input title="req" id='region' type="text" name="Region">
    <select class='database-fill' id='region_drop' query='protocol.region_name' 
     onChange="fillDropText('region_drop', 'region');"></select><br />
   
   <input type="submit" value="Submit" />
</form>
</body>
</html>
