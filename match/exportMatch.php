<?php
   header('Content-disposition: attachment; filename=matchData.csv');
   header('Content-type: text/csv');
   readfile('../tmp/match/matchData.csv');
?>
