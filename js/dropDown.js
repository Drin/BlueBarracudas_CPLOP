function fillDropDowns(basePath)
{
   basePath = basePath ? basePath : '.';

   $('.database-fill').each(function()
   {
      var values = $(this).attr('query').split(/\./);
      var defaultVal = $(this).attr('defaultVal') ? $(this).attr('defaultVal') : '';
      var defaultTag = $(this).attr('defaultTag') ? $(this).attr('defaultTag') : '';
      var id = $(this).attr('id');
   
      jQuery.get(basePath + "/generalFetch.php", 
       ("table=" + values[0] + "&attr=" + values[1]),
       function(data) 
       { 
         var vals = JSON.parse(data);

         var str = "<option value='" + defaultVal + "'>" + defaultTag + "</option>";

         for (var ndx in vals)
         {
            str += "<option value='" + vals[ndx][values[1]] + "'>" +
               vals[ndx][values[1]] + "</option>";
         }

         document.getElementById(id).innerHTML = str;
       });
   });
   
   $('.database-fill-pyro').each(function()
   {
      var id = $(this).attr('id');
      var defaultVal = $(this).attr('defaultVal') ? $(this).attr('defaultVal') : '';
      var defaultTag = $(this).attr('defaultTag') ? $(this).attr('defaultTag') : '';
   
      //jQuery.get("pyroNameFetch.php", "",
      jQuery.get(basePath + "/match/pyroNameFetch.php", "",
       function(data) 
       { 
         var vals = JSON.parse(data);

         var str = "<option value='" + defaultVal + "'>" + defaultTag + "</option>";

         for (var ndx in vals)
         {
            str += "<option value='" + vals[ndx]["pyrogram_num"] + "'>" +
               vals[ndx]["pyroName"] + "</option>";
         }

         document.getElementById(id).innerHTML = str;
       });
   });
}

function appendDropSelect(source, target)
{
   var source = document.getElementById(source);
   var target = document.getElementById(target);
   var selectNdx = source.selectedIndex;

   target.innerHTML = target.innerHTML + 
    "<option value='" + source.value + "'>" + source.options[selectNdx].text + "</option>";

   source.options[selectNdx].selected = false;
}

/*
function fillDropSelect(sourceData, target)
{
   var target = document.getElementById(target);
}
*/

function fillDropText(source, target)
{
   var source = document.getElementById(source);
   var target = document.getElementById(target);

   target.value = source.value;
}

function addDropText(source, target)
{
   var source = document.getElementById(source);
   var target = document.getElementById(target);

   if (target.value == '') {
      target.value = source.value;
   }
   else {
      target.value += ", " + source.value;
   }
}
