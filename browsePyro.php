<?php include("sqlFunctions.php"); ?>
<?php $thisPage = "browsePyro"; ?>
<?php $basePath = "."; ?>

<?php include('meta.php'); ?>

<link rel="StyleSheet" href="css/tablesorter.css" type="text/css" />
<link rel="StyleSheet" href="css/browsePyro.css" type="text/css" />

<script type="text/javascript" src="js/jquery.pagination.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="js/jquery-table-me.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
<script type="text/javascript" src="js/dropDown.js"></script>

<script type='text/javascript'>
   function matchEachother()
   {
      var url = "<?php echo $webBase; ?>/match/match.php?";
      var pyros = '';

      $('.selectPyro:checked').each(function()
      {
         pyros += (this.value + ",");
      });
      pyros = pyros.replace(/,$/, '');

      //Only match if there are actually pyros selected.
      if (pyros != '')
      {
         url += ("left=" + pyros);
         url += ("&right=" + pyros);

         document.location.href = url;
      }
   }

   function matchDatabase()
   {
      var url = "<?php echo $webBase; ?>/match/match.php?";
      var pyros = '';

      $('.selectPyro:checked').each(function()
      {
         pyros += (this.value + ",");
      });
      pyros = pyros.replace(/,$/, '');

      //Only match if there are actually pyros selected.
      if (pyros != '')
      {
         url += ("right=" + pyros);
         url += "&left=all";

         document.location.href = url;
      }
   }

   function matchNonEnv()
   {
      var url = "<?php echo $webBase; ?>/match/match.php?";
      var pyros = '';

      $('.selectPyro:checked').each(function()
      {
         pyros += (this.value + ",");
      });
      pyros = pyros.replace(/,$/, '');

      //Only match if there are actually pyros selected.
      if (pyros != '')
      {
         url += ("right=" + pyros);
         url += "&left=nonEnv";

         document.location.href = url;
      }
   }

   function matchEnv()
   {
      var url = "<?php echo $webBase; ?>/match/match.php?";
      var pyros = '';

      $('.selectPyro:checked').each(function()
      {
         pyros += (this.value + ",");
      });
      pyros = pyros.replace(/,$/, '');

      //Only match if there are actually pyros selected.
      if (pyros != '')
      {
         url += ("right=" + pyros);
         url += "&left=env,begin,end";

         document.location.href = url;
      }
   }

   function toggleCheck(classVal)
   {
      var checkVal;
      if (document.getElementById('checkButton').value == 'Check All')
      {
         document.getElementById('checkButton').value = 'Uncheck All';
         checkVal = true;
      }
      else
      {
         document.getElementById('checkButton').value = 'Check All';
         checkVal = false;
      }
      
      $('.selectPyro').each(function()
      {
         this.checked = checkVal;
      });
   }

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
            data = data.substring(data.indexOf('['), data.length);
            
            var str = "";
            var tableRow = Array();
            var currentRow = -1;

            /*
             * These variables are for managing table row construction
             */
            var checkBoxNdx = 0;
            var isolateNdx = 1;
            var dateNdx = 2;
            var protocolNdx = 3;
            var specieNdx = 4;
            var machineNdx = 5;

            //will be inserted into pyro_tableBody
            $('#pyro_tableBody').html('');

            JSON.parse(data, function (key, val) {
               if (key == "pyrogram_num") {
                  if (val != currentRow) {

                     if (currentRow != -1) {
                        if (window.console) {
                           //console.log('tableRow: ' + tableRow);
                        }

                        $('#pyro_tableBody').append('<tr onClick=\"linkPyro(' + val + ');\">' + tableRow.join('') + '</tr>');
                        tableRow = Array();
                     }

                     tableRow[checkBoxNdx] = "<td><input value='" + val + "' class='selectPyro' type='checkbox'></input></td>";
                     currentRow = val;
                  }
                  else {
                     alert('confusion');
                  }
               }
               //build the table rows
               if (key == "isolate_name") {
                  tableRow[isolateNdx] = '<td>' + val + '</td>';
               }
               else if (key == "pyrogram_date") {
                  tableRow[dateNdx] = '<td>' + val + '</td>';
               }
               else if (key == "protocol") {
                  tableRow[protocolNdx] = '<td>' + val + '</td>';
               }
               else if (key == "host_species") {
                  tableRow[specieNdx] = '<td>' + val + '</td>';
               }
               else if (key == "machine_id") {
                  tableRow[machineNdx] = '<td>' + val + '</td>';
               }
               else {
                  //nothing was there?
               }
            });

            //to get the last tuple into the table
            $('#pyro_tableBody').append('<tr onClick=\"linkPyro(' + val + ');\">' + tableRow.join('') + '</tr>');

            //$('#pyros').trigger("appendCache");
            $('#pyros').trigger("update"); 
         },
         dataType: 'JSON'
      }
      
      //Make all submits ajax 
      $('#pyrogramFilter').ajaxForm(options); 
      
   });
</script>

</head>

<?php include("functions.php"); ?>

<body>

<?php include("header.php"); ?>

<?php $fields = array('Sample', 'Isolate', 'Host', 'Host Species', 'Date Sequenced', 'Sequencing Person', 'Sequencing Machine', 'Sequencing Well', 'PCR Date', 'PCR Machine') ?>
<div id="pyrogram_header">
   <h2>Browse/Filter Pyrograms<h2>
</div>

<div id="pyrogram_filter">
   <form id="pyrogramFilter" name="pyrogramFilter" enctype="multipart/form-data" action="filter/filterPyrogram.php" method="POST">
      <div id="protocol_menu">
         <table>
            <thead><tr>
               <th>
                  <span class="param_label">
                     <a href="javascript:toggleMenu('protocol_tbl', 'imgs');" id='protocol_tbl_bar'>
                        <img src="imgs/down.jpg"></img>
                     </a>
                     <p> Protocol: </p>
                  </span>
                  <span class="param_input"><input type="text" name="protocol" id='protocol'/></span>
               </th><th>
                  <select class='database-fill' id='proto_drop' query='protocol.name' 
                   onChange="fillDropText('proto_drop', 'protocol');"></select><br />
               </th>
            </tr></thead>
            <tbody id="protocol_tbl" style="display:none">
               <tr><td>
                  <div class="parameter_field">
                     <span class="param_label">
                        Sequencing Region:
                     </span>
                     <span class="param_input"><input type="text" id='region_name' name="region_name" /></span>
                  </div>
               </td><td>
                  <select class='database-fill' id='region_drop' query='protocol.region_name' 
                  onChange="fillDropText('region_drop', 'region_name');"></select><br />
               </td></tr>
               <tr><td>
                  <div class="parameter_field">
                     <span class="param_label">
                        Forward Primer:
                     </span>
                     <span class="param_input">
                        <input type="text" id='fsequence_name' name="f.sequence_name" />
                     </span>
                  </div>
               </td><td>
                  <select class='database-fill' id='fPrimer_drop' query='primer.sequence_name' 
                   onChange="fillDropText('fPrimer_drop', 'fsequence_name');"></select><br />
               </td></tr>
               <tr><td>
                  <div class="parameter_field">
                     <span class="param_label">
                        Reverse Primer:
                     </span>
                     <span class="param_input">
                        <input type="text" id='rsequence_name' name="r.sequence_name" />
                     </span>
                  </div>
               </td><td>
                  <select class='database-fill' id='rPrimer_drop' query='primer.sequence_name' 
                   onChange="fillDropText('rPrimer_drop', 'rsequence_name');"></select><br />
               </td></tr>
               <tr><td>
                  <div class="parameter_field">
                     <span class="param_label">
                        Sequence Primer:
                     </span>
                     <span class="param_input">
                        <input type="text" id='ssequence_name' name="s.sequence_name" />
                     </span>
                  </div>
               </td><td>
                  <select class='database-fill' id='sPrimer_drop' query='primer.sequence_name' 
                   onChange="fillDropText('sPrimer_drop', 'ssequence_name');"></select><br />
               </td></tr>
               <tr><td>
                  <div class="parameter_field">
                     <span class="param_label">
                        Dispensation Sequence:
                     </span>
                     <span class="param_input">
                        <input type="text" id='dispensation_name' name="dispensation_name" />
                     </span>
                  </div>
               </td><td>
                  <select class='database-fill' id='disp_drop' query='dispensation_sequence.dispensation_name' 
                   onChange="fillDropText('disp_drop', 'dispensation_name');"></select><br />
               </td></tr>
            </tbody>
         </table>
      </div>

      <br />

      <div id="quality_indicator">
         Quality Control: <input type="checkbox" name="qc" value="passed">Passed Quality Control</input>
      </div>

      <br />

      <table id="filter_params" cellpadding="10">

         <tr><td> 
            <?php addLogicDropDown() ?>
         </td><td> 
            <div class="parameter_field">
               <span class="param_label"> Sample: </span>
               <span class="param_input">
                  <input type='text' id='sample_id' name='sample_id' />
               </span>
            </div>
         </td><td> 
            <select class='database-fill' id='sample_drop' query='sample.sample_id'
             onChange="fillDropText('sample_drop', 'sample_id');"></select> 
         </td></tr> 
      
         <tr><td> 
            <?php addLogicDropDown() ?>
            <!--<input type='checkbox'>Invert Match</input> -->
         </td><td> 
            <div class="parameter_field">
               <span class="param_label"> Isolate: </span>
               <span class="param_input">
                  <input type='text' id='isolate_name' name='isolate_name' /> 
               </span>
            </div>
         </td><td> 
            <select class='database-fill' id='isolate_drop' query='isolate.isolate_name'
             onChange="fillDropText('isolate_drop', 'isolate_name');"></select> 
         </td></tr> 
      
         <tr><td> 
            <?php addLogicDropDown() ?>
            <!--<input type='checkbox'>Invert Match</input> -->
         </td><td> 
            <div class="parameter_field">
               <span class="param_label"> Host: </span>
               <span class="param_input">
                  <input type='text' id='host_name' name='host_name' /> 
               </span>
            </div>
         </td><td> 
            <select class='database-fill' id='host_drop' query='host.host_name'
             onChange="fillDropText('host_drop', 'host_name');"></select> 
         </td></tr> 
      
         <tr><td> 
            <?php addLogicDropDown() ?>
            <!--<input type='checkbox'>Invert Match</input> -->
         </td><td> 
            <div class="parameter_field">
               <span class="param_label"> Host Species: </span>
               <span class="param_input">
                  <input type='text' id='host_species' name='host_species' /> 
               </span>
            </div>
         </td><td> 
            <select class='database-fill' id='species_drop' query='host_species.common_name'
             onChange="fillDropText('species_drop', 'host_species');"></select> 
         </td></tr> 
   
         <tr><td> 
            <?php addLogicDropDown() ?>
            <!--<input type='checkbox'>Invert Match</input> -->
         </td><td> 
            <div class="parameter_field">
               <span class="param_label"> Date Sequenced: </span>
               <span class="param_input">
                  <input type='text' id='pyrogram_date' name='pyrogram_date' /> 
               </span>
            </div>
         </td><td> 
            <select class='database-fill' id='pyroDate_drop' query='pyrogram.pyrogram_date'
             onChange="fillDropText('pyroDate_drop', 'pyrogram_date');"></select> 
         </td></tr> 
      
         <tr><td> 
            <?php addLogicDropDown() ?>
            <!--<input type='checkbox'>Invert Match</input> -->
         </td><td> 
            <div class="parameter_field">
               <span class="param_label"> Sequencing Person: </span>
               <span class="param_input">
                  <input type='text' id='pyroprint_technician' name='pyroprint_technician' /> 
               </span>
            </div>
         </td><td> 
            <select class='database-fill' id='tech_drop' query='pyrogram.pyroprint_technician'
             onChange="fillDropText('tech_drop', 'pyroprint_technician');"></select> 
         </td></tr> 
      
         <tr><td> 
            <?php addLogicDropDown() ?>
            <!--<input type='checkbox'>Invert Match</input> -->
         </td><td> 
            <div class="parameter_field">
               <span class="param_label">Sequencing Machine:</span>
               <span class="param_input"><input type='text' id='machine_id' name='machine_id' /></span>
            </div>
         </td><td> 
            <select class='database-fill' id='machine_drop' query='pyrogram.machine_id'
             onChange="fillDropText('machine_drop', 'machine_id');"></select> 
         </td></tr> 
      
         <tr><td> 
            <?php addLogicDropDown() ?>
            <!--<input type='checkbox'>Invert Match</input> -->
         </td><td> 
            <div class="parameter_field">
               <span class="param_label"> Sequencing Well: </span>
               <span class="param_input">
                  <input type='text' id='well_id' name='well_id' /> 
               </span>
            </div>
         </td><td> 
            <select class='database-fill' id='well_drop' query='pyrogram.well_id'
             onChange="fillDropText('well_drop', 'well_id');"></select> 
         </td></tr> 
      
         <tr><td> 
            <?php addLogicDropDown() ?>
            <!--<input type='checkbox'>Invert Match</input> -->
         </td><td> 
            <div class="parameter_field">
               <span class="param_label"> PCR Date: </span>
               <span class="param_input">
                  <input type='text' id='pcr_date' name='pcr_date' /> 
               </span>
            </div>
         </td><td> 
            <select class='database-fill' id='pcrDateDrop' query='pyrogram.pcr_date'
             onChange="fillDropText('pcrDateDrop', 'pcr_date');"></select> 
         </td></tr> 
      
         <tr><td> 
            <?php addLogicDropDown() ?>
            <!--<input type='checkbox'>Invert Match</input> -->
         </td><td> 
            <div class="parameter_field">
               <span class="param_label"> PCR Machine: </span>
               <span class="param_input">
                  <input type='text' id='pcr_machine' name='pcr_machine' /> 
               </span>
            </div>
         </td><td> 
            <select class='database-fill' id='pcr_machine_drop' query='pyrogram.pcr_machine'
             onChange="fillDropText('pcr_machine_drop', 'pcr_machine');"></select> 
         </td></tr> 
      </table>

      <br />
      <div class="pyro_controls">
         <input type="submit" value="Filter">
         <input type="reset" value="Reset">
      </div>
   </form>
</div>

<div class="pyro_controls">
   <hr />

   <input type="button" id='checkButton' value="Check All" onClick="toggleCheck();">
   <input type="button" value="Match Selected Against Eachother" onClick="matchEachother();">
   <input type="button" value="Match Selected Against Database" onClick="matchDatabase()">
   <input type="button" value="Match Selected Against Environment" onClick="matchEnv()">
   <input type="button" value="Match Selected Against Non-Environment" onClick="matchNonEnv()">

   <?php 
   //TODO: add button that gets all
   $array = query("select pyrogram_num, p.isolate_name, pyrogram_date, protocol, machine_id, common_name ".
      "FROM pyrogram p INNER JOIN (isolate i, host_species h) ".
      "ON (p.isolate_name = i.isolate_name AND ".
      "i.host_species = h.common_name) WHERE p.pyrogram_num IN (SELECT pyrogram_num FROM pyrogram_data_point)");
   ?>
</div>

<br />

<div id="pyro_results">
   <table id='pyros' border="0" class="paged sortable">
      <thead>
      <tr>
         <th>Selected</th>
         <th>Isolate</th>
         <th>Pyro Date</th>
         <th>Protocol</th>
         <th>Host Species</th>
         <th>Sequencing Machine</th>
      </tr>
      </thead>
      <tbody id='pyro_tableBody'>
      <?php while($row = mysqli_fetch_array($array))
      {
         echo "<tr>";
         echo "<td><input class='selectPyro' value='".$row['pyrogram_num']."' type=\"checkbox\"></td>";
         echo "<td onClick='goLink(\"$webBase/pyro/displayPyro.php?pyroNum=".
               $row['pyrogram_num']."\");' nowrap=\"nowrap\">".$row['isolate_name']."</td>";
         echo "<td onClick='goLink(\"$webBase/pyro/displayPyro.php?pyroNum="
               .$row['pyrogram_num']."\");' nowrap=\"nowrap\">".($row['pyrogram_date'])."</td>";
         echo "<td onClick='goLink(\"$webBase/pyro/displayPyro.php?pyroNum=".
               $row['pyrogram_num']."\");' nowrap=\"nowrap\">".$row['protocol']."</td>";
         echo "<td onClick='goLink(\"$webBase/pyro/displayPyro.php?pyroNum=".
               $row['pyrogram_num']."\");' nowrap=\"nowrap\">".$row['common_name']."</td>";
         echo "<td onClick='goLink(\"$webBase/pyro/displayPyro.php?pyroNum=".
               $row['pyrogram_num']."\");' nowrap=\"nowrap\">".$row['machine_id']."</td>";
         echo "</tr>";
      }
   ?>
      </tbody>
   </table>
</div>

</body>
</html>
