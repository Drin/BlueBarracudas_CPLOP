----------------------------------------
--     Primary tables for schema      --
--  these are for the instantiated db --
----------------------------------------

CREATE TABLE IF NOT EXISTS pyro_stats(
   pyroID INT NOT NULL UNIQUE,
   mean FLOAT NOT NULL,
   std_dev FLOAT NOT NULL,
   FOREIGN KEY(pyroID) REFERENCES Pyropints(pyroID));

CREATE TABLE IF NOT EXISTS pyro_similarities(
   pyroID1 INT NOT NULL,
   pyroID2 INT NOT NULL,
   pearson FLOAT,
   PRIMARY KEY(pyroID1, pyroID2),
   FOREIGN KEY(pyroID1) REFERENCES Pyroprints(pyroID),
   FOREIGN KEY(pyroID2) REFERENCES Pyroprints(pyroID));

CREATE TABLE IF NOT EXISTS region_thresholds (
   region VARCHAR(20) PRIMARY KEY,
   alphaThreshold FLOAT DEFAULT .997,
   betaThreshold FLOAT DEFAULT .95);

----------------------------------------
-- TRIGGERS for including appropriate --
--  meta data to speed up clustering  --
----------------------------------------

DELIMITER $$
CREATE TRIGGER preprocessPyroprint
AFTER INSERT ON Histograms FOR EACH ROW
BEGIN
   CALL preprocessPyroprints();
END$$

CREATE TRIGGER pyroCorrelations AFTER INSERT
ON pyro_stats FOR EACH ROW
BEGIN
   CALL computeSimilarities();
END$$
DELIMITER ;

