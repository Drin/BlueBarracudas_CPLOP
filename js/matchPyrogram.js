function clickedHeader(objID) {
   var inputSelector = '#' + objID + ' input';
   var pSelector = '#' + objID + ' p';

   $(inputSelector).removeClass('hidden');
   $(pSelector).addClass('hidden');
   $(inputSelector).focus();

   $(inputSelector).blur(function() {
      var textFilter = $(inputSelector).val();

      $(inputSelector).addClass('hidden');
      $(pSelector).removeClass('hidden');

      /*
      $.each($('#matchResults #matchingPyroprints tr td:first'), function(ndx, tableRow) {
         alert($(this).toSource());
         //$.each($(tableRow
         //if (tableRow.
      });
      */
   });

   $(inputSelector).keyup(function(keyEvent) {
      if (keyEvent.keyCode == '13') {
         $(pSelector).text($(inputSelector).val());
         $(inputSelector).blur();
      }
   });
}
