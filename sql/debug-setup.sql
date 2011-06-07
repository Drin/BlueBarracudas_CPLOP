use cplop;

CREATE TABLE IF NOT EXISTS ghost_data_point_debug(
   position INTEGER,
   pyrogram_num INTEGER,
   peak_value FLOAT,
   PRIMARY KEY(position, pyrogram_num));

CREATE TABLE IF NOT EXISTS ghost_pyrogram_debug(
   pyrogram_num INTEGER AUTO_INCREMENT,
   well_id VARCHAR(4),
   dispensation_sequence VARCHAR(128),
   PRIMARY KEY(pyrogram_num),
   UNIQUE(well_id));


CREATE TABLE IF NOT EXISTS ghost_pyro_stats_debug(
   pyrogram_num INT UNIQUE,
   mean FLOAT,
   std_dev FLOAT);

CREATE TABLE IF NOT EXISTS ghost_statements_debug(
   checkpoint VARCHAR(32));
