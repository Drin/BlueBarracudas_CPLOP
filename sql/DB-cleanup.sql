SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS host_species CASCADE;
DROP TABLE IF EXISTS `host` CASCADE;
DROP TABLE IF EXISTS sample CASCADE;
DROP TABLE IF EXISTS isolate CASCADE;
DROP TABLE IF EXISTS pyrogram CASCADE;
DROP TABLE IF EXISTS pyrogram_data_point CASCADE;
DROP TABLE IF EXISTS compensation_slope CASCADE;
DROP TABLE IF EXISTS protocol CASCADE;
DROP TABLE IF EXISTS dispensation_sequence CASCADE;
DROP TABLE IF EXISTS primer CASCADE;

-- These will drop the tables used for ghost matching
DROP TABLE IF EXISTS ghost_pyrogram CASCADE;
DROP TABLE IF EXISTS ghost_data_point CASCADE;
DROP TABLE IF EXISTS ghost_pyro_stats CASCADE;

SET FOREIGN_KEY_CHECKS = 1;
