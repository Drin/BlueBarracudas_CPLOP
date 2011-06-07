CREATE TABLE IF NOT EXISTS host_species(
   latin_name VARCHAR(64) UNIQUE,
   common_name VARCHAR(32) NOT NULL UNIQUE PRIMARY KEY,
   tld VARCHAR(2) NOT NULL UNIQUE);

CREATE TABLE IF NOT EXISTS host(
   host_name VARCHAR(32) NOT NULL,
   host_species VARCHAR(32) NOT NULL, 
   FOREIGN KEY(host_species) REFERENCES host_species(common_name),
   PRIMARY KEY(host_name, host_species));

CREATE TABLE IF NOT EXISTS sample(
   sample_id VARCHAR(32) NOT NULL,
   sample_date DATE NOT NULL,
   sample_location VARCHAR(32),
   host_name VARCHAR(32) NOT NULL,
   host_species VARCHAR(32) NOT NULL,
   FOREIGN KEY(host_species) REFERENCES host_species(common_name),
   FOREIGN KEY(host_name) REFERENCES host(host_name),
   PRIMARY KEY(sample_id, host_name, host_species));

CREATE TABLE IF NOT EXISTS isolate(
   isolate_name VARCHAR(32) PRIMARY KEY,
   freezer_location VARCHAR(32),
   freezer_num INTEGER,
   box_number INTEGER,
   box_position VARCHAR(32),
   freezing_date DATE NOT NULL,
   isolate_technician VARCHAR(64),
   is_pyroprinted TINYINT NOT NULL,
   host_name VARCHAR(32) NOT NULL,
   host_species VARCHAR(32) NOT NULL,
   sample_id VARCHAR(32) NOT NULL,
   FOREIGN KEY(host_species) REFERENCES host_species(common_name),
   FOREIGN KEY(host_name, sample_id) REFERENCES sample(host_name, sample_id),
   FOREIGN KEY(host_name) REFERENCES host(host_name));

CREATE TABLE IF NOT EXISTS dispensation_sequence(
   dispensation_id INTEGER PRIMARY KEY AUTO_INCREMENT,
   dispensation_sequence VARCHAR(128) UNIQUE,
   dispensation_name VARCHAR(32) UNIQUE);

CREATE TABLE IF NOT EXISTS primer(
   primer_id INTEGER PRIMARY KEY AUTO_INCREMENT,
   sequence VARCHAR(32) NOT NULL UNIQUE,
   sequence_name VARCHAR(32) NOT NULL UNIQUE);

CREATE TABLE IF NOT EXISTS protocol(
   name varchar(32) NOT NULL PRIMARY KEY,
   dispensation_id INTEGER NOT NULL,
   forward_primer_id INTEGER NOT NULL,
   reverse_primer_id INTEGER NOT NULL,
   sequence_primer_id INTEGER NOT NULL,
   region_name VARCHAR(32) NOT NULL,
   FOREIGN KEY (dispensation_id) REFERENCES dispensation_sequence(dispensation_id),
   FOREIGN KEY (forward_primer_id) REFERENCES primer(primer_id),
   FOREIGN KEY (reverse_primer_id) REFERENCES primer(primer_id),
   FOREIGN KEY (sequence_primer_id) REFERENCES primer(primer_id),
   UNIQUE(dispensation_id, forward_primer_id, reverse_primer_id, sequence_primer_id, region_name));

CREATE TABLE IF NOT EXISTS pyrogram(
   pyrogram_num INTEGER NOT NULL AUTO_INCREMENT,
   well_id VARCHAR(4) NOT NULL,
   pyrogram_date DATE NOT NULL,
   machine_id INTEGER,
   pcr_date DATE,
   pcr_machine VARCHAR(23),
   isolate_name VARCHAR(32) NOT NULL,
   pyroprint_technician VARCHAR(64),
   xml_file VARCHAR(32) NOT NULL,
   quality_control TINYINT NOT NULL DEFAULT TRUE,
   protocol VARCHAR(32) NOT NULL,
   FOREIGN KEY(protocol) REFERENCES protocol(name),
   FOREIGN KEY(isolate_name) REFERENCES isolate(isolate_name),
   PRIMARY KEY(pyrogram_num),
   UNIQUE(xml_file, well_id));

CREATE TABLE IF NOT EXISTS pyrogram_data_point(
   position INTEGER NOT NULL,
   pyrogram_num INTEGER NOT NULL,
   peak_value FLOAT NOT NULL,
   compensated_peak_value FLOAT,
   nucleotide VARCHAR(1) NOT NULL,
   CONSTRAINT pyro_id FOREIGN KEY (pyrogram_num) REFERENCES pyrogram(pyrogram_num),
   PRIMARY KEY(position, pyrogram_num));

CREATE TABLE IF NOT EXISTS compensation_slope(
   `level` INTEGER NOT NULL,
   drop_off_value FLOAT NOT NULL,
   position INTEGER NOT NULL,
   pyrogram_num INTEGER NOT NULL,
   CONSTRAINT pyro_data_id FOREIGN KEY (position, pyrogram_num) REFERENCES pyrogram_data_point(position, pyrogram_num),
   FOREIGN KEY (pyrogram_num) REFERENCES pyrogram(pyrogram_num),
   PRIMARY KEY(`level`, position, pyrogram_num));

CREATE TABLE IF NOT EXISTS pyro_stats(
   pyrogram_num INT NOT NULL UNIQUE,
   mean FLOAT NOT NULL,
   std_dev FLOAT NOT NULL,
   FOREIGN KEY(pyrogram_num) REFERENCES pyrogram(pyrogram_num));

-- Extra tables for ghost matching. Should prevent unnecessary collisions

CREATE TABLE IF NOT EXISTS ghost_pyrogram(
   pyrogram_num INTEGER NOT NULL AUTO_INCREMENT,
   well_id VARCHAR(4) NOT NULL,
   dispensation_sequence VARCHAR(128) NOT NULL,
   PRIMARY KEY(pyrogram_num),
   UNIQUE(pyrogram_num, well_id));

CREATE TABLE IF NOT EXISTS ghost_data_point(
   position INTEGER NOT NULL,
   pyrogram_num INTEGER NOT NULL,
   peak_value FLOAT NOT NULL,
   compensated_peak_value FLOAT,
   nucleotide VARCHAR(1) NOT NULL,
   CONSTRAINT pyro_id FOREIGN KEY (pyrogram_num) REFERENCES ghost_pyrogram(pyrogram_num),
   PRIMARY KEY(position, pyrogram_num));

-- CREATE TABLE IF NOT EXISTS ghost_compensation_slope(
   -- `level` INTEGER NOT NULL,
   -- drop_off_value FLOAT NOT NULL,
   -- position INTEGER NOT NULL,
   -- pyrogram_num INTEGER NOT NULL,
   -- CONSTRAINT pyro_data_id FOREIGN KEY (position, pyrogram_num) REFERENCES pyrogram_data_point(position, pyrogram_num),
   -- FOREIGN KEY (pyrogram_num) REFERENCES pyrogram(pyrogram_num),
   -- PRIMARY KEY(`level`, position, pyrogram_num));

CREATE TABLE IF NOT EXISTS ghost_pyro_stats(
   pyrogram_num INT NOT NULL UNIQUE,
   mean FLOAT NOT NULL,
   std_dev FLOAT NOT NULL,
   FOREIGN KEY(pyrogram_num) REFERENCES ghost_pyrogram(pyrogram_num));
