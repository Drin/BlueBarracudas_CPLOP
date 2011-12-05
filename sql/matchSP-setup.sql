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

CREATE PROCEDURE calculate(IN pyroID int(11))
COMMENT 'CALC mean and std_dev for the given pyroprint ID.'
BEGIN
   DECLARE mean, stdDev FLOAT;

   CALL calcMeanStdDev(pyroID, mean, stdDev);

   REPLACE INTO pyro_stats VALUES(pyroID, mean, stdDev);
END$$

CREATE PROCEDURE preprocessPyroprints()
COMMENT 'This calculates pyro_stats for each Pyroprint'
BEGIN
   DECLARE pyroNum int(11);
   DECLARE done INT DEFAULT 0;

   DECLARE pyroNums CURSOR FOR
      SELECT p.pyroID
      FROM Pyroprints p JOIN Histograms h using (pyroID)
      WHERE p.pyroID NOT IN (SELECT pyroID from pyro_stats);
   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

   OPEN pyroNums;

   pyroprintLoop: LOOP
      FETCH pyroNums INTO pyroNum;

      IF done THEN
         LEAVE pyroprintLoop;
      END IF;

      CALL calculate(pyroNum);

   END LOOP;

   CLOSE pyroNums;
END$$

CREATE PROCEDURE getStats(IN pyro1 INT, IN pyro2 INT,
 OUT mean1 FLOAT, OUT mean2 FLOAT, OUT stdDev1 FLOAT, OUT stdDev2 FLOAT)
BEGIN
   SELECT mean, std_dev INTO mean1, stdDev1 FROM pyro_stats WHERE pyroID = pyro1;
   SELECT mean, std_dev INTO mean2, stdDev2 FROM pyro_stats WHERE pyroID = pyro2;
END$$

CREATE FUNCTION calcPearson(pyro1 INT, pyro2 INT) 
RETURNS FLOAT
READS SQL DATA
BEGIN
   DECLARE same_dispensation INT DEFAULT 0;
   DECLARE same_fwPrimer INT DEFAULT 0;
   DECLARE same_revPrimer INT DEFAULT 0;
   DECLARE same_seqPrimer INT DEFAULT 0;

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

   SELECT a.dsName = b.dsName, a.forPrimer = b.forPrimer,
          a.revPrimer = b.revPrimer, a.seqPrimer = b.seqPrimer
   INTO same_dispensation, same_fwPrimer, same_revPrimer, same_seqPrimer
   FROM Pyroprints a, Pyroprints b
   WHERE a.pyroID = pyro1 AND b.pyroID = pyro2;

   IF same_dispensation = 0 OR same_fwPrimer = 0 OR
      same_revPrimer = 0 OR same_seqPrimer = 0
      THEN RETURN -2;
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

CREATE PROCEDURE computeAllSimilarities()
BEGIN
   DECLARE pyro1, pyro2 INT(11);
   DECLARE pearsonCorr FLOAT;
   DECLARE done INT DEFAULT 0;

   DECLARE pyroPairs CURSOR FOR
      SELECT a.pyroID, b.pyroID
      FROM pyro_stats a, pyro_stats b
      WHERE a.pyroID >= b.pyroID
      ORDER BY a.pyroID asc;
   DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

   OPEN pyroPairs;

   correlateLoop: LOOP
      FETCH pyroPairs into pyro1, pyro2;

      IF done = 1 THEN
         LEAVE correlateLoop;
      END IF;

      SET pearsonCorr = calcPearson(pyro1, pyro2);

      REPLACE INTO pyro_similarities VALUES(pyro1, pyro2, pearsonCorr);
   END LOOP;

END$$

CREATE PROCEDURE computeSimilarities()
BEGIN
   DECLARE pyro1, pyro2 INT(11);
   DECLARE pearsonCorr FLOAT;

   DECLARE pyroPairs CURSOR FOR
      SELECT pyroID
      FROM pyro_stats
      ORDER BY pyroID asc;

   SELECT max(pyroID) INTO pyro1
   FROM pyro_stats;

   OPEN pyroPairs;

   correlateLoop: LOOP
      FETCH pyroPairs INTO pyro2;

      IF pyro1 = pyro2 THEN
         REPLACE INTO pyro_similarities VALUES(pyro1, pyro2, 1);
         LEAVE correlateLoop;
      END IF;

      SET pearsonCorr = calcPearson(pyro1, pyro2);

      REPLACE INTO pyro_similarities VALUES(pyro1, pyro2, pearsonCorr);
   END LOOP;

END$$

/*
CREATE PROCEDURE peakCorrelations()
BEGIN
   DECLARE peakCorrelation FLOAT DEFAULT 0;

   DECLARE pyroPairs CURSOR FOR
      SELECT a.pyroID, b.pyroID
      FROM Pyroprints a, Pyroprints b
      WHERE a.pyroID != b.pyroID AND a.pyroID not in
END$$
*/

DELIMITER ;
