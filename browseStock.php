<?php $thisPage = "browseStock"; ?>
<?php $basePath = "."; ?>

<?php include('meta.php'); ?>

<link rel="StyleSheet" href="css/tablesorter.css" type="text/css" />
<!--<link rel="StyleSheet" href="css/pagination.css" type="text/css" />-->
<link rel="StyleSheet" href="css/browseStock.css" type="text/css" />

<script type="text/javascript" src="js/jquery.pagination.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="js/jquery-table-me.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
<script type="text/javascript" src="js/dropDown.js"></script>

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
            if (window.console)
            {
               console.log(data);
            }
            //data = data.substring(data.indexOf('['), data.length);
            console.log(data);
            var str = "";
            var tableRow = Array();
            var currentRow = -1;

            /*
             * These variables are for managing table row construction
             */
            var isolateNdx = 0;
            var specieNdx = 1;
            var hostNdx = 2;
            var sampleNdx = 3;
            var sampleDateNdx = 4;
            var freezeDateNdx = 5;

            //will be inserted into pyro_tableBody
            $('#isolate_tableBody').html('');

            for (var index in data) {
               var pyroObj = data[index];
               //console.log(pyroObj.isolate_name);
               //tableRow[checkBoxNdx] = "<td><input type='checkbox'></input></td>";

               if (pyroObj.sample_date == '0000-00-00') {
                  pyroObj.sample_date = '-';
               }
               tableRow[sampleDateNdx] = '<td>' + pyroObj.sample_date + '</td>';
               tableRow[freezeDateNdx] = '<td>' + pyroObj.freezing_date + '</td>';
               tableRow[sampleNdx] = '<td>' + pyroObj.sample_id + '</td>';
               tableRow[hostNdx] = '<td>' + pyroObj.host_name + '</td>';
               tableRow[specieNdx] = '<td>' + pyroObj.host_species + '</td>';
               tableRow[isolateNdx] = '<td>' + pyroObj.isolate_name + '</td>';

               $('#isolate_tableBody').append('<tr onClick=\"linkIsolate(\'' + pyroObj.isolate_name + '\');\">' + tableRow.join('') + '</tr>');
               //tableRow = Array();
            }

            $('#isolateTable').trigger("update"); 

         },
         dataType: 'JSON'
      }
      
      //Make all submits ajax 
      $('#isolateFilter').ajaxForm(options); 
      
   });
</script>


</head>
<?php include("functions.php"); ?>

<body>

<?php include("header.php"); ?>

<div id="stock_header">
   <h1>Browse/Filter Freezer Stock</h1>
</div>

<div id="isolate_filter">
   <form id="isolateFilter" action="filter/filterIsolate.php" method="POST">
      <table cellpadding="10" id="filter_params">
         <thead><tr>
            <th> Filter Input </th>
            <th> Filter Auto Fill </th>
         </tr></thead>
         <tr><td>
            <span class="param_label">
               Host Species:
            </span>
            <span class="param_input"><input type="text" name="host_species" id="host_species" /></span>
         </td><td>
            <select class='database-fill' id='species_drop' query='host_species.common_name'
             onChange="fillDropText('species_drop', 'host_species');"></select> 
         </td></tr>
         <tr><td>
            <span class="param_label" id="wide_label">
               (Comma Separated)
            </span>
            <br />
            <span class="param_label">
               Specific Hosts:
            </span>
            <span class="param_input"><input type="text" name="host_name" id="host_name"/></span>
         </td><td>
            <select class='database-fill' id='host_drop' query='host.host_name'
             onChange="addDropText('host_drop', 'host_name');"></select> 
         </td></tr>
         <tr><td>
            <span class="param_label">
               Sample:
            </span>
            <span class="param_input"><input type="text" name="sample_id" id="sample_id"/></span>
         </td><td>
            <select class='database-fill' id='sample_drop' query='isolate.sample_id'
             onChange="fillDropText('sample_drop', 'sample_id');"></select> 
         </td></tr>
         <tr><td>
            <span class="param_label">
               Date Frozen:
            </span>
            <span class="param_input"><input type="text" name="freezing_date" id="freezing_date"/></span>
            <br />
            <span class="param_label">
               Range: From:
            </span>
            <span class="param_input"><input type="text" name="from_date" id="from_date"/></span>
            <br />
            <span class="param_label">
               To:
            </span>
            <span class="param_input"><input type="text" name="to_date" id="to_date"/></span>
         </td><td> 
            <select class='database-fill' id='date_drop' query='isolate.freezing_date'
             onChange="fillDropText('date_drop', 'freezing_date');"></select> 
         </td></tr>
         <tr><td>
            <span class="param_label">
               Isolating Person:
            </span>
            <span class="param_input">
               <input type="text" name="isolate_technician" id="isolate_technician"/>
            </span>
         </td><td>
            <select class='database-fill' id='technician_drop' query='isolate.isolate_technician'
             onChange="fillDropText('technician_drop', 'isolate_technician');"></select> 
         </td></tr>
         <tr><td>
            <span class="param_label">
               Status:
            </span>
            <span class="param_input">
               <input type="checkbox" name="is_pyroPrinted" value="1" checked="yes" id="is_pyroPrinted">
                     Already Pyrosequenced
               </input>
            </span>
            <br />
            <span class="param_input">
               <input type="checkbox" name="not_pyroPrinted" value="1" checked="yes"id="not_pyroPrinted">
                  Not Yet Pyrosequenced
               </input>
            </span>
         </td><td>
         </td></tr>
         <tr><td>
            <span class="param_label">
               Freezer Location:
            </span>
            <span class="param_input">
               <input type="text" name="freezer_location" id="freezer_location"/>
            </span>
         </td><td>
            <select class='database-fill' id='freezer_drop' query='isolate.freezer_location'
             onChange="fillDropText('freezer_drop', 'freezer_location');"></select> 
         </td></tr>
      </table>

      <br />

      <div class="isolate_controls">
         <input type="submit" value="Filter">
         <input type="reset" value="Reset">
      </div>
   </form>
</div>

<hr />

<?php
include("sqlFunctions.php");
$resultSet = query("select host_species, host_name, isolate_name, sample_id,".
"sample_date, freezing_date from sample natural join isolate LIMIT 50");

?>
<div id="isolate_results">
   <table id="isolateTable" border="1" class="paged sortable">
      <thead>
      <tr>
         <th>Isolate Name</th>
         <th>Host Species</th>
         <th>Host</th>
         <th>Sample</th>
         <th>Sample Date</th>
         <th>Freezing Date</th>
      </tr>
      </thead>
      <tbody id="isolate_tableBody">
      <?php
       while($row = mysqli_fetch_array($resultSet))
       {
          echo "<tr onClick=\"linkIsolate('".$row['isolate_name']."');\">";
          echo "<td>".$row['isolate_name']."</td>";
          echo "<td>".$row['host_species']."</td>";
          echo "<td>".$row['host_name']."</td>";
          echo "<td>".$row['sample_id']."</td>";
          echo "<td>".$row['sample_date']."</td>";
          echo "<td>".$row['freezing_date']."</td>";
          echo "</tr>";
       }

   ?>
      </tbody>
   </table>
</div>


</body>
</html>
