DELIMITER $$

CREATE PROCEDURE calcMeanStdDev(IN pyroNum INT, OUT mean FLOAT, OUT stdDev FLOAT)
BEGIN
   DECLARE count INT DEFAULT 0;
   DECLARE done INT DEFAULT 0;
   DECLARE val FLOAT;
   DECLARE runningMean, runningStdDev FLOAT DEFAULT 0;

   DECLARE pyroVals CURSOR FOR
      SELECT SignalValue
      FROM Histograms
      WHERE pyroID = pyroNum;
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

CREATE PROCEDURE calculate(IN xmlFile VARCHAR(150))
COMMENT 'CALC mean and std_dev for every pyro assigned to the given xmlFile.'
BEGIN
   DECLARE num INT;
   DECLARE done INT DEFAULT 0;
   DECLARE mean, stdDev FLOAT;

   DECLARE pyroNum CURSOR FOR
      SELECT pyroID
      FROM Pyroprints p JOIN (SELECT DISTINCT pyroID FROM Histograms) h USING(pyroID)
      WHERE fileName = xmlFile;
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

CREATE PROCEDURE calcAllFiles()
COMMENT 'This calculates pyro_stats for each distinct xml file in Pyroprints'
BEGIN
   DECLARE xmlFile varchar(150);
   DECLARE done INT DEFAULT 0;

   DECLARE pyroFile CURSOR FOR
      SELECT distinct fileName
      FROM Pyroprints;
   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

   OPEN pyroFile;

   fileLoop: LOOP
      FETCH pyroFile INTO xmlFile;

      IF done THEN
         LEAVE fileLoop;
      END IF;

      CALL calculate(xmlFile);

   END LOOP;

   CLOSE pyroFile;
END
$$

CREATE PROCEDURE getStats(IN pyro1 INT, IN pyro2 INT,
 OUT mean1 FLOAT, OUT mean2 FLOAT, OUT stdDev1 FLOAT, OUT stdDev2 FLOAT)
BEGIN
   SELECT mean, std_dev INTO mean1, stdDev1 FROM pyro_stats WHERE pyroID = pyro1;
   SELECT mean, std_dev INTO mean2, stdDev2 FROM pyro_stats WHERE pyroID = pyro2;
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
      SELECT a.SignalValue, b.SignalValue
      FROM Histograms a, Histograms b
      WHERE a.pyroID = pyro1 AND b.pyroID = pyro2 
       AND a.position = b.position
      ORDER BY a.position;
   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

   SELECT a.protocol = b.protocol INTO same_protocol
   FROM Pyroprints a, Pyroprints b
   WHERE a.pyroID = pyro1 AND b.pyroID = pyro2;

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

DELIMITER ;
