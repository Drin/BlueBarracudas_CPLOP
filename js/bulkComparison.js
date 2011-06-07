$(document).ready(function() {
   ControlButtonHandlers();
   OptionButtonHandlers();
   HighlightButtonHandler();
});

function ControlButtonHandlers() {
   $('#okayButton').click(function() {
      $('.controls').addClass('hidden').removeClass('shown');
      $('.options').removeClass('hidden').addClass('shown');
      $('#compTable .selectOptions').addClass('hidden').removeClass('shown');
      $('#highlightArea').addClass('hidden').removeClass('shown');
      highlightCells();
      createSubView();
   });

   $('#cancelButton').click(function() {
      $('.controls').addClass('hidden').removeClass('shown');
      $('.options').removeClass('hidden').addClass('shown');
      $('#compTable .selectOptions').addClass('hidden').removeClass('shown');
      $('#highlightArea').addClass('hidden').removeClass('shown');
   });
}

function OptionButtonHandlers() {
   $('#selectionButton').click(function() {
      $('#compTable .selectOptions').removeClass('hidden').addClass('shown');
      $('.options').addClass('hidden').removeClass('shown');
      $('.controls').removeClass('hidden').addClass('shown');
   });

   $('#highlightButton').click(function() {
      $('#highlightArea').removeClass('hidden').addClass('shown');
      $('.options').addClass('hidden').removeClass('shown');
      $('.controls').removeClass('hidden').addClass('shown');
   });
}

function HighlightButtonHandler() {
   var settingsId = 0;
   $('#addHighlight').click(function() {
      var deleteButton = '<div id="highlightSetting' + settingsId + '">\n' +
         '<input class="highlightSettings" type="button" value="X" onclick="removeSetting(' + (settingsId++) + ')">';
      var highlightRange = '<input class="highlightSettings" type="text" value="99.7">';
      var highlightColor = '<input class="highlightSettings" type="text" value="red">\n</div>';

      $('#highlightList').append(deleteButton + highlightRange + highlightColor);
   });
}

function removeSetting(settingId) {
   $('#highlightSetting' + settingId).remove();
}

function highlightCells() {
   $('#compTable td').each(function() {
      //if (
   });
}

function createSubView() {
}
