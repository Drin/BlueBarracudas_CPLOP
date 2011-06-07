<?php $thisPage = "match"; ?>
<?php $basePath = ".."; ?>

<?php include("../meta.php"); ?>
<link rel="StyleSheet" href="../css/tablesorter.css" type="text/css" />
<link rel="StyleSheet" href="../css/pagination.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo "$cssBase/" ?>matchPyrogram.css" media="screen" />

<script type='text/javascript' src="<?php echo "$jsBase/" ?>dropDown.js"></script>
<script type='text/javascript' src="<?php echo "$jsBase/" ?>jquery.form.js"></script>
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<script type="text/javascript" src="../js/jquery.tablesorter.min.js"></script>
<!--
<script type="text/javascript" src="../js/jquery-table-me.js"></script>
-->

<script type='text/javascript'>
   function noResults()
   {
      //Empty and invisible verbose table
      document.getElementById('verboseHeader').innerHTML = '';
      document.getElementById('verboseBody').innerHTML = '';
      $('#verboseTable').tablesorter();
      $('#verboseTable').trigger("update");
      document.getElementById("verboseTable").style.visibility = "hidden";

      //Make the export button invisible.
      document.getElementById('exportButton').style.visibility = 'hidden';

      //Empty and invisible succinct tables
      document.getElementById('succinctHighHeader').innerHTML = '';
      document.getElementById('succinctHighBody').innerHTML = '';
      $('#succinctHighTable').tablesorter();
      $('#succinctHighTable').trigger("update");

      document.getElementById('succinctLowHeader').innerHTML = '';
      document.getElementById('succinctLowBody').innerHTML = '';
      $('#succinctLowTable').tablesorter();
      $('#succinctLowTable').trigger("update");

      document.getElementById("succinct").style.visibility = "hidden";

      //Show no results
      document.getElementById('noResults').style.visibility = 'visible';

      //Hide the loading symbol
      document.getElementById("loadingImage").style.visibility = "hidden";
   }

   /**
    * Fill one of the lists using the pyros given.
    *
    * @param listId The id of the multi-slect to fill.
    * @param pyroList Either a comma-seperated list of pyrogram numbers,
    *  or 'all' which means every pyrogram with the same protocol.
    *
    * TODO: This calls match() in the AJAX call-back, but technically match
    *  should not be called unless both the left and right are already filled.
    */
   function getFillList(listId, pyroList, callMatch)
   {
      var parseFunction = function(data)
      {
         var vals = JSON.parse(data);

         options = '';

         for (var ndx in vals)
         {
            options += ("<option value='" + vals[ndx]['pyrogram_num'] + "'>" +
             vals[ndx]['pyroName'] + "</option>");
         }

         document.getElementById(listId).innerHTML = options;

         if (callMatch)
         {
            match();
         }
      };

      if (pyroList == 'all')
      {
         $.get('pyroNameFetch.php', parseFunction);
      }
      else if (pyroList == 'nonEnv')
      {
         $.get('nonEnvPyroNameFetch.php', parseFunction);
      }
      else if (regMatch = pyroList.match(/^env,((?:begin)|(?:\d+-\d+-\d+)),((?:end)|(?:\d+-\d+-\d+))$/))
      {
         $.get(('envPyroNameFetch.php?begin=' + regMatch[1] + '&end=' + regMatch[2]), parseFunction);
      }
      else
      {
         $.get(('selectPyroNameFetch.php?pyroNums=' + pyroList), parseFunction);
      }
   }

   function emptyElement(eleId)
   {
      document.getElementById(eleId).innerHTML = "";
   }

   function getThreshValue(inputId, defaultVal)
   {
      var temp = document.getElementById(inputId).value;

      if (temp.match(/^((\d+)|(\.\d+)|(\d+\.\d+))$/))
      {
         return parseFloat(temp);
      }
      else
      {
         document.getElementById(inputId).value = defaultVal;
         return defaultVal;
      }
   }

   function fillMatchTable(leftPyros, rightPyros, leftStr, rightStr, numMap, verbose)
   {
      //console.log(leftStr);
      //console.log(rightStr);
      //console.log('fetchListMatch.php?leftPyros=' + leftStr + '&rightPyros=' + rightStr);

      $.get(('fetchListMatch.php?leftPyros=' + leftStr + '&rightPyros=' + rightStr),
       function(data)
       {
         //console.log(data);

         if (typeof console == "object") {
            //console.log(data);
         }

         var vals = JSON.parse(data);

         //console.log(vals);

         var resMap = {};
         for (ndx in vals)
         {
            var res = {'val': parseFloat(vals[ndx]['pearson']).toFixed(6),
             'left-num': vals[ndx]['num1'],
             'right-num': vals[ndx]['num2'],
             'left-name': numMap[vals[ndx]['num1']],
             'right-name': numMap[vals[ndx]['num2']]};

            resMap[vals[ndx]['num1'] + ',' + vals[ndx]['num2']] = res;
         }

         highThreshold = getThreshValue('hiThreshold', 0.997);
         lowThreshold = getThreshValue('lowThreshold', 0.995);

         if (verbose)
         {
            fillVerbose(leftPyros, rightPyros, resMap, highThreshold, lowThreshold);
         }
         else
         {
            fillSuccinct(sortResults(resMap), highThreshold, lowThreshold);
         }

         sortResults(resMap);
       });
   }

   function getNumMatches()
   {
      var temp = document.getElementById('numMatches').value;

      if (temp.match(/^((\d+)|(\d+\.0*))$/))
      {
         return parseInt(temp);
      }
      else
      {
         document.getElementById('numMatches').value = 0;
         return 0;
      }
   }

   function fillSuccinct(sortRes, highThresh, lowThresh)
   {
      var head = '<tr align="center"><th></th><th></th><th>Corelation</th></tr>';

      var highTable = '';
      var lowTable = '';

      var numMatches = getNumMatches();
      var count = 0;

      for (var ndx in sortRes)
      {
         if (numMatches == 0 || count < numMatches)
         {
            if (sortRes[ndx]['val'] >= highThresh)
            {
               count++;

               highTable += '<tr><td>' + sortRes[ndx]['left-name'] + 
                '</td><td>' + sortRes[ndx]['right-name'] + '</td><td class="hi-match">' +
                sortRes[ndx]['val'] + '</td></tr>';
            }
            else if (sortRes[ndx]['val'] >= lowThresh)
            {
               count++; 

               lowTable += '<tr><td>' + sortRes[ndx]['left-name'] + 
                '</td><td>' + sortRes[ndx]['right-name'] + '</td><td class="low-match">' +
                sortRes[ndx]['val'] + '</td></tr>';
            }
         }
      }

      if (highTable != '' || lowTable != '')
      {
         document.getElementById("succinct").style.visibility = "visible";
         document.getElementById("exportButton").style.visibility = "visible";
         document.getElementById('noResults').style.visibility = 'hidden';
         
         document.getElementById('succinctHighHeader').innerHTML = head;
         document.getElementById('succinctHighBody').innerHTML = highTable;
         $('#succinctHighTable').trigger("update");
         $('#succinctHighTable').tablesorter();

         document.getElementById('succinctLowHeader').innerHTML = head;
         document.getElementById('succinctLowBody').innerHTML = lowTable;
         $('#succinctLowTable').trigger("update");
         $('#succinctLowTable').tablesorter();
      }
      else
      {
         noResults();
      }

      //Hide the loading symbol
      document.getElementById("loadingImage").style.visibility = "hidden";
   }

   function exportMatch()
   {
      //var url = "<?php echo "$webBase"; ?>/tmp/match/matchData.csv";

      $.get('exportMatch.php', 
       function(data)
       {
         console.log(data);
       });

      //window.open(url);
      //console.log(url);
   }

   function sortResults(resMap)
   {
      var sortList = [];

      for (var key in resMap)
      {
         var position = 0;

         for (position = 0; position < sortList.length; position++)
         {
            if (sortList[position]['val'] < resMap[key]['val'])
            {
               break;
            }
         }

         sortList.splice(position, 0, resMap[key]);
      }

      return sortList;
   }

   function fillVerbose(leftPyros, rightPyros, resMap, highThresh, lowThresh)
   {
      var head = '<tr align="center"><th></th>';
      for (var ndx in rightPyros)
      {
         head += ('<th>' + rightPyros[ndx]['name'] + '</th>');
      }
      head += '</tr>';

      var table = '';
      for (var leftNdx in leftPyros)
      {
         table += ('<tr align="center"><th>' + leftPyros[leftNdx]['name'] + '</th>');

         for (var rightNdx in rightPyros)
         {
            var val = resMap[(leftPyros[leftNdx]['num'] + ',' + rightPyros[rightNdx]['num'])]['val'];
            
            if (val == -2)
            {
               val = 'N/A';
               classVal = 'mismatch';
            }
            else if (val >= highThresh)
            {
               classVal = 'hi-match';
            }
            else if (val >= lowThresh)
            {
               classVal = 'low-match';
            }
            else
            {
               classVal = 'no-match';
            }

            table += ('<td class="' + classVal  + '">' + val + 
               '</td>');
         }

         table += '</tr>';
      }

      document.getElementById("verboseTable").style.visibility = "visible";
      document.getElementById("exportButton").style.visibility = "visible";
      document.getElementById('noResults').style.visibility = 'hidden';
      
      document.getElementById('verboseHeader').innerHTML = head;
      document.getElementById('verboseBody').innerHTML = table;

      $('#verboseTable').trigger("update");
      $('#verboseTable').tablesorter();

      //Hide the loading symbol
      document.getElementById("loadingImage").style.visibility = "hidden";
   }

   function match()
   {
      var leftPyros = [];
      var rightPyros = [];
      var leftStr = "";
      var rightStr = "";

      var table = "";
    
      noResults();

      //Display the loading image
      document.getElementById("loadingImage").style.visibility = "visible";

      var numMap = {};

      $('#left-pyros > option').each(function()
      {
         var val = {'num': $(this).attr('value'), 'name': $(this).text()};
         val[$(this).attr('value')] = $(this).text();

         leftPyros.push(val);
         leftStr += $(this).attr('value') + ', ';

         numMap[$(this).attr('value')] = $(this).text();
      });
      leftStr = leftStr.replace(/, $/, '');

      $('#right-pyros > option').each(function()
      {
         var val = {'num': $(this).attr('value'), 'name': $(this).text()};
         val[$(this).attr('value')] = $(this).text();

         rightPyros.push(val);
         rightStr += $(this).attr('value') + ', ';

         numMap[$(this).attr('value')] = $(this).text();
      });
      rightStr = rightStr.replace(/, $/, '');

      if (leftPyros.length == 0 || rightPyros.length == 0)
      {
         noResults();

         return;
      }

      var verbose = $('#verboseRadio').attr('checked') == 'checked' ? true : false;

      fillMatchTable(leftPyros, rightPyros, leftStr, rightStr, numMap, verbose);
   }

   function fillEnvRange(listId, beginTextId, endTextId)
   {
      var begin = $('#' + beginTextId)[0].value;
      var end = $('#' + endTextId)[0].value;

      begin = (begin == 'Beginning of Time' ? 'begin' : begin);
      end = (end == 'End of Time' ? 'end' : end);

      getFillList(listId, "env," + begin + "," + end, false);
   }

   $(document).ready(function()
   {
      var left_options = 
      {
         beforeSubmit : function(arr, form, options)
         {
            return requireFieldsNoSubmit(form[0].id);
         },
         success : function(data)
         {
            if (data.match(/error/i))
            {
               alert(data);
            }
            else
            {
               //TODO populate '#xml_wells'
               if (typeof console == "object") {
                  //console.log(data);
               }
               var ghost_options = '<option value=""></option>';
               //pyro_num is pyrogram_num but pyro_name is "isolate_name, file"
               JSON.parse(data, function (pyro_num, pyro_name) {
                  if (pyro_num == "") {
                     return;
                  }

                  ghost_options += '<option value="' + pyro_num + '">' + pyro_name + '</option>';
                  /*
                  if (typeof console == "object") {
                     console.log(pyro_num.toSource() + ',  ' + pyro_name.toSource());
                  }
                   */
               });
               //alert(ghost_options);
               alert("success");

               $('#left_xml_wells').html(ghost_options);
            }
         }
      }

      var right_options = 
      {
         beforeSubmit : function(arr, form, options)
         {
            return requireFieldsNoSubmit(form[0].id);
         },
         success : function(data)
         {
            if (data.match(/error/i))
            {
               alert(data);
            }
            else
            {
               //TODO populate '#xml_wells'
               //alert(data);
               var ghost_options = '<option value=""></option>';
               //pyro_num is pyrogram_num but pyro_name is "isolate_name, file"
               JSON.parse(data, function (pyro_num, pyro_name) {
                  if (pyro_num == "") {
                     return;
                  }

                  ghost_options += '<option value="' + pyro_num + '">' + pyro_name + '</option>';
                  /*
                  if (typeof console == "object") {
                     console.log(pyro_num.toSource() + ',  ' + pyro_name.toSource());
                  }
                   */
               });
               //alert(ghost_options);
               alert("success");

               $('#right_xml_wells').html(ghost_options);
            }
         }
      }

      /* added by amontana */
      $('#left_ghost_form').ajaxForm(left_options);
      $('#right_ghost_form').ajaxForm(right_options);

      fillDropDowns('<?php echo "$basePath"; ?>');

      var leftPyros = "<?php if(!empty($_GET['left'])){echo $_GET['left'];}else{echo '';}?>";
      
      var rightPyros = "<?php if(!empty($_GET['right'])){echo $_GET['right'];}else{echo '';}?>";
      var regEx = /^(all)|(nonEnv)|(env,((?:begin)|(?:\d+-\d+-\d+)),((?:end)|(?:\d+-\d+-\d+)))|(\d+(,\d+)*)$/;
     
      if (leftPyros.match(regEx))
      { 
         //Get the pyros that are on the left side.
         getFillList('left-pyros', leftPyros, true);
      }

      if (rightPyros.match(regEx))
      { 
         //Get the pyros that are on the right side.
         getFillList('right-pyros', rightPyros, true);
      }
   });
</script>

</head>
<?php include("../functions.php"); ?>

<body>

<?php include("../header.php"); ?>

<h2>Match Pyros</h2>
<table>
   <tbody>
      <tr>
         <td width='330'>
            <select multiple id='left-pyros' style="width: 300px; height: 150px"
             onClick="removeSelection('left-pyros');">
            </select>
            <br />
            Add pyrogram (Isolate Name, Host Name): <br />
            -<select class='database-fill-pyro' id='left-pyro_drop' 
             onChange="appendDropSelect('left-pyro_drop', 'left-pyros');"></select>
            <br />
            <table width='330'>
               <tr>
                  <td>
                     <button id='clear' value="clear" onClick="emptyElement('left-pyros');">Clear List</button>
                  </td>
               </tr>
               <tr>
                  <td><br />Special Selections:</td>
               </tr>
               <tr>
                  <td>
                     <button id='loadAll' value="loadAll" 
                      onClick="getFillList('left-pyros', 'all', false);">Full Database</button>
                  </td>
               </tr>
               <tr>
                  <td>
                     <button id='leftNonEnv' onClick="getFillList('left-pyros', 'nonEnv', false);">Non-Environmental</button>
                  </td>
               </tr>
               <tr>
                  <td>
                     <form id="left_ghost_form" enctype="multipart/form-data"
                           action="ghostUploader.php?side=left" method="POST">
                        <input type="file" id="left_ghost_file" name="left_ghost_file_name" value="XML file"/>
                        <select id='left_xml_wells' defaultVal=''
                                onChange="appendDropSelect('left_xml_wells', 'left-pyros');">
                        </select>
                        <input type="submit" value="extract" />
                     </form>
                  </td>
               </tr>
               <tr>
                  <td>
                     <table border='1' width='350'>
                        <tr>
                           <td>
                              <button id='leftEnv' onClick="fillEnvRange('left-pyros', 'leftEnvBegin', 'leftEnvEnd');">Environmental</button> 
                               within range:
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <input type='text' id='leftEnvBegin' value='Beginning of Time' />
                              <select class='database-fill' defaultVal='Beginning of Time' 
                               defaultTag='Beginning of Time' id='leftEnvBeginDrop' query='pyrogram.pyrogram_date'
                               onChange="fillDropText('leftEnvBeginDrop', 'leftEnvBegin');"></select>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <input type='text' id='leftEnvEnd' value='End of Time' />
                              <select class='database-fill' defaultVal='End of Time' 
                               defaultTag='End of Time' id='leftEnvEndDrop' query='pyrogram.pyrogram_date'
                               onChange="fillDropText('leftEnvEndDrop', 'leftEnvEnd');"></select>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
         </td>
         <td width='150' valign='top' align='center'><br /><br />AGAINST</td>
         <td width='330'>
            <select multiple id='right-pyros' style="width: 300px; height: 150px"
             onClick="removeSelection('right-pyros');">
            </select>
            <br />
            Add pyrogram (Isolate Name, Host Name): <br />
            -<select class='database-fill-pyro' id='right-pyro_drop' 
             onChange="appendDropSelect('right-pyro_drop', 'right-pyros');"></select>
            <br />
            <table width='330'>
               <tr>
                  <td>
                     <button id='clear' value="clear" onClick="emptyElement('right-pyros');">Clear List</button>
                  </td>
               </tr>
               <tr>
                  <td><br />Special Selections:</td>
               </tr>
               <tr>
                  <td>
                     <button id='loadAll' value="loadAll" 
                      onClick="getFillList('right-pyros', 'all', false);">Full Database</button>
                  </td>
               </tr>
               <tr>
                  <td>
                     <button id='rightNonEnv' onClick="getFillList('right-pyros', 'nonEnv', false);">Non-Environmental</button>
                  </td>
               </tr>
               <tr>
                  <td>
                     <form id="right_ghost_form" enctype="multipart/form-data"
                           action="ghostUploader.php?side=right" method="POST">
                        <input type="file" id="right_ghost_file" name="right_ghost_file_name" value="XML file"/>
                        <select id='right_xml_wells' defaultVal=''
                                onChange="appendDropSelect('right_xml_wells', 'right-pyros');">
                        </select>
                        <input type="submit" value="extract" />
                     </form>
                  </td>
               </tr>
               <tr>
                  <td>
                     <table border='1' width='350'>
                        <tr>
                           <td>
                              <button id='rightEnv' onClick="fillEnvRange('right-pyros', 'rightEnvBegin', 'rightEnvEnd');">Environmental</button> 
                               within range:
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <input type='text' id='rightEnvBegin' value='Beginning of Time' />
                              <select class='database-fill' defaultVal='Beginning of Time' 
                               defaultTag='Beginning of Time' id='rightEnvBeginDrop' query='pyrogram.pyrogram_date'
                               onChange="fillDropText('rightEnvBeginDrop', 'rightEnvBegin');"></select>
                           </td>
                        </tr>
                        <tr>
                           <td>
                              <input type='text' id='rightEnvEnd' value='End of Time' />
                              <select class='database-fill' defaultVal='End of Time' 
                               defaultTag='End of Time' id='rightEnvEndDrop' query='pyrogram.pyrogram_date'
                               onChange="fillDropText('rightEnvEndDrop', 'rightEnvEnd');"></select>
                           </td>
                        </tr>
                     </table>
                  </td>
               </tr>
            </table>
         </td>
         <td align='center'>
            <div class='hiDiv'>High Threshold</div><input type='text' id='hiThreshold' value='0.997' />
            <br />
            <br />
            <br />
            <div class='lowDiv'>Low Threshold</div><input type='text' id='lowThreshold' value='0.995' />
         </td>
         <td>
            <img id="loadingImage" style="visibility:hidden" 
             src="<?php echo "$imagePath/loading.gif"; ?>" alt="Loading..." />
         </td>
      </tr>
      <tr>
         <td>
            Verbose: <input id='verboseRadio' type='radio' name='verbose' value='true' checked/>
            Succinct: <input type='radio' name='verbose' value='false' />
         </td>
         <td colspan=2 align="center">
            <button id='match' onClick="match();">Match Selected</button>
         </td>
         <td>
            Number of Matches (0 for all)<br />
            <input id='numMatches' type='text' value=0 /><br />
            <sub>Does not apply for verbose output.</sub>
         </td>
      </tr>
   </tbody>
</table>

<hr />

<input type='button' id='exportButton' style='visibility:hidden'
 value='Export Match Data' onClick="goLink('exportMatch.php');"/>

<div id='noResults' style="visibility:visible">
   No Results
</div>

<div id='succinct' style="visibility:hidden">
   <table cellpadding='5'>
   <tr align='center'>
      <td>Pass High Threshold</td><td>Pass Low Threshold</td>
   </tr>
   <tr valign='top'>
   <td>

   <table border=1 id='succinctHighTable' class='tablesorter'>
      <thead id='succinctHighHeader'>
      </thead>
      <tbody id='succinctHighBody'>
      </tbody>
   </table>
   
   </td><td>

   <table border=1 id='succinctLowTable' class='tablesorter'>
      <thead id='succinctLowHeader'>
      </thead>
      <tbody id='succinctLowBody'>
      </tbody>
   </table>

   </td></tr>
   </table>
</div>

<table border=1 id='verboseTable' class='tablesorter' style="visibility:hidden">
   <thead id='verboseHeader'>
   </thead>
   <tbody id='verboseBody'>
   </tbody>
</table>
   
</body>
</html>
