var A_COLOR = '#00CC00'; //GREEN
var C_COLOR = '#0000FF'; //BLUE
var G_COLOR = '#000000'; //BLACK
var T_COLOR = '#FF0000'; //RED
var ERROR_COLOR = '#FFFF00'; //YELLOW

function findClosestSpecies(pyrogramNum, tableDiv)
{
   $.get('../match/closestMatch.php?pyroNum=' + pyrogramNum,
    function(data)
    {
      var matches = JSON.parse(data);
      
      var table = '<table cellPadding=5 border=1><th>Species</th><th>Match %</th><th>Matches above 99.7%</th></tr>';

      for (var ndx in matches)
      {
         if (matches[ndx]['val'] != '0')
         {
            table += ("<tr onClick='linkPyro(" + matches[ndx]['pyrogram_num'] + ");'><td>" + matches[ndx]['species'] + "</td><td>" + 
             (parseFloat(matches[ndx]['val']) * 100).toFixed(4) + "</td><td>" +
             matches[ndx]['numMatch'] + "</td></a></tr>");
         }
      }

      table += '</table>';

      document.getElementById(tableDiv).innerHTML = table;
    });
}

/**
   * Take the given array of nucleotides and insert a second level 
   *  number under the nucleotide.
   *
   * @param nucleotides The nucleotides.
   * @param start The number to start on.
   * @param interval The interval between sequences.
   */
function insertNumericPositions(nucleotides, start, interval)
{
   var count = 0;

   for (ndx in nucleotides)
   {
      if (!(count % interval))
      {
         nucleotides[ndx] = nucleotides[ndx] + "<br/>" + (count + 1);
      }

      count++;
   }
}

/**
   * Take the given nucleotides and peak values and make a graph.
   *
   * @param nucleotides The nucleotide sequence.
   * @param values The peak values.
   *
   * @pre nucleotides and values should be the same length.
   */
function makeGraph(nucleotides, values, chartContainerName, title, max)
{
   var colors = getColors(nucleotides);
   insertNumericPositions(nucleotides, 1, 10);

   var myChart = new Highcharts.Chart(
   {
      chart: 
      {
         renderTo: chartContainerName,
         defaultSeriesType: 'column',
         zoomType: 'x'
      },
      credits:
      {
         enabled: false
      },
      colors: colors,
      title: 
      {
         text: title
      },
      xAxis: 
      {
         categories: nucleotides
      },
      yAxis: 
      {
         min: 0,
         max: max,
         title: 
         {
            text: 'Peak Value'
         }
      },
      tooltip:
      {
         formatter: function()
         {
            return '' + this.x + ": " + this.y;
         }
      },
      plotOptions:
      {
         column:
         {
            colorByPoint: true,
            pointPadding: 0.1,
            borderWidth: 0
         }
      },
      legend:
      {
         enabled: false
      },
      series: 
      [{
         data: values
      }]
   });

   return myChart;
}

/**
   * Using the given nucleotides, make the list of proper colors.
   * As per some bio-standards:
   *  A -> Green
   *  C -> Blue
   *  T -> Black
   *  G -> Red
   *
   * @param nucleotides The sequence. 
   *  Each value should be 'A', 'T', 'G', or 'C'.
   *  If a nucleotide is something else, an error color (yellow)
   *  will be inserted.
   *
   * @return The array of colors representing the nucleotides.
   */
function getColors(nucleotides)
{
   //Array needs to be primed with a color becuse it uses
   // 1-index.
   var colors = new Array(ERROR_COLOR);

   for (var i = 0; i < nucleotides.length; i++)
   {
      if (nucleotides[i] == 'A')
      {
         colors.push(A_COLOR);
      }
      else if (nucleotides[i] == 'T')
      {
         colors.push(T_COLOR);
      }
      else if (nucleotides[i] == 'C')
      {
         colors.push(C_COLOR);
      }
      else if (nucleotides[i] == 'G')
      {
         colors.push(G_COLOR);
      }
      else 
      {
         colors.push(ERROR_COLOR);
      }
   }

   return colors;
}
