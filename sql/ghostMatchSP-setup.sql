DELIMITER $$

CREATE PROCEDURE calcGhostMeanStdDev(IN pyroNum INT, OUT mean FLOAT, OUT stdDev FLOAT)
BEGIN
   DECLARE count INT DEFAULT 0;
   DECLARE done INT DEFAULT 0;
   DECLARE val FLOAT;
   DECLARE runningMean, runningStdDev FLOAT DEFAULT 0;

   DECLARE pyroVals CURSOR FOR
      SELECT peak_value
      FROM ghost_data_point
      WHERE pyrogram_num = pyroNum;
   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;


   OPEN pyroVals;

   meanLoop: LOOP
      FETCH pyroVals INTO val;

      -- begin debug statements
      -- INSERT INTO ghost_statements_debug(checkpoint)
      -- VALUES('checking if done');
      -- end debug statements

      IF done THEN
         LEAVE meanLoop;
      END IF;

      -- begin debug statements
      -- INSERT INTO ghost_statements_debug(checkpoint)
      -- VALUES('calculating mean');
      -- end debug statements

      SET runningMean = runningMean + val;

      SET count = count + 1;

      -- begin debug statements
      -- INSERT IGNORE INTO ghost_data_point_debug(position, pyrogram_num, peak_value)
      -- VALUES (count, pyroNum, val);
      -- end debug statements


   END LOOP;

   SET mean = (runningMean / count);

   SET done = 0;
   CLOSE pyroVals;

   OPEN pyroVals;

   devLoop: LOOP
      FETCH pyroVals INTO val;

      IF done THEN
         LEAVE devLoop;
      END IF;

      -- begin debug statements
      -- INSERT INTO ghost_statements_debug(checkpoint)
      -- VALUES('calculating stdDev');
      -- end debug statements

      SET runningStdDev = runningStdDev + POW((mean - val), 2);
   END LOOP;
   
   CLOSE pyroVals;

   SET stdDev = SQRT(runningStdDev / count);

   -- begin debug statements
   -- INSERT IGNORE INTO ghost_pyro_stats_debug(pyrogram_num, mean, std_dev)
   -- VALUES (pyroNum, mean, std_dev);
   -- end debug statements

END$$

CREATE PROCEDURE ghost_calculate()
COMMENT 'CALC mean and std_dev for every pyro in the ghost xmlFile.'
BEGIN
   DECLARE num INT;
   DECLARE done INT DEFAULT 0;
   DECLARE mean, stdDev FLOAT;

   DECLARE pyroNum CURSOR FOR
      SELECT pyrogram_num 
      FROM ghost_pyrogram;
   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

   OPEN pyroNum;
   
   numLoop: LOOP
      FETCH pyroNum INTO num;

      IF done THEN
         LEAVE numLoop;
      END IF;

      -- begin debug statements
      -- INSERT INTO ghost_statements_debug(checkpoint)
      -- VALUES('calling MeanStdDev');
      -- end debug statements

      CALL calcGhostMeanStdDev(num, mean, stdDev);

      -- begin debug statements
      -- INSERT INTO ghost_statements_debug(checkpoint)
      -- VALUES('called MeanStdDev');
      -- INSERT INTO ghost_pyro_stats_debug(pyrogram_num, mean, std_dev)
      -- VALUES (num, mean, stdDev);
      -- end debug statements

      REPLACE INTO ghost_pyro_stats VALUES(num, mean, stdDev);
   END LOOP;

   CLOSE pyroNum;
END$$

CREATE PROCEDURE ghost_getStats(IN pyro1 INT, IN pyro2 INT,
 OUT mean1 FLOAT, OUT mean2 FLOAT, OUT stdDev1 FLOAT, OUT stdDev2 FLOAT)
BEGIN
   IF pyro1 > 0 THEN
      SELECT mean, std_dev INTO mean1, stdDev1 FROM pyro_stats WHERE pyrogram_num = pyro1;
   ELSE
      SELECT mean, std_dev INTO mean1, stdDev1 FROM ghost_pyro_stats WHERE pyrogram_num = pyro1;
   END IF;

   IF pyro2 > 0 THEN
      SELECT mean, std_dev INTO mean2, stdDev2 FROM pyro_stats WHERE pyrogram_num = pyro2;
   ELSE
      SELECT mean, std_dev INTO mean2, stdDev2 FROM ghost_pyro_stats WHERE pyrogram_num = pyro2;
   END IF;
END$$

CREATE FUNCTION ghost_pearsonMatch(pyro1 INT, pyro2 INT) 
RETURNS FLOAT
READS SQL DATA
BEGIN
   DECLARE same_dispensation INT DEFAULT 0;
   DECLARE done INT DEFAULT 0;
   DECLARE sum FLOAT DEFAULT 0;
   DECLARE aVal, bVal FLOAT DEFAULT 0;
   DECLARE mean1, mean2, stdDev1, stdDev2 FLOAT;
   DECLARE rtn FLOAT DEFAULT 0;
   DECLARE count INT DEFAULT 0;

   DECLARE pyroVals CURSOR FOR 
      -- depending on whether the pyroNum is positive or negative it is in a different table
      IF pyro1 > 0 AND pyro2 > 0 THEN
         SELECT a.peak_value, b.peak_value
         FROM pyrogram_data_point a, pyrogram_data_point b
         WHERE a.pyrogram_num = pyro1 AND b.pyrogram_num = pyro2 
          AND a.position = b.position
         ORDER BY a.position;

      ELSIF pyro1 > 0 AND pyro2 < 0 THEN
         SELECT a.peak_value, b.peak_value
         FROM pyrogram_data_point a, ghost_data_point b
         WHERE a.pyrogram_num = pyro1 AND b.pyrogram_num = pyro2 
          AND a.position = b.position
         ORDER BY a.position;

      ELSIF pyro1 < 0 AND pyro2 < 0 THEN
         SELECT a.peak_value, b.peak_value
         FROM ghost_data_point a, ghost_data_point b
         WHERE a.pyrogram_num = pyro1 AND b.pyrogram_num = pyro2 
          AND a.position = b.position
         ORDER BY a.position;

      ELSIF pyro1 < 0 AND pyro2 > 0 THEN
         SELECT a.peak_value, b.peak_value
         FROM ghost_data_point a, pyrogram_data_point b
         WHERE a.pyrogram_num = pyro1 AND b.pyrogram_num = pyro2 
          AND a.position = b.position
         ORDER BY a.position;

      END IF;
   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;


   IF pyro1 > 0 AND pyro2 > 0 THEN
      SELECT a.protocol = d.protocol INTO same_dispensation
      FROM pyrogram a, pyrogram b
      WHERE a.pyrogram_num = pyro1 AND b.pyrogram_num = pyro2;

   ELSIF pyro1 > 0 AND pyro2 < 0 THEN
      SELECT b.dispensation_sequence = d.dispensation_sequence INTO same_dispensation
      FROM ghost_pyrogram b, pyrogram a JOIN protocol p ON (b.protocol = p.name)
                             JOIN dispensation_sequence d ON (p.dispensation_id = d.dispensation_id)
      WHERE a.pyrogram_num = pyro1 AND b.pyrogram_num = pyro2;

   ELSIF pyro1 < 0 AND pyro2 < 0 THEN
      SELECT a.dispensation_sequence = b.dispensation_sequence INTO same_dispensation
      FROM ghost_pyrogram a, ghost_pyrogram b
      WHERE a.pyrogram_num = pyro1 AND b.pyrogram_num = pyro2;

   ELSIF pyro1 < 0 AND pyro2 > 0 THEN
      SELECT a.dispensation_sequence = d.dispensation_sequence INTO same_dispensation
      FROM ghost_pyrogram a, pyrogram b JOIN protocol p ON (b.protocol = p.name)
                             JOIN dispensation_sequence d ON (p.dispensation_id = d.dispensation_id)
      WHERE a.pyrogram_num = pyro1 AND b.pyrogram_num = pyro2;

   END IF;


   IF same_dispensation = 0 THEN
      RETURN -2;
   END IF;

   CALL ghost_getStats(pyro1, pyro2, mean1, mean2, stdDev1, stdDev2);
   OPEN pyroVals;

   countLoop: LOOP
      FETCH pyroVals INTO aVal, bVal;

      IF done THEN
         LEAVE countLoop;
      END IF;

      SET sum = sum + ((aVal - mean1) * (bVal - mean2));
      SET count = count + 1;
   END LOOP;

   SET rtn = sum / (count * stdDev1 * stdDev2); 

   CLOSE pyroVals;

   RETURN(rtn);
END$$

/*
CREATE PROCEDURE closestSpecies(IN pyroNum INT)
BEGIN
   DECLARE done INT DEFAULT 0;
   DECLARE currentSpecies VARCHAR(32);
   DECLARE maxMatch FLOAT;
   DECLARE tempMatch FLOAT;
   DECLARE cmpPyro INT;

   DECLARE species CURSOR FOR
      SELECT common_name 
      FROM host_species
      WHERE common_name != 'Environmental';

   DECLARE cmpPyros CURSOR FOR
      SELECT DISTINCT(pyrogram_num)
      FROM pyrogram JOIN isolate USING(isolate_name)
      WHERE host_species = currentSpecies;

   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

   CREATE TEMPORARY TABLE IF NOT EXISTS temp_closest_match(
      species VARCHAR(32) UNIQUE,
      val FLOAT);

   OPEN species;

   speciesLoop: LOOP
      FETCH species INTO currentSpecies;

      IF done THEN
         LEAVE speciesLoop;
      END IF;

      SET maxMatch = 0;

      OPEN cmpPyros;

      cmpLoop: LOOP
         FETCH cmpPyros INTO cmpPyro;

         IF done THEN
            SET done = 0;
            LEAVE cmpLoop;
         END IF;

         SET tempMatch = pearsonMatch(pyroNum, cmpPyro);

         IF tempMatch > maxMatch THEN
            SET maxMatch = tempMatch;
         END IF;
      END LOOP;

      close cmpPyros;

      REPLACE INTO temp_closest_match VALUES(currentSpecies, maxMatch);
   END LOOP;

   SELECT * FROM temp_closest_match ORDER BY val DESC;

   DROP TEMPORARY TABLE IF EXISTS temp_closest_match;
END$$
*/

DELIMITER ;
