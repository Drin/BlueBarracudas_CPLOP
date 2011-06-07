<div id="topmenu">
   <ul>
      <li>
         <a <?php if ($thisPage == "main") echo "class=\"active\"" ?> 
          href=<?php echo "\"$webBase/\"" ?>>Main</a>
      </li>

      <li>
         <a <?php if ($thisPage == "browsePyro") echo "class=\"active\"" ?> 
          href=<?php echo "\"$webBase/browsePyro.php\"" ?>>Browse Pyrograms</a>
      </li>

      <li>
         <a <?php if ($thisPage == "browseStock") echo "class=\"active\"" ?> 
          href=<?php echo "\"$webBase/browseStock.php\"" ?>>Browse Freezer Stock</a>

      </li>
      <li>
         <a <?php if ($thisPage == "upload") echo "class=\"active\"" ?> 
          href=<?php echo "\"$webBase/upload.php\"" ?>>Upload</a>
      </li>

      <li>
         <a <?php if ($thisPage == "match") echo "class=\"active\"" ?> 
          href=<?php echo "\"$webBase/match/match.php\"" ?>>Match</a>
      </li>

      <li>
         <a <?php if ($thisPage == "search") echo "class=\"active\"" ?> 
          href=<?php echo "\"$webBase/search/search.php\"" ?>>Search</a>
      </li>

      <li>
         <a <?php if ($thisPage == "browseProtocol") echo "class=\"active\"" ?> 
          href=<?php echo "\"$webBase/browseProtocol.php\"" ?>>Browse Protocol</a>
      </li>
   </ul>
</div>

