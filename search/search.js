/**
   * Check the input data and alert on error.
   *
   * @return true if the data is good, false otherwise.
   */
function checkData(seq, values, tolerence)
{
   if (values.length == 1 && values[0] == "")
   {
      values.pop();
   }

   if (seq.length == 0 && values.length == 0)
   {
      return false;
   }

   if (seq.length != values.length)
   {
      if (seq.length == 0)
      {
         alert("Search Sequence must be non-zero.");
         return false;
      }
      else if (values.length == 0)
      {
         alert("Search Values must be non-zero.");
         return false;
      }
      else
      {
         alert("Sequence and Values must be the same size.");
         return false;
      }
   }

   if (seq.match(/[^ACTG]/))
   {
      alert("Sequence must only contain A, C, T, or G.");
      return false;
   }

   for (var ndx = 0; ndx < values.length; ndx++)
   {
      if (!values[ndx].match(/^\d+(\.\d+)?$/))
      {
         alert("Values must contain only numbers.");
         return false;
      }
   }

   if (tolerence.match(/-\d+(\.\d+)?$/))
   {
      alert("Tolerence must be positive.");
      return false;
   }

   if (!tolerence.match(/^(\d+(\.\d+)?)|(\.\d+)$/))
   {
      alert("Invalid Tolerence.");
      return false;
   }

   return true;
}

/**
   * Check to see if the given pyrogram is a pearson match for the query
   */
function pyroPearsonMatch(searchSeq, searchValues, seq, values, tolerence)
{
   tolerence = parseFloat(tolerence);
   
   if (searchSeq.length <= 1)
   {
      return tolerence > 1;
   }

   for (var pyroNdx = 0; pyroNdx < seq.length; pyroNdx++)
   {
      if (seq[pyroNdx] == searchSeq.charAt(0) && 
         (pyroNdx + searchSeq.length <= seq.length))
      {
         //GET the means
         var searchMean = 0;
         var valMean = 0;

         for (var searchNdx = 0; searchNdx < searchSeq.length; searchNdx++)
         {
            searchVal = parseFloat(searchValues[searchNdx]);
            pyroVal = parseFloat(values[pyroNdx + searchNdx]);

            searchMean += searchVal;
            valMean += pyroVal;
         }

         searchMean /= searchSeq.length;
         valMean /= searchSeq.length;

         //Get the stdDeviations
         var searchDev = 0;
         var valDev = 0;

         for (var searchNdx = 0; searchNdx < searchSeq.length; searchNdx++)
         {
            searchVal = parseFloat(searchValues[searchNdx]);
            pyroVal = parseFloat(values[pyroNdx + searchNdx]);

            searchDev += Math.pow((searchVal - searchMean), 2);               
            valDev += Math.pow((pyroVal - valMean), 2);               
         }

         searchDev = Math.sqrt(searchDev / searchSeq.length);
         valDev = Math.sqrt(valDev / searchSeq.length);

         //Calc the actual Pearson Correlation
         var pearson = 0;

         for (var searchNdx = 0; searchNdx < searchSeq.length; searchNdx++)
         {
            searchVal = parseFloat(searchValues[searchNdx]);
            pyroVal = parseFloat(values[pyroNdx + searchNdx]);
   
            pearson += ((searchVal - searchMean) * (pyroVal - valMean));
         }

         //TODO: Do we need a -1 on the length?
         pearson /= ((searchSeq.length) * searchDev * valDev);

         if (1.0 - pearson <= tolerence)
         {
            return pearson;
         }
      }
   }

   return false;
}

function sortResults(pyros, percentMap)
{
   var rtn = [];

   for (var pyroNdx in pyros)
   {
      pyros[pyroNdx]['matchPercent'] = percentMap[pyros[pyroNdx]['pyrogram_num']];
      var position = 0;

      for (position = 0; position < rtn.length; position++)
      {
         if (rtn[position]['matchPercent'] < pyros[pyroNdx]['matchPercent'])
         {
            break;
         }
      }

      rtn.splice(position, 0, pyros[pyroNdx]);
   }

   return rtn;
}

/**
   * Check to see if the given pyrogram is a direct match for the query
   */
function pyroDirectMatch(searchSeq, searchValues, seq, values, tolerence)
{
   tolerence = parseFloat(tolerence);

   for (var pyroNdx = 0; pyroNdx < seq.length; pyroNdx++)
   {
      if (seq[pyroNdx] == searchSeq.charAt(0))
      {
         var match = true;
         var sumAbsDiff = 0;
         var sumSearch = 0;

         for (var searchNdx = 0; searchNdx < searchSeq.length; searchNdx++)
         {
            searchVal = parseFloat(searchValues[searchNdx]);
            pyroVal = parseFloat(values[pyroNdx + searchNdx]);

            sumSearch += searchVal;
            sumAbsDiff += Math.abs(searchVal - pyroVal);

            if (Math.abs(searchVal - pyroVal) > tolerence)
            {
               match = false;
               break;
            }
         }

         if (match)
         {
            return 1 - (sumAbsDiff / sumSearch);
         }
      }
   }

   return false;
}

/**
   * Return a table row formatted with this pyro's information.
   */
function insertPyros(pyroNums, percentMap)
{
   if (pyroNums.length == 0)
   {
      //Hide the loading image
      document.getElementById("loadingImage").style.visibility = "hidden";
      
      document.getElementById("pyros").innerHTML = "<tr><td>NONE</td></tr>";
      return;
   }

   var xmlhttp;
   if (window.XMLHttpRequest)
   {
      xmlhttp = new XMLHttpRequest();
   }
   else
   {//IE5, IE6
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
   }

   xmlhttp.onreadystatechange = function()
   {
      if (xmlhttp.readyState==4 && xmlhttp.status==200)
      {
         var pyro = JSON.parse(xmlhttp.responseText);

         sortRes = sortResults(pyro, percentMap);

         var rtn = "";

         for (var ndx in sortRes)
         {
            rtn += '<tr onMouseOver="toggleColor(this, true);"';
            rtn += 'onMouseOut="toggleColor(this, false);"';
            rtn += ('onClick="goLink(\'../pyro/displayPyro.php?pyroNum=' +
               sortRes[ndx].pyrogram_num + '\');">');

            /*
            rtn += ('onClick="goLink(\'' + 
             <?php echo ("'".$webBase.'/pyro/displayPyro.php?pyroNum='."'"); ?> +
             pyro[ndx].pyrogram_num + '\');">');
            */

            rtn += "<td>" + (sortRes[ndx].matchPercent * 100).toFixed(4) + "</td>";
            rtn += "<td>" + sortRes[ndx].xml_file + "</td>";
            rtn += "<td>" + sortRes[ndx].well_id + "</td>";
            rtn += "<td>" + sortRes[ndx].pyrogram_date + "</td>";
            rtn += "<td>" + sortRes[ndx].protocol + "</td>";
            rtn += "<td>" + sortRes[ndx].isolate_name + "</td>";
            rtn += "<td>" + (sortRes[ndx].quality_control == "1" ? "Yes" : "No") + "</td>";

            rtn += "</tr>";
         }

         document.getElementById("pyros").innerHTML = rtn;
   
         //Hide the loading image
         document.getElementById("loadingImage").style.visibility = "hidden";

         $('#sortPyroTable').trigger("update");
      }
   }

   var paramStr = "";
   for (var ndx = 0; ndx < pyroNums.length; ndx++)
   {
      paramStr += (pyroNums[ndx] + ",");
   }
   paramStr = paramStr.replace(/,$/, '');

   xmlhttp.open("GET", "fetchPyro.php?pyroNum=" + paramStr, true);
   xmlhttp.send();
}

/**
   * Do the search given the search string entered in the
   *  sequence and values boxes/
   */
function doSearch()
{
   var seq = document.getElementById('searchSequence').value.
      replace(/[,\s]+/g, '').toUpperCase();
   var values = document.getElementById('searchValues').value.
      replace(/[,\s]+/g, ' ').replace(/^\s+$/, '').split(' ');

   var tolerence = document.getElementById('tolerence').value.
      replace(/\s+/g, '');

   if (!checkData(seq, values, tolerence))
   {
      return new Array();
   }

   //Display the loading image
   document.getElementById("loadingImage").style.visibility = "visible";
   
   var xmlhttp;
   if (window.XMLHttpRequest)
   {
      xmlhttp = new XMLHttpRequest();
   }
   else
   {//IE5, IE6
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
   }

   xmlhttp.onreadystatechange = function()
   {
      if (xmlhttp.readyState==4 && xmlhttp.status==200)
      {
         var data = JSON.parse(xmlhttp.responseText);

         var matching = new Array();
         var useDirect = document.getElementById("radio-direct").checked;
         var percentMap = {};

         for (var ndx in data)
         {
            var matchPercent;

            if (useDirect)
            {
               if (matchPercent = 
                  pyroDirectMatch(seq, values, data[ndx].seq.split(' '), 
                  data[ndx].peak_values.split(' '), tolerence))
               {
                  matching.push(data[ndx].pyroNum);
               }
            }
            else
            {
               if (matchPercent =
                  pyroPearsonMatch(seq, values, data[ndx].seq.split(' '), 
                  data[ndx].peak_values.split(' '), tolerence))
               {
                  matching.push(data[ndx].pyroNum);
               }
            }

            percentMap[data[ndx].pyroNum] = matchPercent;
         }
         
         //Clear out the table
         document.getElementById("pyros").innerHTML = "";

         insertPyros(matching, percentMap);
      }
   }

   xmlhttp.open("GET", "fetchValues.php?searchSeq=" + seq, true);
   xmlhttp.send();
}
