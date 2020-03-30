CREATE DATABASE numrand;

USE numrand;

CREATE TABLE `cylinder` (
  `cylinder_index` text,
  `state_id` int(11) DEFAULT NULL,
  `phase_offset` text,
  `speed` text
);

CREATE TABLE `result_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) DEFAULT NULL,
  `max` int(11) DEFAULT NULL,
  `ratio` text,
  PRIMARY KEY (`id`)
);

CREATE TABLE `state` (
  `particle_x` float DEFAULT NULL,
  `particle_y` float DEFAULT NULL,
  `particle_direction` float DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
);