CREATE TABLE IF NOT EXISTS `pokemon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pokemon_id` int(11) DEFAULT NULL,
  `encounter_id` varchar(250) DEFAULT NULL,
  `disappear_time` varchar(100) DEFAULT NULL,
  `geo_lat` float DEFAULT NULL,
  `geo_lng` float DEFAULT NULL,
  `spawnpoint_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;