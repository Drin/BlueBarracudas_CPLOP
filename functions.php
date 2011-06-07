<?php
function addDropDown($name)
{
   echo "<select>\n";
   echo "   <option value=\'\'></option>\n";
   echo "   <option value=\'".$name."1\'>".$name."1</option>\n";
   echo "   <option value=\'".$name."2\'>".$name."2</option>\n";
   echo "</select>\n";
}

function addLogicDropDown()
{
   echo "<select>\n";
   echo "   <option value=\'\'>contains</option>\n";
   echo "   <option value=\'invert\'>does not contain</option>\n";
   echo "</select>\n";
}

function addDropDownMultiple($name)
{
   echo "<select multiple=\'multiple\'>\n";
   echo "   <option value=\'\'></option>\n";
   echo "   <option value=\'".$name."1\'>".$name."1</option>\n";
   echo "   <option value=\'".$name."2\'>".$name."2</option>\n";
   echo "</select>\n";
}

function buildTableFilters($arr)
{
   foreach ($arr as $ele)
   {
      echo "<tr><td>\n";
      echo "   <input type='checkbox'>Invert Match</input>\n";
      echo "</td><td>\n";
      echo "   $ele: <input type='text' name='$ele' />\n";
      echo "</td><td>\n";
      echo "   <select></select>\n";
      echo "</td></tr>\n\n";
   }
}

?>
