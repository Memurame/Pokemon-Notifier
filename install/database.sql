CREATE TABLE `chats` (
  `chat_id` varchar(50) CHARACTER SET utf8 NOT NULL,
  `admin` int(11) DEFAULT '0',
  `place` varchar(45) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `notify_iv` (
  `chat_id` varchar(50) NOT NULL,
  `iv_val` int(11) DEFAULT NULL,
  `pokemon_id` int(11) NOT NULL,
  PRIMARY KEY (`chat_id`,`pokemon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `notify_pokemon` (
  `chat_id` varchar(30) CHARACTER SET utf8 NOT NULL,
  `pokemon_id` int(11) NOT NULL,
  PRIMARY KEY (`chat_id`,`pokemon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `pokemon` (
  `pokemon_id` int(11) DEFAULT NULL,
  `encounter_id` varchar(250) DEFAULT NULL,
  `disappear_time` varchar(100) DEFAULT NULL,
  `geo_lat` float DEFAULT NULL,
  `geo_lng` float DEFAULT NULL,
  `spawnpoint_id` varchar(50) DEFAULT NULL,
  `place` varchar(45) DEFAULT NULL,
  `iv_attack` int(11) DEFAULT NULL,
  `iv_defense` int(11) DEFAULT NULL,
  `iv_stamina` int(11) DEFAULT NULL,
  `iv_result` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8
