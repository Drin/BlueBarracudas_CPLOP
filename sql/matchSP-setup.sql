DELIMITER $$

CREATE PROCEDURE calcMeanStdDev(IN pyroNum INT, OUT mean FLOAT, OUT stdDev FLOAT)
BEGIN
   DECLARE count INT DEFAULT 0;
   DECLARE done INT DEFAULT 0;
   DECLARE val FLOAT;
   DECLARE runningMean, runningStdDev FLOAT DEFAULT 0;

   DECLARE pyroVals CURSOR FOR
      SELECT peak_value
      FROM pyrogram_data_point
      WHERE pyrogram_num = pyroNum;
   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;


   OPEN pyroVals;

   meanLoop: LOOP
      FETCH pyroVals INTO val;

      IF done THEN
         LEAVE meanLoop;
      END IF;

      SET runningMean = runningMean + val;

      SET count = count + 1;
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

      SET runningStdDev = runningStdDev + POW((mean - val), 2);
   END LOOP;
   
   CLOSE pyroVals;

   SET stdDev = SQRT(runningStdDev / count);
END$$

CREATE PROCEDURE calculate(IN xmlFile VARCHAR(32))
COMMENT 'CLAC mean and std_dev for every pyro assigned to the given xmlFile.'
BEGIN
   DECLARE num INT;
   DECLARE done INT DEFAULT 0;
   DECLARE mean, stdDev FLOAT;

   DECLARE pyroNum CURSOR FOR
      SELECT pyrogram_num 
      FROM pyrogram 
      WHERE xml_file = xmlFile;
   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

   OPEN pyroNum;
   
   numLoop: LOOP
      FETCH pyroNum INTO num;

      IF done THEN
         LEAVE numLoop;
      END IF;

      CALL calcMeanStdDev(num, mean, stdDev);

      REPLACE INTO pyro_stats VALUES(num, mean, stdDev);
   END LOOP;

   CLOSE pyroNum;
END$$

CREATE PROCEDURE getStats(IN pyro1 INT, IN pyro2 INT,
 OUT mean1 FLOAT, OUT mean2 FLOAT, OUT stdDev1 FLOAT, OUT stdDev2 FLOAT)
BEGIN
   SELECT mean, std_dev INTO mean1, stdDev1 FROM pyro_stats WHERE pyrogram_num = pyro1;
   SELECT mean, std_dev INTO mean2, stdDev2 FROM pyro_stats WHERE pyrogram_num = pyro2;
END$$

CREATE FUNCTION pearsonMatch(pyro1 INT, pyro2 INT) 
RETURNS FLOAT
READS SQL DATA
BEGIN
   DECLARE same_protocol INT DEFAULT 0;
   DECLARE done INT DEFAULT 0;
   DECLARE sum FLOAT DEFAULT 0;
   DECLARE aVal, bVal FLOAT DEFAULT 0;
   DECLARE mean1, mean2, stdDev1, stdDev2 FLOAT;
   DECLARE rtn FLOAT DEFAULT 0;
   DECLARE count INT DEFAULT 0;

   DECLARE pyroVals CURSOR FOR 
      SELECT a.peak_value, b.peak_value
      FROM pyrogram_data_point a, pyrogram_data_point b
      WHERE a.pyrogram_num = pyro1 AND b.pyrogram_num = pyro2 
       AND a.position = b.position
      ORDER BY a.position;
   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

   SELECT a.protocol = b.protocol INTO same_protocol
   FROM pyrogram a, pyrogram b
   WHERE a.pyrogram_num = pyro1 AND b.pyrogram_num = pyro2;

   IF same_protocol = 0 THEN
      RETURN -2;
   END IF;

   CALL getStats(pyro1, pyro2, mean1, mean2, stdDev1, stdDev2);
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

CREATE PROCEDURE closestSpecies(IN pyroNum INT)
BEGIN
   DECLARE done INT DEFAULT 0;
   DECLARE currentSpecies VARCHAR(32);
   DECLARE maxMatch FLOAT;
   DECLARE tempMatch FLOAT;
   DECLARE cmpPyro INT;
   DECLARE threshold FLOAT DEFAULT 0.997;
   DECLARE count INT DEFAULT 0;
   DECLARE maxPyro INT DEFAULT 0;

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
      val FLOAT,
      pyrogram_num INTEGER,
      numMatch INTEGER);

   OPEN species;

   speciesLoop: LOOP
      FETCH species INTO currentSpecies;

      IF done THEN
         LEAVE speciesLoop;
      END IF;

      SET maxMatch = 0;
      SET count = 0;
      SET maxPyro = 0;

      OPEN cmpPyros;

      cmpLoop: LOOP
         FETCH cmpPyros INTO cmpPyro;

         IF done THEN
            SET done = 0;
            LEAVE cmpLoop;
         END IF;

         SET tempMatch = pearsonMatch(pyroNum, cmpPyro);

         IF tempMatch > threshold THEN
            SET count = count + 1;
         END IF;

         IF tempMatch > maxMatch THEN
            SET maxMatch = tempMatch;
            SET maxPyro = cmpPyro;
         END IF;
      END LOOP;

      close cmpPyros;

      REPLACE INTO temp_closest_match VALUES(currentSpecies, maxMatch, maxPyro, count);
   END LOOP;

   SELECT * FROM temp_closest_match ORDER BY val DESC;

   DROP TEMPORARY TABLE IF EXISTS temp_closest_match;
END$$

DELIMITER ;
