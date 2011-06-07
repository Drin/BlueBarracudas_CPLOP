function toggleColor(tableRow, highLight)
{
   if (highLight)
   {
      tableRow.style.backgroundColor = '#dcfac9';
   }
   else
   {
      tableRow.style.backgroundColor = 'white';
   }
}

function goLink(link)
{
   document.location.href = link;
}

function linkPyro(pyrogram_num)
{
   var baseURL = _webBase_ + "/pyro/displayPyro.php";
   var pyroQuery = "?pyroNum=" + pyrogram_num;
   document.location.href = baseURL + pyroQuery;
}

function linkIsolate(isolate_name)
{
   //var baseURL = "http://cslvm97.csc.calpoly.edu/blueBarracudas/isolate/displayIsolate.php";
   var baseURL = _webBase_ + "/isolate/displayIsolate.php";
   var isolateQuery = "?isolateName=" + isolate_name;
   //alert(isolateQuery);
   document.location.href = baseURL + isolateQuery;
}

function removeSelection(multiSelectId)
{
   $('#' + multiSelectId + ' option:selected').remove();
}

function toggleMenu(menu, imgPath)
{
   ele = document.getElementById(menu);
   bar = document.getElementById(menu + "_bar");

   if (ele.style.display == "none")
   {
      ele.style.display = "block";
      bar.innerHTML = "<img src='" + imgPath + "/up.jpg'></img>";
   }
   else
   {
      ele.style.display = "none";
      bar.innerHTML = "<img src='" + imgPath + "/down.jpg'></img>";
   }
}

/**
  * Give the id of a form that you want to require input fields.
  * The required fields should have the title 'req'.
  * This one will not submit;
  */
function requireFieldsNoSubmit(formName)
{
   var fields = document.getElementById(formName).elements;

   var msg = "You must supply the following fields: ";
   var flag = false;

   for (i = 0; i < fields.length; i++)
   {
      if (fields[i].title == "req")
      {
         if (fields[i].value == "")
         {
            flag = true;
            msg += ("\n" + fields[i].name);
         }
      }
   }

   if (flag)
   {
      alert(msg);
   }

   return !flag;
}

/**
  * Give the id of a form that you want to require input fields.
  * The required fields should have the title 'req'.
  * This should probably by attatched to a button and the form should have no
  *  other submit.
  */
function requireFields(formName)
{
   if (!requireFieldsNoSubmit(formName))
   {
      alert(msg);
      return false;
   }
   else
   {
      document.getElementById(formName).submit();
      return true;
   }
}
