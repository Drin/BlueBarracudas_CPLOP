<?php include("meta.php"); ?>
<?php $thisPage = "main"; ?>

<body>

   <?php include("header.php"); ?>

   <div id="comparisonTable">
      <table id="compTable" summary="distance results between pyrograms">
         <tr>
            <th> <!--this is the column where the pyrogram
               names for each row will go--></th>
            <th> Pyrogram 1 </th>
            <th> Pyrogram 2 </th>
            <th> Pyrogram 3 </th>
            <th> Pyrogram 4 </th>
            <th> Pyrogram 5 </th>
            <th> Pyrogram 6 </th>
            <th> Pyrogram 7 </th>
            <th> Pyrogram 8 </th>
         </tr>
         <tr>
            <td> <input class="selectOptions hidden" type="checkbox"> Pyrogram 1  </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
         </tr>
         <tr>
            <td> <input class="selectOptions hidden" type="checkbox"> Pyrogram 2  </td>
            <td> 100.00 </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
         </tr>
         <tr>
            <td> <input class="selectOptions hidden" type="checkbox"> Pyrogram 3  </td>
            <td> 83.12 </td>
            <td> 78.58 </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
         </tr>
         <tr>
            <td> <input class="selectOptions hidden" type="checkbox"> Pyrogram 4  </td>
            <td> 83.12 </td>
            <td> 78.58 </td>
            <td> 76.12 </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
         </tr>
         <tr>
            <td> <input class="selectOptions hidden" type="checkbox"> Pyrogram 5  </td>
            <td> 83.12 </td>
            <td> 78.58 </td>
            <td> 83.12 </td>
            <td> 78.58 </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
         </tr>
         <tr>
            <td> <input class="selectOptions hidden" type="checkbox"> Pyrogram 6  </td>
            <td> 83.12 </td>
            <td> 78.58 </td>
            <td> 78.58 </td>
            <td> 83.12 </td>
            <td> 78.58 </td>
            <td> </td>
            <td> </td>
            <td> </td>
         </tr>
         <tr>
            <td> <input class="selectOptions hidden" type="checkbox"> Pyrogram 7  </td>
            <td> 83.12 </td>
            <td> 78.58 </td>
            <td> 76.21 </td>
            <td> 35.11 </td>
            <td> 12.98 </td>
            <td> 99.87 </td>
            <td> </td>
         </tr>
         <tr>
            <td> <input class="selectOptions hidden" type="checkbox"> Pyrogram 8  </td>
            <td> 83.12 </td>
            <td> 78.58 </td>
            <td> 89.38 </td>
            <td> 54.54 </td>
            <td> 54.89 </td>
            <td> 98.53 </td>
            <td> 97.21 </td>
            <td>  </td>
         </tr>
      </table>
      <br/>
      <br/>
      <div id="tableOptions">
         <input id="okayButton" class="controls hidden" type="button" value="Okay">
         <input id="cancelButton" class="controls hidden" type="button" value="Cancel">
         <br/>

         <input id="highlightButton" class="options" type="button" value="Highlight">
         <input id="selectionButton" class="options" type="button" value="Selection">

         <div id="highlightArea" class="hidden">
            <input id="addHighlight" class="highlightSettings" type="button" value="Add Highlight">

            <div id="highlightList">
            </div>
         </div>
      </div>
   </div>

   </body>
</html>
